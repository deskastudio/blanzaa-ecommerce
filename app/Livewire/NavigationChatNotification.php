<?php

// Buat file baru: app/Livewire/NavigationChatNotification.php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NavigationChatNotification extends Component
{
    public int $unreadCount = 0;
    public int $activeConversations = 0;
    public bool $hasUrgent = false;
    
    // Polling untuk update otomatis
    protected $listeners = [
        'chat-updated' => 'refreshNotifications',
        'message-received' => 'refreshNotifications'
    ];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        try {
            // Get unread messages count
            $this->unreadCount = DB::table('chat_messages as m')
                ->join('chat_conversations as c', 'c.id', 'm.conversation_id')
                ->where('m.sender_id', '!=', Auth::id())
                ->where('m.is_read', false)
                ->whereIn('c.status', ['active', 'pending'])
                ->count();

            // Get active conversations count
            $this->activeConversations = DB::table('chat_conversations')
                ->whereIn('status', ['active', 'pending'])
                ->where(function($query) {
                    $query->whereExists(function($subQuery) {
                        $subQuery->select(DB::raw(1))
                                ->from('chat_messages')
                                ->whereColumn('chat_messages.conversation_id', 'chat_conversations.id');
                    });
                })
                ->count();

            // Check for urgent messages (unread > 5 or older than 2 hours)
            $this->hasUrgent = DB::table('chat_messages as m')
                ->join('chat_conversations as c', 'c.id', 'm.conversation_id')
                ->where('m.sender_id', '!=', Auth::id())
                ->where('m.is_read', false)
                ->whereIn('c.status', ['active', 'pending'])
                ->where(function($query) {
                    $query->where('m.created_at', '<', now()->subHours(2))
                          ->orHavingRaw('COUNT(*) > 5');
                })
                ->exists();

        } catch (\Exception $e) {
            Log::error('Navigation notification refresh error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.navigation-chat-notification');
    }
}