<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        // Indonesian locale for GoPay pages
        Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Payment-Locale: id-ID';
    }

    /**
     * Create GoPay Dynamic QRIS charge.
     * Uses order_id as Idempotency-Key — safe to retry on network error.
     *
     * @param string $orderId
     * @param int $grossAmount Amount in IDR
     * @param array $customerDetails
     * @return array{qr_code_url: string, deeplink_url: string, transaction_id: string, order_id: string}
     * @throws Exception
     */
    public function createGopayCharge(string $orderId, int $grossAmount, array $customerDetails = []): array
    {
        // Midtrans requires HTTPS callback URL in production
        $callbackUrl = str_replace('http://', 'https://', config('app.url'))
            . '/api/payments/midtrans/callback';

        // Idempotency-Key prevents duplicate charges on mobile retry (max 46 chars)
        Config::$paymentIdempotencyKey = substr($orderId, 0, 46);

        $params = [
            'payment_type' => 'gopay',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url'    => $callbackUrl,
            ],
            'custom_field1' => 'mybisnis.biz.id',
            'metadata' => [
                'app' => 'mybisnis.biz.id',
            ],
        ];

        if (!empty($customerDetails)) {
            $params['customer_details'] = $customerDetails;
        }

        Log::info('Midtrans GoPay charge request', [
            'order_id'        => $orderId,
            'amount'          => $grossAmount,
            'is_production'   => Config::$isProduction,
            'callback_url'    => $callbackUrl,
            'idempotency_key' => Config::$paymentIdempotencyKey,
        ]);

        try {
            $result = CoreApi::charge($params);
        } finally {
            // Reset idempotency key so it doesn't bleed into other calls
            Config::$paymentIdempotencyKey = null;
        }

        $qrCodeUrl   = '';
        $deeplinkUrl = '';

        if (isset($result->actions)) {
            foreach ($result->actions as $action) {
                if ($action->name === 'generate-qr-code') {
                    $qrCodeUrl = $action->url;
                }
                if ($action->name === 'deeplink-redirect') {
                    $deeplinkUrl = $action->url;
                }
            }
        }

        return [
            'qr_code_url'    => $qrCodeUrl,
            'deeplink_url'   => $deeplinkUrl,
            'transaction_id' => $result->transaction_id ?? '',
            'order_id'       => $result->order_id ?? $orderId,
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
            'capture'  => ($fraudStatus === 'accept' || $fraudStatus === null) ? 'success' : 'failed',
            'settlement' => 'success',
            'pending'  => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default    => 'pending',
        };
    }
}
