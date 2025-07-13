<?php

namespace App\Filament\Superadmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            \App\Filament\Superadmin\Widgets\UserStatsWidget::class,
            \App\Filament\Superadmin\Widgets\SystemOverviewWidget::class,
            \App\Filament\Superadmin\Widgets\SystemStatsWidget::class,
            \App\Filament\Superadmin\Widgets\UserActivityChartWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }
}