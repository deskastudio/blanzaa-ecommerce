<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatConversationResource\Pages;
use App\Models\ChatConversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class ChatConversationResource extends Resource
{
    protected static ?string $model = ChatConversation::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Chat Conversations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('admin_id')
                    ->label('Assign to Admin')
                    ->relationship('admin', 'name')
                    ->searchable()
                    ->placeholder('Select admin'),
                    
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending', 
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('admin.name')
                    ->label('Assigned Admin')
                    ->placeholder('Unassigned'),
                    
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending', 
                        'primary' => 'resolved',
                        'danger' => 'closed',
                    ]),
                    
                TextColumn::make('last_message_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatConversations::route('/'),
            'edit' => Pages\EditChatConversation::route('/{record}/edit'),
        ];
    }
}