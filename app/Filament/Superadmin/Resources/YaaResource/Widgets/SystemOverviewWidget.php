<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Products in catalog')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
            
            Stat::make('Categories', Category::count())
                ->description('Product categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),
            
            Stat::make('Total Orders', Order::count())
                ->description('All orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
            
            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Need attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}