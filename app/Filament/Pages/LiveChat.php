<?php

namespace App\Filament\Pages;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class LiveChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Service';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Live Chat';
    protected static string $view = 'filament.pages.live-chat';
    
    // Polling interval - dikurangi untuk real-time feel
    protected static ?string $pollingInterval = '2s';
    
    public ?int $selectedConversationId = null;
    public string $message = '';
    public bool $isLoading = false;
    
    // Improved caching
    private const CACHE_TTL = 5; // 5 seconds
    private const CACHE_KEY_CONVERSATIONS = 'admin_conversations_';
    private const CACHE_KEY_MESSAGES = 'conversation_messages_';

    // Method untuk clear cache saat ada update conversation
public function clearNavigationCache(): void
{
    $cacheKey = 'nav_chat_stats_' . Auth::id();
    Cache::forget($cacheKey);
     $keysToForget = [
        'nav_chat_stats_' . Auth::id(),
        self::CACHE_KEY_CONVERSATIONS . Auth::id(),
    ];
    
    foreach ($keysToForget as $key) {
        Cache::forget($key);
    }
}


    public function mount(): void
    {
        $this->selectedConversationId = null;
        $this->message = '';
    }
// Ganti method getActiveConversations() dengan versi simple & clean
// Sekarang database sudah bersih, tidak perlu logic duplicate handling

