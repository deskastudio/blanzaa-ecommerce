<?php

namespace App\Filament\Pages;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiveChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = 'Live Chat';
    protected static string $view = 'filament.pages.live-chat';
    
    // Polling interval
    protected static ?string $pollingInterval = '5s';
    
    public ?int $selectedConversationId = null;
    public string $message = '';
    
    // Cache untuk performa
    public $conversationsCache = null;
    public $lastCacheTime = null;

    public function mount(): void
    {
        $this->selectedConversationId = null;
    }

    // OPTIMIZED: Get conversations dengan last message
    public function getActiveConversations()
    {
        $startTime = microtime(true);
        
        // Cache untuk 3 detik
        if ($this->conversationsCache && $this->lastCacheTime && (time() - $this->lastCacheTime) < 3) {
            return $this->conversationsCache;
        }
        
        try {
            // Raw SQL dengan last message - OPTIMIZED!
            $conversations = DB::select("
                SELECT 
                    c.id,
                    c.status,
                    c.last_message_at,
                    c.admin_id,
                    u.id as user_id,
                    u.name as user_name,
                    u.email as user_email,
                    a.name as admin_name,
                    (
                        SELECT COUNT(*) 
                        FROM chat_messages m 
                        WHERE m.conversation_id = c.id 
                        AND m.sender_id != ? 
                        AND m.is_read = 0
                    ) as unread_count,
                    (
                        SELECT m2.message 
                        FROM chat_messages m2 
                        WHERE m2.conversation_id = c.id 
                        ORDER BY m2.created_at DESC 
                        LIMIT 1
                    ) as last_message,
                    (
                        SELECT u2.name 
                        FROM chat_messages m3 
                        JOIN users u2 ON u2.id = m3.sender_id 
                        WHERE m3.conversation_id = c.id 
                        ORDER BY m3.created_at DESC 
                        LIMIT 1
                    ) as last_message_sender
                FROM chat_conversations c
                JOIN users u ON u.id = c.user_id
                LEFT JOIN users a ON a.id = c.admin_id
                WHERE c.status != 'closed'
                ORDER BY c.last_message_at DESC
                LIMIT 20
            ", [Auth::id()]);
            
            // Convert ke collection dengan last_message
            $result = collect($conversations)->map(function ($conv) {
                return (object) [
                    'id' => $conv->id,
                    'status' => $conv->status,
                    'last_message_at' => $conv->last_message_at ? \Carbon\Carbon::parse($conv->last_message_at) : null,
                    'admin_id' => $conv->admin_id,
                    'unread_count' => $conv->unread_count,
                    'last_message' => $conv->last_message,         // ✅ KEY: Ini yang dibutuhkan view
                    'last_message_sender' => $conv->last_message_sender,  // ✅ KEY: Ini juga
                    'user' => (object) [
                        'id' => $conv->user_id,
                        'name' => $conv->user_name,
                        'email' => $conv->user_email
                    ],
                    'admin' => $conv->admin_name ? (object) ['name' => $conv->admin_name] : null
                ];
            });
            
            // Cache result
            $this->conversationsCache = $result;
            $this->lastCacheTime = time();
            
            $totalTime = microtime(true) - $startTime;
            Log::info('getActiveConversations took: ' . round($totalTime * 1000, 2) . 'ms');
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('getActiveConversations error: ' . $e->getMessage());
            return collect();
        }
    }

    // OPTIMIZED: Get messages dengan raw SQL
    public function getSelectedMessages()
    {
        if (!$this->selectedConversationId) {
            return collect();
        }

        $startTime = microtime(true);
        
        try {
            // Raw SQL untuk performa
            $messages = DB::select("
                SELECT 
                    m.id,
                    m.message,
                    m.sender_id,
                    m.created_at,
                    u.name as sender_name
                FROM chat_messages m
                JOIN users u ON u.id = m.sender_id
                WHERE m.conversation_id = ?
                ORDER BY m.created_at ASC
                LIMIT 100
            ", [$this->selectedConversationId]);
            
            // Convert dan tambah sender_type
            $result = collect($messages)->map(function ($msg) {
                return (object) [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'sender_type' => $msg->sender_id === Auth::id() ? 'admin' : 'user',
                    'created_at' => \Carbon\Carbon::parse($msg->created_at),
                    'sender' => (object) ['name' => $msg->sender_name]
                ];
            });
            
            $totalTime = microtime(true) - $startTime;
            Log::info('getSelectedMessages took: ' . round($totalTime * 1000, 2) . 'ms');
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('getSelectedMessages error: ' . $e->getMessage());
            return collect();
        }
    }

    // Select conversation
    public function selectConversation(int $conversationId)
    {
        Log::info('Selecting conversation: ' . $conversationId);
        
        $this->selectedConversationId = $conversationId;
        
        // Mark messages as read dengan raw SQL
        try {
            $updated = DB::update("
                UPDATE chat_messages 
                SET is_read = 1, updated_at = NOW() 
                WHERE conversation_id = ? 
                AND sender_id != ? 
                AND is_read = 0
            ", [$conversationId, Auth::id()]);
            
            Log::info('Marked ' . $updated . ' messages as read');
            
        } catch (\Exception $e) {
            Log::error('Mark as read error: ' . $e->getMessage());
        }
        
        $this->message = '';
        
        // Clear cache karena unread count berubah
        $this->conversationsCache = null;
    }

    // Close modal
    public function closeModal()
    {
        $this->selectedConversationId = null;
        $this->message = '';
    }

    // OPTIMIZED: Send message dengan raw SQL
    public function sendMessage()
    {
        $startTime = microtime(true);
        
        if (empty(trim($this->message)) || !$this->selectedConversationId) {
            Log::warning('sendMessage failed: empty message or no conversation');
            return;
        }

        try {
            // Insert message dengan raw SQL - FAST!
            $messageId = DB::table('chat_messages')->insertGetId([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => Auth::id(),
                'message' => trim($this->message),
                'type' => 'text',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Update conversation dengan raw SQL
            DB::update("
                UPDATE chat_conversations 
                SET admin_id = ?, last_message_at = NOW(), status = 'active', updated_at = NOW()
                WHERE id = ?
            ", [Auth::id(), $this->selectedConversationId]);
            
            $this->message = '';
            
            // Clear cache
            $this->conversationsCache = null;
            
            $totalTime = microtime(true) - $startTime;
            Log::info('sendMessage completed in: ' . round($totalTime * 1000, 2) . 'ms');
            
        } catch (\Exception $e) {
            Log::error('sendMessage error: ' . $e->getMessage());
        }
    }

    // Assign conversation
    public function assignToMe(int $conversationId)
    {
        try {
            DB::update("
                UPDATE chat_conversations 
                SET admin_id = ?, updated_at = NOW() 
                WHERE id = ?
            ", [Auth::id(), $conversationId]);
            
            // Clear cache
            $this->conversationsCache = null;
            
            Log::info('Assigned conversation ' . $conversationId . ' to admin ' . Auth::id());
            
        } catch (\Exception $e) {
            Log::error('assignToMe error: ' . $e->getMessage());
        }
    }

    // Close conversation
    public function closeConversation(int $conversationId)
    {
        try {
            DB::update("
                UPDATE chat_conversations 
                SET status = 'closed', updated_at = NOW() 
                WHERE id = ?
            ", [$conversationId]);
            
            // Clear cache
            $this->conversationsCache = null;
            
            $this->closeModal();
            
            Log::info('Closed conversation: ' . $conversationId);
            
        } catch (\Exception $e) {
            Log::error('closeConversation error: ' . $e->getMessage());
        }
    }
}