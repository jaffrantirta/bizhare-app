<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use App\Models\Investment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BizShareStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $verifiedUsers = User::where('is_verified', true)->count();
        $totalBusinesses = Business::where('status', 'active')->count();
        $totalInvestments = Investment::count();
        $activeInvestments = Investment::where('status', 'active')->count();
        $totalBalance = User::sum('balance');

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description("{$verifiedUsers} verified")
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Active Businesses', number_format($totalBusinesses))
                ->description('Currently running')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Total Investments', number_format($totalInvestments))
                ->description("{$activeInvestments} active")
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('Total User Balance', 'Rp ' . number_format($totalBalance, 0, ',', '.'))
                ->description('Across all users')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),
        ];
    }
}
