<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $featuredProducts = Product::where('is_featured', true)->count();
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();

        return [
            Stat::make('Total Products', $totalProducts)
                ->description('All products in catalog')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
            
            Stat::make('Active Products', $activeProducts)
                ->description('Currently visible')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),
            
            Stat::make('Featured Products', $featuredProducts)
                ->description('Highlighted products')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
            
            Stat::make('Out of Stock', $outOfStockProducts)
                ->description('Need restocking')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}