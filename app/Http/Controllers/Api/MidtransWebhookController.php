<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Investment;
use App\Models\Transaction;
use App\Services\InvestmentService;
use App\Services\MidtransService;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    use ApiResponse;

    public function __construct(
        private MidtransService $midtransService,
        private InvestmentService $investmentService,
        private ReferralService $referralService,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans webhook received', $payload);

        $serverKey = config('services.midtrans.server_key');
        $expected  = hash('sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            $serverKey
        );

        if (($payload['signature_key'] ?? '') !== $expected) {
            Log::warning('Midtrans webhook signature mismatch');
            return $this->error('Invalid signature', null, 403);
        }

        $orderId = $payload['order_id'] ?? null;
        if (!$orderId) {
            return $this->error('Missing order_id');
        }

        $transaction = Transaction::where('midtrans_order_id', $orderId)->first();
        if (!$transaction) {
            Log::warning("Transaction not found for order_id: {$orderId}");
            return $this->success(null, 'Acknowledged');
        }

        $internalStatus = $this->midtransService->mapStatus(
            $payload['transaction_status'] ?? null,
            $payload['fraud_status'] ?? null
        );

        DB::transaction(function () use ($transaction, $internalStatus, $payload) {
            $transaction->update([
                'status'                 => $internalStatus,
                'midtrans_status'        => $payload['transaction_status'] ?? null,
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $transaction->midtrans_transaction_id,
            ]);

            if ($internalStatus === 'success') {
                $this->processSuccessfulPayment($transaction);
            }
        });

        return $this->success(null, 'Webhook processed');
    }

    private function processSuccessfulPayment(Transaction $transaction): void
    {
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
    }
}
