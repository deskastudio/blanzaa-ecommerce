<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('key')
                    ->required()
                    ->unique(Setting::class, 'key', ignoreRecord: true)
                    ->maxLength(255),
                
                Select::make('type')
                    ->options([
                        'string' => 'String',
                        'text' => 'Text',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ])
                    ->required()
                    ->live(),
                
                Forms\Components\Group::make()
                    ->schema([
                        TextInput::make('value')
                            ->required()
                            ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['string', 'number'])),
                        
                        Textarea::make('value')
                            ->required()
                            ->rows(4)
                            ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['text', 'json'])),
                        
                        Forms\Components\Toggle::make('value')
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'boolean'),
                    ]),
                
                Textarea::make('description')
                    ->rows(2)
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'string',
                        'success' => 'text',
                        'warning' => 'number',
                        'danger' => 'boolean',
                        'info' => 'json',
                    ]),
                
                TextColumn::make('value')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                
                TextColumn::make('description')
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'text' => 'Text',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}