<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Order Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Order Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('order_number')
                                    ->label('Order Number')
                                    ->content(fn (Order $record): string => $record->order_number ?? 'Auto-generated'),
                                
                                Placeholder::make('created_at')
                                    ->label('Order Date')
                                    ->content(fn (Order $record): string => $record->created_at?->format('d M Y H:i') ?? '-'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),
                                
                                Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'refunded' => 'Refunded',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('customer_name')
                                    ->label('Customer Name')
                                    ->content(fn (Order $record): string => $record->customer_name ?? '-'),
                                
                                Placeholder::make('customer_email')
                                    ->label('Customer Email')
                                    ->content(fn (Order $record): string => $record->customer_email ?? '-'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('customer_phone')
                                    ->label('Customer Phone')
                                    ->content(fn (Order $record): string => $record->customer_phone ?? '-'),
                                
                                Placeholder::make('user.name')
                                    ->label('User Account')
                                    ->content(fn (Order $record): string => $record->user?->name ?? 'Guest'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Shipping Information')
                    ->schema([
                        Placeholder::make('shipping_address')
                            ->label('Shipping Address')
                            ->content(fn (Order $record): string => $record->shipping_address ?? '-'),
                        
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('shipping_city')
                                    ->label('City')
                                    ->content(fn (Order $record): string => $record->shipping_city ?? '-'),
                                
                                Placeholder::make('shipping_postal_code')
                                    ->label('Postal Code')
                                    ->content(fn (Order $record): string => $record->shipping_postal_code ?? '-'),
                                
                                Placeholder::make('shipping_province')
                                    ->label('Province')
                                    ->content(fn (Order $record): string => $record->shipping_province ?? '-'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Order Summary')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('total_amount')
                                    ->label('Total Amount')
                                    ->content(fn (Order $record): string => $record->formatted_total ?? '-'),
                                
                                Placeholder::make('shipping_cost')
                                    ->label('Shipping Cost')
                                    ->content(fn (Order $record): string => 'Rp ' . number_format($record->shipping_cost ?? 0, 0, ',', '.')),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Payment Information')
                    ->schema([
                        FileUpload::make('payment_proof')
                            ->label('Payment Proof')
                            ->image()
                            ->directory('payment-proofs')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->disabled()
                            ->helperText('Uploaded by customer'),
                        
                        Textarea::make('notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->placeholder('Add internal notes about this order...'),
                    ]),

                Section::make('Timeline')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('shipped_at')
                                    ->label('Shipped At')
                                    ->content(fn (Order $record): string => $record->shipped_at?->format('d M Y H:i') ?? 'Not shipped'),
                                
                                Placeholder::make('delivered_at')
                                    ->label('Delivered At')
                                    ->content(fn (Order $record): string => $record->delivered_at?->format('d M Y H:i') ?? 'Not delivered'),
                            ]),
                        
                        Placeholder::make('cancelled_at')
                            ->label('Cancelled At')
                            ->content(fn (Order $record): string => $record->cancelled_at?->format('d M Y H:i') ?? 'Not cancelled')
                            ->visible(fn (Order $record): bool => $record->status === 'cancelled'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Order number copied!')
                    ->weight('medium'),
                
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->default('Guest'),
                
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                
                TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('medium'),
                
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'primary' => 'processing',
                        'success' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'confirmed',
                        'heroicon-o-cog-6-tooth' => 'processing',
                        'heroicon-o-truck' => 'shipped',
                        'heroicon-o-home' => 'delivered',
                        'heroicon-o-x-circle' => 'cancelled',
                    ]),
                
                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check' => 'paid',
                        'heroicon-o-x-mark' => 'failed',
                        'heroicon-o-arrow-path' => 'refunded',
                    ]),
                
                ImageColumn::make('payment_proof')
                    ->label('Proof')
                    ->size(40)
                    ->toggleable()
                    ->defaultImageUrl('/images/no-image.png'),
                
                TextColumn::make('shipping_city')
                    ->label('City')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                
                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
                
                TextColumn::make('shipped_at')
                    ->label('Shipped')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),
                
                Tables\Filters\Filter::make('has_payment_proof')
                    ->label('Has Payment Proof')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('payment_proof'))
                    ->toggle(),
                
                Tables\Filters\Filter::make('total_range')
                    ->form([
                        TextInput::make('total_from')
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('total_to')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['total_from'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['total_to'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('update_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->live(),
                        
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                        
                        Textarea::make('notes')
                            ->label('Update Notes')
                            ->rows(2),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $updateData = [
                            'status' => $data['status'],
                            'payment_status' => $data['payment_status'],
                        ];
                        
                        // Update timestamps based on status
                        if ($data['status'] === 'shipped' && !$record->shipped_at) {
                            $updateData['shipped_at'] = now();
                        }
                        
                        if ($data['status'] === 'delivered' && !$record->delivered_at) {
                            $updateData['delivered_at'] = now();
                        }
                        
                        if ($data['status'] === 'cancelled' && !$record->cancelled_at) {
                            $updateData['cancelled_at'] = now();
                        }
                        
                        if (!empty($data['notes'])) {
                            $updateData['notes'] = $record->notes . "\n\n[" . now()->format('d M Y H:i') . "] " . $data['notes'];
                        }
                        
                        $record->update($updateData);
                        
                        Notification::make()
                            ->title('Order status updated successfully')
                            ->success()
                            ->send();
                    }),
                
                Action::make('view_payment_proof')
                    ->label('View Payment')
                    ->icon('heroicon-o-photo')
                    ->color('warning')
                    ->visible(fn (Order $record): bool => !empty($record->payment_proof))
                    ->url(fn (Order $record): string => asset('storage/' . $record->payment_proof))
                    ->openUrlInNewTab(),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('mark_confirmed')
                        ->label('Mark as Confirmed')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'confirmed']);
                            Notification::make()
                                ->title('Orders marked as confirmed')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    
                    BulkAction::make('mark_processing')
                        ->label('Mark as Processing')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('primary')
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'processing']);
                            Notification::make()
                                ->title('Orders marked as processing')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    
                    BulkAction::make('mark_shipped')
                        ->label('Mark as Shipped')
                        ->icon('heroicon-o-truck')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->update([
                                'status' => 'shipped',
                                'shipped_at' => now(),
                            ]);
                            Notification::make()
                                ->title('Orders marked as shipped')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    
                    BulkAction::make('mark_payment_paid')
                        ->label('Mark Payment as Paid')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->update(['payment_status' => 'paid']);
                            Notification::make()
                                ->title('Payment status updated to paid')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_number', 'customer_name', 'customer_email'];
    }
}