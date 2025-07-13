<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Selling Products';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->withCount(['orderItems as total_sold' => function (Builder $query) {
                        $query->selectRaw('SUM(quantity)');
                    }])
                    ->withSum('orderItems as total_revenue', 'total')
                    ->having('total_sold', '>', 0)
                    ->orderBy('total_sold', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image.image_path')
                    ->label('Image')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl('/images/placeholder-product.png'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->weight('medium')
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Units Sold')
                    ->numeric()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR'),
            ])
            ->paginated(false);
    }
}