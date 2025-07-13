<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            
            Stat::make('Super Admins', User::where('role', 'super_admin')->count())
                ->description('System administrators')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
            
            Stat::make('Admins', User::where('role', 'admin')->count())
                ->description('Admin users')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),
            
            Stat::make('Customers', User::where('role', 'customer')->count())
                ->description('Customer accounts')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            
            Stat::make('Active Users', User::where('is_active', true)->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
            
            Stat::make('Verified Users', User::whereNotNull('email_verified_at')->count())
                ->description('Email verified')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}