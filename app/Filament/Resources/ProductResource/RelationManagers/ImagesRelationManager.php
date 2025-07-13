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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $recordTitleAttribute = 'alt_text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->required()
                    ->directory('products')
                    ->visibility('public')
                    ->maxSize(2048),
                
                TextInput::make('alt_text')
                    ->label('Alt Text')
                    ->maxLength(255),
                
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                
                Toggle::make('is_primary')
                    ->label('Primary Image'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt_text')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->size(80),
                
                TextColumn::make('alt_text')
                    ->searchable()
                    ->limit(30),
                
                TextColumn::make('sort_order')
                    ->sortable(),
                
                IconColumn::make('is_primary')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}