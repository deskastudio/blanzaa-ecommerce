<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $pendingPayments = Order::where('payment_status', 'pending')->count();

        return [
            Stat::make('Total Orders', $totalOrders)
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            
            Stat::make('Pending Orders', $pendingOrders)
                ->description('Need attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Total Revenue', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Paid orders only')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('Pending Payments', $pendingPayments)
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('danger'),
        ];
    }
}