<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $recordTitleAttribute = 'alt_text';
    protected static ?string $title = 'Product Images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->required()
                            ->directory('products/gallery')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Max size: 2MB'),
                        
                        Grid::make(1)
                            ->schema([
                                TextInput::make('alt_text')
                                    ->label('Alt Text')
                                    ->maxLength(255)
                                    ->helperText('Alternative text for accessibility'),
                                
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Order of image display'),
                                
                                Toggle::make('is_primary')
                                    ->label('Primary Image')
                                    ->helperText('Set as main product image'),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt_text')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->size(80)
                    ->square(),
                
                TextColumn::make('alt_text')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('No alt text'),
                
                TextColumn::make('sort_order')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                IconColumn::make('is_primary')
                    ->boolean()
                    ->label('Primary')
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Image')
                    ->icon('heroicon-o-photo')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Auto-increment sort order
                        $maxSort = $this->getOwnerRecord()->images()->max('sort_order') ?? 0;
                        $data['sort_order'] = $maxSort + 1;
                        return $data;
                    }),
            ])
            ->actions([
                Action::make('set_primary')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (Model $record) {
                        // Remove primary from all images
                        $record->product->images()->update(['is_primary' => false]);
                        
                        // Set this as primary
                        $record->update(['is_primary' => true]);
                        
                        Notification::make()
                            ->title('Primary image updated')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Model $record): bool => !$record->is_primary)
                    ->tooltip('Set as primary image'),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First Image')
                    ->icon('heroicon-o-photo'),
            ]);
    }
}