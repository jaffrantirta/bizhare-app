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
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create QRIS charge (universal QR — works with GoPay, OVO, DANA, ShopeePay, etc.)
     *
     * @param string $orderId
     * @param int $grossAmount Amount in IDR
     * @param array $customerDetails
     * @return array{qr_code_url: string, deeplink_url: string, transaction_id: string, order_id: string}
     * @throws Exception
     */
    public function createGopayCharge(string $orderId, int $grossAmount, array $customerDetails = []): array
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
            'custom_field1' => 'mybisnis.biz.id',
            'metadata' => [
                'app' => 'mybisnis.biz.id',
            ],
        ];

        if (!empty($customerDetails)) {
            $params['customer_details'] = $customerDetails;
        }

        Log::info('Midtrans QRIS charge request', [
            'order_id'      => $orderId,
            'amount'        => $grossAmount,
            'is_production' => Config::$isProduction,
        ]);

        $result = CoreApi::charge($params);

        $qrCodeUrl = '';

        if (isset($result->actions)) {
            foreach ($result->actions as $action) {
                if ($action->name === 'generate-qr-code') {
                    $qrCodeUrl = $action->url;
                }
            }
        }

        return [
            'qr_code_url'    => $qrCodeUrl,
            'deeplink_url'   => '',
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
