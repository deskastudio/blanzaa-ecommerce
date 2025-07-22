<?php

namespace App\Filament\Pages;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class LiveChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = 'Live Chat';
    protected static string $view = 'filament.pages.live-chat';
    
    // Auto refresh setiap 5 detik
    protected static ?string $pollingInterval = '5s';
    
    public ?int $selectedConversationId = null;
    public string $message = '';

    public function mount(): void
    {
        // Tidak auto-select conversation untuk modal style
        $this->selectedConversationId = null;
    }

    // Ambil semua conversations aktif
    public function getActiveConversations()
    {
        return ChatConversation::with(['user', 'admin'])
            ->where('status', '!=', 'closed')
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) {
                // Hitung unread messages (tanpa sender_type)
                $conversation->unread_count = ChatMessage::where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', Auth::id()) // Messages bukan dari admin current
                    ->where('is_read', false)
                    ->count();
                return $conversation;
            });
    }

    // Ambil messages dari conversation yang dipilih
    public function getSelectedMessages()
    {
        if (!$this->selectedConversationId) {
            return collect();
        }

        return ChatMessage::with('sender')
            ->where('conversation_id', $this->selectedConversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                // Tentukan sender type berdasarkan user role atau logika lain
                $message->sender_type = $message->sender_id === Auth::id() ? 'admin' : 'user';
                return $message;
            });
    }

    // Pilih conversation (buka modal)
    public function selectConversation(int $conversationId)
    {
        $this->selectedConversationId = $conversationId;
        
        // Mark messages as read (messages bukan dari admin current)
        ChatMessage::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        $this->message = '';
    }

    // Close modal
    public function closeModal()
    {
        $this->selectedConversationId = null;
        $this->message = '';
    }

    // Kirim message
    public function sendMessage()
    {
        if (empty(trim($this->message)) || !$this->selectedConversationId) {
            return;
        }

        $conversation = ChatConversation::find($this->selectedConversationId);
        
        if (!$conversation) {
            return;
        }

        // Simpan message (tanpa sender_type)
        ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'message' => trim($this->message),
            'type' => 'text',
            'is_read' => true, // Admin messages otomatis read
        ]);

        // Update conversation
        $conversation->update([
            'admin_id' => Auth::id(),
            'last_message_at' => now(),
            'status' => 'active',
        ]);

        // Clear message
        $this->message = '';
    }

    // Assign conversation ke admin
    public function assignToMe(int $conversationId)
    {
        ChatConversation::where('id', $conversationId)
            ->update(['admin_id' => Auth::id()]);
    }

    // Close conversation
    public function closeConversation(int $conversationId)
    {
        ChatConversation::where('id', $conversationId)
            ->update(['status' => 'closed']);
            
        // Close modal after closing conversation
        $this->closeModal();
    }
}