public function getActiveConversations()
{
    $cacheKey = self::CACHE_KEY_CONVERSATIONS . Auth::id();
    
    return Cache::remember($cacheKey, self::CACHE_TTL, function () {
        $startTime = microtime(true);
        
        try {
            // Query simple dan efficient - no duplicate handling needed
            $conversations = DB::select("
                SELECT 
                    c.id,
                    c.status,
                    c.last_message_at,
                    c.admin_id,
                    c.created_at,
                    u.id as user_id,
                    u.name as user_name,
                    u.email as user_email,
                    u.avatar as user_avatar,
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
                    ) as last_message_sender,
                    (
                        SELECT m4.created_at 
                        FROM chat_messages m4 
                        WHERE m4.conversation_id = c.id 
                        ORDER BY m4.created_at DESC 
                        LIMIT 1
                    ) as last_message_time
                FROM chat_conversations c
                JOIN users u ON u.id = c.user_id
                LEFT JOIN users a ON a.id = c.admin_id
                WHERE c.status IN ('active', 'pending')
                -- Hanya conversation yang punya pesan (database sudah bersih)
                AND EXISTS (
                    SELECT 1 
                    FROM chat_messages m 
                    WHERE m.conversation_id = c.id
                )
                ORDER BY 
                    CASE WHEN c.admin_id = ? THEN 0 ELSE 1 END,
                    c.last_message_at DESC,
                    c.id DESC
                LIMIT 50
            ", [Auth::id(), Auth::id()]);
            
            Log::info('Clean conversations found: ' . count($conversations));
            
            $result = collect($conversations)->map(function ($conv) {
                return (object) [
                    'id' => $conv->id,
                    'status' => $conv->status,
                    'last_message_at' => $conv->last_message_at ? \Carbon\Carbon::parse($conv->last_message_at) : null,
                    'created_at' => \Carbon\Carbon::parse($conv->created_at),
                    'admin_id' => $conv->admin_id,
                    'unread_count' => (int) $conv->unread_count,
                    'last_message' => $conv->last_message ? \Illuminate\Support\Str::limit($conv->last_message, 100) : null,
                    'last_message_sender' => $conv->last_message_sender,
                    'last_message_time' => $conv->last_message_time ? \Carbon\Carbon::parse($conv->last_message_time) : null,
                    'is_assigned_to_me' => $conv->admin_id == Auth::id(),
                    'user' => (object) [
                        'id' => $conv->user_id,
                        'name' => $conv->user_name,
                        'email' => $conv->user_email,
                        'avatar' => $conv->user_avatar ?? null,
                        'initials' => $this->getUserInitials($conv->user_name)
                    ],
                    'admin' => $conv->admin_name ? (object) ['name' => $conv->admin_name] : null
                ];
            });
            
            $totalTime = microtime(true) - $startTime;
            Log::debug('getActiveConversations took: ' . round($totalTime * 1000, 2) . 'ms');
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('getActiveConversations error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    });
}
    // OPTIMIZED: Get messages dengan pagination dan caching
    public function getSelectedMessages()
{
    if (!$this->selectedConversationId) {
        return collect();
    }

    // HAPUS CACHE - biar selalu fresh
    try {
        $messages = DB::select("
            SELECT 
                m.id,
                m.message,
                m.sender_id,
                m.created_at,
                m.type,
                m.is_read,
                u.name as sender_name
            FROM chat_messages m
            JOIN users u ON u.id = m.sender_id
            WHERE m.conversation_id = ?
            ORDER BY m.created_at ASC
            LIMIT 200
        ", [$this->selectedConversationId]);
        
        $result = collect($messages)->map(function ($msg) {
            $isAdmin = $msg->sender_id === Auth::id();
            return (object) [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender_type' => $isAdmin ? 'admin' : 'user',
                'created_at' => \Carbon\Carbon::parse($msg->created_at),
                'type' => $msg->type ?? 'text',
                'is_read' => (bool) $msg->is_read,
                'sender' => (object) [
                    'name' => $msg->sender_name
                ]
            ];
        });
        
        return $result;
        
    } catch (\Exception $e) {
        Log::error('getSelectedMessages error: ' . $e->getMessage());
        return collect();
    }
}

    // Helper untuk initials
    private function getUserInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    // Select conversation dengan optimasi
   public function selectConversation(int $conversationId)
{
    $this->isLoading = true;
    
    try {
        $this->selectedConversationId = $conversationId;
        
        // Mark messages as read dengan batch update
        DB::transaction(function () use ($conversationId) {
            $updated = DB::update("
                UPDATE chat_messages 
                SET is_read = 1, updated_at = NOW() 
                WHERE conversation_id = ? 
                AND sender_id != ? 
                AND is_read = 0
            ", [$conversationId, Auth::id()]);
            
            Log::info("Marked {$updated} messages as read for conversation {$conversationId}");
        });
        
        $this->message = '';
        $this->clearCache();
        
        // PERBAIKAN: Pindah ke dalam try block
        $this->clearNavigationCache();
        
    } catch (\Exception $e) {
        Log::error('selectConversation error: ' . $e->getMessage());
    } finally {
        $this->isLoading = false;
    }
}

    // Close modal
    public function closeModal()
    {
        $this->selectedConversationId = null;
        $this->message = '';
        $this->isLoading = false;
    }

    // OPTIMIZED: Send message dengan validasi dan error handling
   public function sendMessage()
{
    $this->isLoading = true;
    
    try {
        $message = trim($this->message);
        
        // Validasi
        if (empty($message)) {
            $this->addError('message', 'Message cannot be empty');
            return;
        }
        
        if (strlen($message) > 1000) {
            $this->addError('message', 'Message too long (max 1000 characters)');
            return;
        }
        
        if (!$this->selectedConversationId) {
            $this->addError('message', 'No conversation selected');
            return;
        }

        DB::transaction(function () use ($message) {
            // Insert message
            $messageId = DB::table('chat_messages')->insertGetId([
                'conversation_id' => $this->selectedConversationId,
                'sender_id' => Auth::id(),
                'message' => $message,
                'type' => 'text',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Update conversation
            DB::update("
                UPDATE chat_conversations 
                SET admin_id = ?, 
                    last_message_at = NOW(), 
                    status = CASE 
                        WHEN status = 'pending' THEN 'active'
                        ELSE status 
                    END,
                    updated_at = NOW()
                WHERE id = ?
            ", [Auth::id(), $this->selectedConversationId]);
            
            Log::info("Message {$messageId} sent to conversation {$this->selectedConversationId}");
        });
        
        $this->message = '';
        $this->clearCache();
        
        // PERBAIKAN: Pindah ke dalam try block
        $this->clearNavigationCache();
        
        // Broadcast event untuk real-time updates
        $this->dispatch('message-sent', conversationId: $this->selectedConversationId);
        
    } catch (\Exception $e) {
        Log::error('sendMessage error: ' . $e->getMessage());
        $this->addError('message', 'Failed to send message. Please try again.');
    } finally {
        $this->isLoading = false;
    }
}

    // Assign conversation dengan validasi
    public function assignToMe(int $conversationId)
    {
        try {
            DB::transaction(function () use ($conversationId) {
                $updated = DB::update("
                    UPDATE chat_conversations 
                    SET admin_id = ?, 
                        status = 'active',
                        updated_at = NOW() 
                    WHERE id = ? AND (admin_id IS NULL OR admin_id != ?)
                ", [Auth::id(), $conversationId, Auth::id()]);
                
                if ($updated > 0) {
                    Log::info("Conversation {$conversationId} assigned to admin " . Auth::id());
                    $this->clearCache();
                } else {
                    Log::warning("Failed to assign conversation {$conversationId} - already assigned or not found");
                }
            });
            
        } catch (\Exception $e) {
            Log::error('assignToMe error: ' . $e->getMessage());
        }
    }

    // Close conversation dengan konfirmasi
    public function closeConversation(int $conversationId)
    {
        try {
            DB::transaction(function () use ($conversationId) {
                // Tambah system message
                DB::table('chat_messages')->insert([
                    'conversation_id' => $conversationId,
                    'sender_id' => Auth::id(),
                    'message' => 'Conversation closed by admin',
                    'type' => 'system',
                    'is_read' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Update status
                DB::update("
                    UPDATE chat_conversations 
                    SET status = 'closed', 
                        closed_at = NOW(),
                        updated_at = NOW() 
                    WHERE id = ?
                ", [$conversationId]);
                
                Log::info("Conversation {$conversationId} closed by admin " . Auth::id());
            });
            
            $this->clearCache();
            $this->closeModal();
            
        } catch (\Exception $e) {
            Log::error('closeConversation error: ' . $e->getMessage());
        }
    }

    // Clear all related caches
    private function clearCache(): void
    {
        $conversationsCacheKey = self::CACHE_KEY_CONVERSATIONS . Auth::id();
        Cache::forget($conversationsCacheKey);
        
        if ($this->selectedConversationId) {
            $messagesCacheKey = self::CACHE_KEY_MESSAGES . $this->selectedConversationId;
            Cache::forget($messagesCacheKey);
        }
    }

    // Listen untuk real-time updates
    #[On('message-received')]
    public function onMessageReceived($conversationId)
    {
        $this->clearCache();
        
        // Auto-refresh jika conversation yang aktif
        if ($this->selectedConversationId == $conversationId) {
            $this->dispatch('scroll-to-bottom');
        }
    }
// Versi yang dioptimasi untuk LiveChat.php

public static function getNavigationBadge(): ?string
{
    try {
        $unreadCount = DB::table('chat_messages as m')
            ->join('chat_conversations as c', 'c.id', 'm.conversation_id')
            ->where('m.sender_id', '!=', Auth::id())
            ->where('m.is_read', false)
            ->whereIn('c.status', ['active', 'pending'])
            // Tambah filter untuk conversation yang punya pesan
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('chat_messages as m2')
                      ->whereColumn('m2.conversation_id', 'c.id');
            })
            ->count();
        
        return $unreadCount > 0 ? (string) $unreadCount : null;
    } catch (\Exception $e) {
        Log::error('Error getting navigation badge: ' . $e->getMessage());
        return null;
    }
}

public static function getNavigationBadgeColor(): ?string
{
    $unreadCount = static::getNavigationBadge();
    
    if (!$unreadCount) {
        return null;
    }
    
    $count = (int) $unreadCount;
    
    if ($count >= 10) {
        return 'danger'; // Red untuk banyak pesan
    } elseif ($count >= 5) {
        return 'warning'; // Orange untuk sedang
    } else {
        return 'primary'; // Blue lebih baik dari green untuk visibility
    }
}

public static function getNavigationLabel(): string
{
    $baseLabel = 'Live Chat';
    
    try {
        // Cache dengan key yang lebih spesifik
        $cacheKey = 'nav_chat_stats_' . Auth::id();
        
        $stats = Cache::remember($cacheKey, 30, function () { // 30 detik cache
            $active = DB::table('chat_conversations')
                ->where('status', 'active')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('chat_messages')
                          ->whereColumn('chat_messages.conversation_id', 'chat_conversations.id');
                })
                ->count();
                
            $pending = DB::table('chat_conversations')
                ->where('status', 'pending')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('chat_messages')
                          ->whereColumn('chat_messages.conversation_id', 'chat_conversations.id');
                })
                ->count();
                
            return [
                'active' => $active,
                'pending' => $pending,
            ];
        });
        
        $totalActive = $stats['active'] + $stats['pending'];
        
        if ($totalActive > 0) {
            return $baseLabel . " ({$totalActive})";
        }
    } catch (\Exception $e) {
        Log::error('Error getting navigation label: ' . $e->getMessage());
    }
    
    return $baseLabel;
}

    // Get conversation statistics
    public function getConversationStats()
    {
        return Cache::remember('admin_chat_stats_' . Auth::id(), 60, function () {
            return [
                'total_active' => DB::table('chat_conversations')->where('status', 'active')->count(),
                'total_pending' => DB::table('chat_conversations')->where('status', 'pending')->count(),
                'my_assigned' => DB::table('chat_conversations')->where('admin_id', Auth::id())->whereIn('status', ['active', 'pending'])->count(),
                'unread_total' => DB::table('chat_messages as m')
                    ->join('chat_conversations as c', 'c.id', 'm.conversation_id')
                    ->where('m.sender_id', '!=', Auth::id())
                    ->where('m.is_read', false)
                    ->whereIn('c.status', ['active', 'pending'])
                    ->count()
            ];
        });
    }

    public function refreshMessages()
{
    // Method ini dipanggil setiap 2 detik saat modal terbuka
    // Livewire akan otomatis refresh messages
    
    // Optional: Clear cache jika menggunakan caching
    if ($this->selectedConversationId) {
        Cache::forget('messages_' . $this->selectedConversationId);
    }
}
}