<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\AdminActivityLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('Total Products', Product::count())
                ->description('Products in catalog')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
            
            Stat::make('Total Orders', Order::count())
                ->description('All orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info')
                ->chart([3, 8, 1, 10, 2, 14, 8]),
            
            Stat::make('Categories', Category::count())
                ->description('Product categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),
            
            Stat::make('Admin Activities', AdminActivityLog::count())
                ->description('Total activities logged')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('gray'),
            
            Stat::make('System Health', '100%')
                ->description('All systems operational')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),
        ];
    }
}