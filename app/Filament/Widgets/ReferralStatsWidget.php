<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReferralStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalReferrals = User::whereNotNull('referred_by')->count();

        $totalRewarded = Transaction::where('type', 'referral_reward')
            ->where('status', 'success')
            ->sum('amount');

        $topReferrer = User::withCount('referrals')
            ->orderByDesc('referrals_count')
            ->first();

        return [
            Stat::make('Total Referral Members', number_format($totalReferrals))
                ->description('Users who joined via referral')
                ->icon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Total Referral Rewards', 'IDR ' . number_format($totalRewarded, 0, ',', '.'))
                ->description('Distributed to all uplines')
                ->icon('heroicon-o-gift')
                ->color('success'),

            Stat::make('Top Referrer', $topReferrer?->name ?? '—')
                ->description($topReferrer ? ($topReferrer->referrals_count . ' direct referrals') : 'No data')
                ->icon('heroicon-o-trophy')
                ->color('warning'),
        ];
    }
}
