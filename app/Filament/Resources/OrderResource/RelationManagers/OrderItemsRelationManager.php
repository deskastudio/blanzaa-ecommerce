<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';
    protected static ?string $recordTitleAttribute = 'product_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only for order items
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                ImageColumn::make('product.primary_image.image_path')
                    ->label('Image')
                    ->size(50)
                    ->defaultImageUrl('/images/placeholder-product.png'),
                
                TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable()
                    ->weight('medium'),
                
                TextColumn::make('product_sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                
                TextColumn::make('quantity')
                    ->numeric()
                    ->alignCenter(),
                
                TextColumn::make('price')
                    ->label('Unit Price')
                    ->money('IDR'),
                
                TextColumn::make('total')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->weight('medium'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // No create action for order items
            ])
            ->actions([
                // No edit/delete actions for order items
            ])
            ->bulkActions([
                // No bulk actions for order items
            ]);
    }
}