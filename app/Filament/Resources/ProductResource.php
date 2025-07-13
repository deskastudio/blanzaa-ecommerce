<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Product Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $context, $state, Forms\Set $set) => 
                                        $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                                    ),
                                
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->helperText('URL-friendly version of the name'),
                            ]),
                        
                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Textarea::make('short_description')
                            ->maxLength(500)
                            ->rows(2)
                            ->helperText('Brief description shown in product listings'),
                        
                        Textarea::make('description')
                            ->maxLength(2000)
                            ->rows(4)
                            ->helperText('Detailed product description'),
                    ])->columns(1),

                Section::make('Product Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(100)
                                    ->helperText('Stock Keeping Unit'),
                                
                                TextInput::make('brand')
                                    ->maxLength(100)
                                    ->datalist([
                                        'Apple',
                                        'Samsung',
                                        'Xiaomi',
                                        'Huawei',
                                        'Asus',
                                        'Acer',
                                        'Lenovo',
                                        'HP',
                                        'Dell',
                                        'Sony',
                                    ]),
                                
                                TextInput::make('warranty')
                                    ->maxLength(255)
                                    ->placeholder('e.g., 1 Year International Warranty'),
                            ]),
                    ]),

                Section::make('Pricing & Inventory')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->step(1000),
                                
                                TextInput::make('compare_price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->label('Compare Price')
                                    ->helperText('Original price (for discounts)')
                                    ->minValue(0)
                                    ->step(1000),
                                
                                TextInput::make('stock_quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                
                                TextInput::make('min_stock_level')
                                    ->numeric()
                                    ->default(10)
                                    ->label('Min Stock Level')
                                    ->helperText('Low stock alert threshold')
                                    ->minValue(0),
                            ]),
                    ]),

                Section::make('Physical Properties')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('weight')
                                    ->numeric()
                                    ->suffix('kg')
                                    ->step(0.01)
                                    ->minValue(0),
                                
                                TextInput::make('dimensions')
                                    ->maxLength(255)
                                    ->placeholder('Length x Width x Height (cm)')
                                    ->helperText('Product dimensions for shipping'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('SEO & Status')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->helperText('SEO title for search engines'),
                        
                        Textarea::make('meta_description')
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText('SEO description (max 160 characters)'),
                        
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->helperText('Product visibility on website'),
                                
                                Toggle::make('is_featured')
                                    ->default(false)
                                    ->helperText('Show in featured products section'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_image.image_path')
                    ->label('Image')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl('/images/placeholder-product.png'),
                
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                
                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),
                
                TextColumn::make('sku')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('SKU copied to clipboard'),
                
                TextColumn::make('brand')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                TextColumn::make('compare_price')
                    ->money('IDR')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . ' pcs'),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                
                IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('brand')
                    ->options(function () {
                        return Product::query()
                            ->whereNotNull('brand')
                            ->distinct()
                            ->pluck('brand', 'brand')
                            ->toArray();
                    })
                    ->searchable(),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('price_to')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('stock_quantity', '<=', 'min_stock_level'))
                    ->toggle(),
                
                Tables\Filters\Filter::make('out_of_stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 0))
                    ->toggle(),
            ])
            ->actions([
                Action::make('toggle_featured')
                    ->icon('heroicon-o-star')
                    ->color(fn (Product $record): string => $record->is_featured ? 'warning' : 'gray')
                    ->action(function (Product $record) {
                        $record->update(['is_featured' => !$record->is_featured]);
                        $status = $record->is_featured ? 'featured' : 'unfeatured';
                        Notification::make()
                            ->title("Product {$status} successfully")
                            ->success()
                            ->send();
                    })
                    ->tooltip(fn (Product $record): string => $record->is_featured ? 'Remove from featured' : 'Mark as featured'),
                
                Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function (Product $record) {
                        $newProduct = $record->replicate();
                        $newProduct->name = $record->name . ' (Copy)';
                        $newProduct->slug = $record->slug . '-copy';
                        $newProduct->sku = $record->sku . '-COPY';
                        $newProduct->save();
                        
                        Notification::make()
                            ->title('Product duplicated successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => true]);
                            Notification::make()
                                ->title('Products activated successfully')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => false]);
                            Notification::make()
                                ->title('Products deactivated successfully')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_featured' => true]);
                            Notification::make()
                                ->title('Products marked as featured')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            // 'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'brand'];
    }
}