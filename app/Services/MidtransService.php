<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Create a Snap transaction token.
     * Returns snap_token (used by Snap.js / mobile SDK) and snap_redirect_url (WebView).
     *
     * @param string $orderId
     * @param int $grossAmount Amount in IDR
     * @param array $customerDetails  ['first_name', 'email', ...]
     * @return array{snap_token: string, snap_redirect_url: string, order_id: string}
     * @throws Exception
     */
    public function createSnapToken(string $orderId, int $grossAmount, array $customerDetails = []): array
    {
        $callbackUrl = str_replace('http://', 'https://', config('app.url'))
            . '/api/payments/midtrans/callback';

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'enabled_payments' => ['gopay', 'other_qris'],
            'gopay' => [
                'enable_callback' => true,
                'callback_url'    => $callbackUrl,
            ],
            'callbacks' => [
                'finish' => $callbackUrl,
            ],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 24,
            ],
            'custom_field1' => 'mybisnis.biz.id',
        ];

        if (!empty($customerDetails)) {
            $params['customer_details'] = $customerDetails;
        }

        Log::info('Midtrans Snap create transaction', [
            'order_id'      => $orderId,
            'amount'        => $grossAmount,
            'is_production' => Config::$isProduction,
        ]);

        $result = Snap::createTransaction($params);

        return [
            'snap_token'       => $result->token,
            'snap_redirect_url' => $result->redirect_url,
            'order_id'         => $orderId,
        ];
    }

    /**
     * Get transaction status from Midtrans
     *
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function getTransactionStatus(string $orderId): array
    {
        $result = Transaction::status($orderId);

        return [
            'order_id'           => $result->order_id ?? $orderId,
            'transaction_id'     => $result->transaction_id ?? null,
            'transaction_status' => $result->transaction_status ?? 'unknown',
            'payment_type'       => $result->payment_type ?? null,
            'gross_amount'       => $result->gross_amount ?? 0,
            'fraud_status'       => $result->fraud_status ?? null,
        ];
    }

    /**
     * Map Midtrans transaction status to internal status
     */
    public function mapStatus(string $midtransStatus, ?string $fraudStatus = null): string
    {
        return match ($midtransStatus) {
            'capture'    => ($fraudStatus === 'accept' || $fraudStatus === null) ? 'success' : 'failed',
            'settlement' => 'success',
            'pending'    => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default      => 'pending',
        };
    }
}
