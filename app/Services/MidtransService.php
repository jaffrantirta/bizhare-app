<?php

namespace App\Services;

use Exception;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create QRIS charge via GoPay payment_type
     *
     * @param string $orderId
     * @param int $grossAmount Amount in IDR
     * @param array $customerDetails
     * @return array{qr_code_url: string, deeplink_url: string, transaction_id: string, order_id: string}
     * @throws Exception
     */
    public function createQrisCharge(string $orderId, int $grossAmount, array $customerDetails = []): array
    {
        $params = [
            'payment_type' => 'gopay',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'gopay' => [
                'enable_callback' => true,
                'callback_url' => config('app.url') . '/api/webhooks/midtrans',
            ],
        ];

        if (!empty($customerDetails)) {
            $params['customer_details'] = $customerDetails;
        }

        $result = CoreApi::charge($params);

        $qrCodeUrl = '';
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
            'qr_code_url' => $qrCodeUrl,
            'deeplink_url' => $deeplinkUrl,
            'transaction_id' => $result->transaction_id ?? '',
            'order_id' => $result->order_id ?? $orderId,
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
            'order_id' => $result->order_id ?? $orderId,
            'transaction_id' => $result->transaction_id ?? null,
            'transaction_status' => $result->transaction_status ?? 'unknown',
            'payment_type' => $result->payment_type ?? null,
            'gross_amount' => $result->gross_amount ?? 0,
            'fraud_status' => $result->fraud_status ?? null,
        ];
    }

    /**
     * Map Midtrans transaction status to internal status
     */
    public function mapStatus(string $midtransStatus, ?string $fraudStatus = null): string
    {
        return match ($midtransStatus) {
            'capture' => ($fraudStatus === 'accept' || $fraudStatus === null) ? 'success' : 'failed',
            'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default => 'pending',
        };
    }
}
