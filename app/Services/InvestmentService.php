<?php

namespace App\Services;

use App\Models\Business;
use App\Models\InstallmentPayment;
use App\Models\Investment;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class InvestmentService
{
    public function __construct(private MidtransService $midtransService)
    {
    }

    /**
     * Create a new investment
     *
     * @param User $user
     * @param array $data
     * @return array{investment: Investment, transaction: Transaction}
     * @throws Exception
     */
    public function createInvestment(User $user, array $data): array
    {
        $business = Business::findOrFail($data['business_id']);

        if (!$business->isOpen()) {
            throw new Exception('Business is not open for investment.');
        }

        $paymentType = $data['payment_type'];
        $paymentMethod = $data['payment_method'] ?? 'manual_transfer';
        $adminFeePercentage = (float) SystemSetting::get('investment_admin_fee_percentage', 1);

        if ($paymentType === 'full') {
            $totalAmount = (float) SystemSetting::get('investment_full_amount', 1500000);
            $tenureMonths = null;
        } else {
            $monthlyAmount = (float) SystemSetting::get('investment_installment_monthly_amount', 125000);
            $tenureMonths = (int) ($data['tenure_months'] ?? 12);
            $maxTenure = (int) SystemSetting::get('investment_max_tenure_months', 12);

            if ($tenureMonths > $maxTenure) {
                throw new Exception("Maximum tenure is {$maxTenure} months.");
            }

            $totalAmount = $monthlyAmount * $tenureMonths;
        }

        $adminFee = round($totalAmount * ($adminFeePercentage / 100), 2);

        return DB::transaction(function () use (
            $user, $business, $paymentType, $paymentMethod,
            $totalAmount, $adminFee, $tenureMonths, $data
        ) {
            $investment = Investment::create([
                'user_id' => $user->id,
                'business_id' => $business->id,
                'payment_type' => $paymentType,
                'total_amount' => $totalAmount,
                'admin_fee' => $adminFee,
                'tenure_months' => $tenureMonths,
                'months_paid' => 0,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
            ]);

            if ($paymentType === 'installment') {
                $this->createInstallmentSchedule($investment);
            }

            $transaction = $this->createInvestmentTransaction($investment, $user, $paymentMethod);

            return compact('investment', 'transaction');
        });
    }

    /**
     * Create installment payment schedule
     */
    private function createInstallmentSchedule(Investment $investment): void
    {
        $monthlyAmount = (float) SystemSetting::get('investment_installment_monthly_amount', 125000);
        $adminFeePercentage = (float) SystemSetting::get('investment_admin_fee_percentage', 1);
        $adminFeePerMonth = round($monthlyAmount * ($adminFeePercentage / 100), 2);

        for ($month = 1; $month <= $investment->tenure_months; $month++) {
            InstallmentPayment::create([
                'investment_id' => $investment->id,
                'month_number' => $month,
                'amount' => $monthlyAmount,
                'admin_fee' => $adminFeePerMonth,
                'status' => 'pending',
                'due_date' => Carbon::now()->addMonths($month)->startOfMonth(),
            ]);
        }
    }

    /**
     * Create investment transaction record
     */
    private function createInvestmentTransaction(
        Investment $investment,
        User $user,
        string $paymentMethod
    ): Transaction {
        $type = $investment->isInstallment() ? 'installment' : 'investment';
        $amount = $investment->isInstallment()
            ? (float) SystemSetting::get('investment_installment_monthly_amount', 125000)
            : $investment->total_amount;

        $transactionData = [
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'status' => 'pending',
            'reference_id' => (string) $investment->id,
            'payment_method' => $paymentMethod,
        ];

        if ($paymentMethod === 'qris') {
            $orderId = 'INV-' . $investment->id . '-' . time();
            try {
                $qrisResult = $this->midtransService->createQrisCharge(
                    $orderId,
                    (int) ($amount + $investment->admin_fee),
                    ['first_name' => $user->name, 'email' => $user->email]
                );
                $transactionData['midtrans_order_id'] = $orderId;
                $transactionData['midtrans_transaction_id'] = $qrisResult['transaction_id'];
                $transactionData['midtrans_qr_code_url'] = $qrisResult['qr_code_url'];
                $transactionData['midtrans_deeplink_url'] = $qrisResult['deeplink_url'];
            } catch (Exception $e) {
                // Log error but don't fail — admin can retry
            }
        }

        return Transaction::create($transactionData);
    }

    /**
     * Check if business can be activated, activate if conditions met
     */
    public function checkAndActivateBusiness(Business $business): bool
    {
        $minInvestors = (int) SystemSetting::get('minimum_investors_to_activate', 5);
        $activeCount = $business->activeInvestments()->count();

        if ($activeCount >= $minInvestors && $business->status === 'open') {
            $business->update([
                'status' => 'active',
                'current_investors' => $activeCount,
                'activation_date' => now()->toDateString(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Activate an investment after payment confirmed
     */
    public function activateInvestment(Investment $investment): void
    {
        $investment->update(['status' => 'active', 'months_paid' => $investment->isFull() ? 1 : 1]);

        $business = $investment->business;
        $business->increment('current_investors');

        $this->checkAndActivateBusiness($business);
    }
}
