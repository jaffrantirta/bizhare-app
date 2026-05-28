<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Investment;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Notifications\InvestmentPaymentFailedNotification;
use App\Notifications\InvestmentPaymentSuccessNotification;
use App\Notifications\TopUpFailedNotification;
use App\Notifications\TopUpSuccessNotification;
use App\Services\InvestmentService;
use App\Services\MidtransService;
use App\Services\ReferralService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private MidtransService $midtransService,
        private InvestmentService $investmentService,
        private ReferralService $referralService,
    ) {}

    public function initialDeposit(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->has_initial_deposit) {
            return $this->error('Initial deposit already completed.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:manual_transfer,gopay,qris',
        ]);

        // normalise legacy 'qris' value from older mobile app versions
        if ($validated['payment_method'] === 'qris') {
            $validated['payment_method'] = 'gopay';
        }

        $depositAmount = (int) SystemSetting::get('initial_deposit_amount', 375000);
        $orderId       = 'DEP-' . $user->id . '-' . time();

        $transactionData = [
            'user_id'           => $user->id,
            'type'              => 'initial_deposit',
            'amount'            => $depositAmount,
            'status'            => 'pending',
            'payment_method'    => $validated['payment_method'],
            'midtrans_order_id' => $orderId,
        ];

        if ($validated['payment_method'] === 'gopay') {
            try {
                $gopay = $this->midtransService->createGopayCharge(
                    $orderId,
                    $depositAmount,
                    ['first_name' => $user->name, 'email' => $user->email]
                );
                $transactionData['midtrans_transaction_id'] = $gopay['transaction_id'];
                $transactionData['midtrans_qr_code_url']    = $gopay['qr_code_url'];
                $transactionData['midtrans_deeplink_url']   = $gopay['deeplink_url'];
            } catch (Exception $e) {
                return $this->error('Failed to create QRIS payment: ' . $e->getMessage());
            }
        }

        $transaction   = Transaction::create($transactionData);
        $responseData  = ['transaction' => $transaction];

        if ($validated['payment_method'] === 'manual_transfer') {
            $responseData['bank_details'] = [
                'bank_name'      => config('payment.bank_name', 'BCA'),
                'account_number' => config('payment.account_number', '1234567890'),
                'account_name'   => config('app.name'),
                'amount'         => $depositAmount,
            ];
        }

        return $this->created($responseData, 'Initial deposit initiated. Please complete your payment.');
    }

    public function uploadProof(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'proof_image'    => 'required|file|image|max:5120',
        ]);

        $transaction = Transaction::where('id', $validated['transaction_id'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            return $this->notFound('Transaction not found or already processed.');
        }

        $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');
        $transaction->update(['proof_image' => $proofPath]);

        return $this->success(['transaction' => $transaction], 'Payment proof uploaded. Waiting for admin confirmation.');
    }

    public function payInstallment(Investment $investment, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($investment->user_id !== $user->id) {
            return $this->forbidden('You do not have access to this investment.');
        }

        if (!$investment->isInstallment()) {
            return $this->error('This is not an installment investment.');
        }

        $nextInstallment = $investment->nextInstallment();

        if (!$nextInstallment) {
            return $this->error('No pending installment found.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:manual_transfer,gopay,qris',
        ]);

        if ($validated['payment_method'] === 'qris') {
            $validated['payment_method'] = 'gopay';
        }

        $paymentAmount = $nextInstallment->amount + $nextInstallment->admin_fee;
        $orderId       = 'INST-' . $investment->id . '-' . $nextInstallment->month_number . '-' . time();

        $transactionData = [
            'user_id'           => $user->id,
            'type'              => 'installment',
            'amount'            => $paymentAmount,
            'status'            => 'pending',
            'reference_id'      => (string) $investment->id,
            'payment_method'    => $validated['payment_method'],
            'midtrans_order_id' => $orderId,
        ];

        if ($validated['payment_method'] === 'gopay') {
            try {
                $gopay = $this->midtransService->createGopayCharge(
                    $orderId,
                    (int) $paymentAmount,
                    ['first_name' => $user->name, 'email' => $user->email]
                );
                $transactionData['midtrans_transaction_id'] = $gopay['transaction_id'];
                $transactionData['midtrans_qr_code_url']    = $gopay['qr_code_url'];
                $transactionData['midtrans_deeplink_url']   = $gopay['deeplink_url'];
            } catch (Exception $e) {
                return $this->error('Failed to create QRIS payment: ' . $e->getMessage());
            }
        }

        $transaction  = Transaction::create($transactionData);
        $responseData = ['transaction' => $transaction, 'installment' => $nextInstallment];

        if ($validated['payment_method'] === 'manual_transfer') {
            $responseData['bank_details'] = [
                'bank_name'      => config('payment.bank_name', 'BCA'),
                'account_number' => config('payment.account_number', '1234567890'),
                'account_name'   => config('app.name'),
                'amount'         => $paymentAmount,
            ];
        }

        return $this->created($responseData, 'Installment payment initiated.');
    }

    public function checkStatus(Transaction $transaction, Request $request): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return $this->forbidden('You do not have access to this transaction.');
        }

        if ($transaction->payment_method === 'gopay' && $transaction->midtrans_order_id && $transaction->isPending()) {
            try {
                $status         = $this->midtransService->getTransactionStatus($transaction->midtrans_order_id);
                $internalStatus = $this->midtransService->mapStatus($status['transaction_status'], $status['fraud_status']);

                if ($internalStatus !== 'pending') {
                    $transaction->update([
                        'midtrans_status' => $status['transaction_status'],
                        'status'          => $internalStatus,
                    ]);

                    $user = $transaction->user;

                    if ($internalStatus === 'success') {
                        $this->processSuccessfulPayment($transaction);

                        if ($transaction->type === 'initial_deposit') {
                            $user->notify(new TopUpSuccessNotification($transaction));
                        } elseif (in_array($transaction->type, ['investment', 'installment'])) {
                            $user->notify(new InvestmentPaymentSuccessNotification($transaction));
                        }
                    } elseif ($internalStatus === 'failed') {
                        if ($transaction->type === 'initial_deposit') {
                            $user->notify(new TopUpFailedNotification($transaction));
                        } elseif (in_array($transaction->type, ['investment', 'installment'])) {
                            $user->notify(new InvestmentPaymentFailedNotification($transaction));
                        }
                    }
                }
            } catch (Exception) {
                // Silently fall through with cached status
            }
        }

        return $this->success(['transaction' => $transaction->fresh()]);
    }

    public function history(Request $request): JsonResponse
    {
        $query = $request->user()
            ->transactions()
            ->whereIn('type', ['initial_deposit', 'investment', 'installment'])
            ->latest();

        return $this->success($query->paginate($request->get('per_page', 15)));
    }

    private function processSuccessfulPayment(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $user = $transaction->user;

            if ($transaction->type === 'initial_deposit') {
                $user->update([
                    'has_initial_deposit'          => true,
                    'initial_deposit_confirmed_at' => now(),
                ]);
                $this->referralService->distributeRewards($user);
            } elseif (in_array($transaction->type, ['investment', 'installment'])) {
                $investment = Investment::find($transaction->reference_id);
                if ($investment) {
                    $this->investmentService->activateInvestment($investment);
                }
            }
        });
    }
}
