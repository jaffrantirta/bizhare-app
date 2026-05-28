<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentChannelStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    private static array $incomingTypes = ['initial_deposit', 'investment', 'installment'];

    protected function getStats(): array
    {
        $qrisTotal = Transaction::where('payment_method', 'qris')
            ->where('status', 'success')
            ->whereIn('type', self::$incomingTypes)
            ->sum('amount');

        $qrisCount = Transaction::where('payment_method', 'qris')
            ->where('status', 'success')
            ->whereIn('type', self::$incomingTypes)
            ->count();

        $manualTotal = Transaction::where('payment_method', 'manual_transfer')
            ->where('status', 'success')
            ->whereIn('type', self::$incomingTypes)
            ->sum('amount');

        $manualCount = Transaction::where('payment_method', 'manual_transfer')
            ->where('status', 'success')
            ->whereIn('type', self::$incomingTypes)
            ->count();

        $withdrawalTotal = Transaction::where('type', 'withdrawal')
            ->where('status', 'success')
            ->sum('amount');

        $withdrawalCount = Transaction::where('type', 'withdrawal')
            ->where('status', 'success')
            ->count();

        $netFlow = ($qrisTotal + $manualTotal) - $withdrawalTotal;

        return [
            Stat::make('Masuk via QRIS / Midtrans', 'Rp ' . number_format($qrisTotal, 0, ',', '.'))
                ->description($qrisCount . ' transaksi berhasil')
                ->descriptionIcon('heroicon-o-qr-code')
                ->color('success'),

            Stat::make('Masuk via Transfer Manual', 'Rp ' . number_format($manualTotal, 0, ',', '.'))
                ->description($manualCount . ' transaksi berhasil')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('info'),

            Stat::make('Total Penarikan (Withdrawal)', 'Rp ' . number_format($withdrawalTotal, 0, ',', '.'))
                ->description($withdrawalCount . ' penarikan diproses')
                ->descriptionIcon('heroicon-o-arrow-up-tray')
                ->color('danger'),

            Stat::make('Net Cash Flow', 'Rp ' . number_format(abs($netFlow), 0, ',', '.'))
                ->description($netFlow >= 0 ? 'Surplus (masuk - keluar)' : 'Defisit (masuk - keluar)')
                ->descriptionIcon($netFlow >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($netFlow >= 0 ? 'success' : 'danger'),
        ];
    }
}
