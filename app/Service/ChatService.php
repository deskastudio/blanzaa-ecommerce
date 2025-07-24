<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChatService
{
    private const CACHE_TTL = 300; // 5 minutesliv
    private const SHORT_CACHE_TTL = 15; // 30 seconds for real-time data

    /**
     * Get conversations for admin dashboard with optimized queries
     */
    public function getAdminConversations(int $adminId, array $filters = []): Collection
    {
        $cacheKey = "admin_conversations_{$adminId}_" . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::SHORT_CACHE_TTL, function () use ($adminId, $filters) {
            $query = DB::table('chat_conversations as c')
                ->select([
                    'c.id',
                    'c.status', 
                    'c.priority',
                    'c.last_message_at',
                    'c.admin_id',
                    'c.created_at',
                    'u.id as user_id',
                    'u.name as user_name',
                    'u.email as user_email',
                    'u.avatar as user_avatar',
                    'u.is_online as user_online',
                    'a.name as admin_name',
                    DB::raw('(
                        SELECT COUNT(*) 
                        FROM chat_messages m 
                        WHERE m.conversation_id = c.id 
                        AND m.sender_id != ' . $adminId . ' 
                        AND m.is_read = 0
                    ) as unread_count'),
                    DB::raw('(
                        SELECT m2.message 
                        FROM chat_messages m2 
                        WHERE m2.conversation_id = c.id 
                        ORDER BY m2.created_at DESC 
                        LIMIT 1
                    ) as last_message'),
                    DB::raw('(
                        SELECT u2.name 
                        FROM chat_messages m3 
                        JOIN users u2 ON u2.id = m3.sender_id 
                        WHERE m3.conversation_id = c.id 
                        ORDER BY m3.created_at DESC 
                        LIMIT 1
                    ) as last_message_sender')
                ])
                ->join('users as u', 'u.id', '=', 'c.user_id')
                ->leftJoin('users as a', 'a.id', '=', 'c.admin_id')
                ->whereIn('c.status', $filters['status'] ?? ['active', 'pending']);

            // Apply filters
            if (!empty($filters['assigned_only'])) {
                $query->where('c.admin_id', $adminId);
            }

            if (!empty($filters['unassigned_only'])) {
                $query->whereNull('c.admin_id');
            }

            if (!empty($filters['priority'])) {
                $query->where('c.priority', $filters['priority']);
            }

            // Optimize ordering - prioritize assigned conversations
            $query->orderByRaw('CASE WHEN c.admin_id = ? THEN 0 ELSE 1 END', [$adminId])
                  ->orderBy('c.priority', 'desc')
                  ->orderBy('c.last_message_at', 'desc')
                  ->limit($filters['limit'] ?? 50);

            $results = $query->get();

            return $results->map(function ($conv) use ($adminId) {
                return (object) [
                    'id' => $conv->id,
                    'status' => $conv->status,
                    'priority' => $conv->priority ?? 'normal',
                    'last_message_at' => $conv->last_message_at ? Carbon::parse($conv->last_message_at) : null,
                    'created_at' => Carbon::parse($conv->created_at),
                    'admin_id' => $conv->admin_id,
                    'unread_count' => (int) $conv->unread_count,
                    'last_message' => $conv->last_message ? Str::limit($conv->last_message, 100) : null,
                    'last_message_sender' => $conv->last_message_sender,
                    'is_assigned_to_me' => $conv->admin_id == $adminId,
                    'user' => (object) [
                        'id' => $conv->user_id,
                        'name' => $conv->user_name,
                        'email' => $conv->user_email,
                        'avatar' => $conv->user_avatar ?? null,
                        'is_online' => (bool) ($conv->user_online ?? false),
                        'initials' => $this->getUserInitials($conv->user_name)
                    ],
                    'admin' => $conv->admin_name ? (object) ['name' => $conv->admin_name] : null
                ];
            });
        });
    }

    /**
     * Get messages for a specific conversation
     */
    public function getConversationMessages(int $conversationId, int $limit = 200): Collection
    {
        $cacheKey = "conversation_messages_{$conversationId}_{$limit}";
        
        return Cache::remember($cacheKey, self::SHORT_CACHE_TTL, function () use ($conversationId, $limit) {
            $messages = DB::table('chat_messages as m')
                ->select([
                    'm.id',
                    'm.message',
                    'm.sender_id',
                    'm.created_at',
                    'm.type',
                    'm.is_read',
                    'm.metadata',
                    'u.name as sender_name',
                    'u.avatar as sender_avatar'
                ])
                ->join('users as u', 'u.id', '=', 'm.sender_id')
                ->where('m.conversation_id', $conversationId)
                ->orderBy('m.created_at', 'asc')
                ->limit($limit)
                ->get();

            return $messages->map(function ($msg) {
                return (object) [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'sender_type' => $this->getSenderType($msg->sender_id),
                    'created_at' => Carbon::parse($msg->created_at),
                    'type' => $msg->type ?? 'text',
                    'is_read' => (bool) $msg->is_read,
                    'metadata' => $msg->metadata ? json_decode($msg->metadata, true) : null,
                    'time_ago' => Carbon::parse($msg->created_at)->diffForHumans(),
                    'sender' => (object) [
                        'name' => $msg->sender_name,
                        'avatar' => $msg->sender_avatar,
                        'initials' => $this->getUserInitials($msg->sender_name)
                    ]
                ];
            });
        });
    }

    /**
     * Send a message with transaction safety
     */
    public function sendMessage(array $data): array
    {
        try {
            $result = DB::transaction(function () use ($data) {
                // Insert message
                $messageId = DB::table('chat_messages')->insertGetId([
                    'conversation_id' => $data['conversation_id'],
                    'sender_id' => $data['sender_id'],
                    'message' => trim($data['message']),
                    'type' => $data['type'] ?? 'text',
                    'metadata' => isset($data['metadata']) ? json_encode($data['metadata']) : null,
                    'is_read' => $data['sender_type'] === 'admin', // Admin messages are auto-read
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update conversation
                $updateData = [
                    'last_message_at' => now(),
                    'updated_at' => now()
                ];

                // If admin is sending, assign conversation and set active
                if ($data['sender_type'] === 'admin') {
                    $updateData['admin_id'] = $data['sender_id'];
                    $updateData['status'] = 'active';
                }

                DB::table('chat_conversations')
                    ->where('id', $data['conversation_id'])
                    ->update($updateData);

                return [
                    'success' => true,
                    'message_id' => $messageId,
                    'timestamp' => now()
                ];
            });

            // Clear relevant caches
            $this->clearConversationCache($data['conversation_id']);
            
            // Broadcast event (if using broadcasting)
            $this->broadcastMessage($data['conversation_id'], $result['message_id']);

            return $result;

        } catch (\Exception $e) {
            Log::error('SendMessage failed: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send message'
            ];
        }
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead(int $conversationId, int $userId): int
    {
        try {
            $updated = DB::table('chat_messages')
                ->where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'updated_at' => now()
                ]);

            if ($updated > 0) {
                $this->clearConversationCache($conversationId);
                Log::info("Marked {$updated} messages as read", [
                    'conversation_id' => $conversationId,
                    'user_id' => $userId
                ]);
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('markMessagesAsRead failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Assign conversation to admin
     */
    public function assignConversation(int $conversationId, int $adminId): bool
    {
        try {
            $updated = DB::table('chat_conversations')
                ->where('id', $conversationId)
                ->where(function ($query) use ($adminId) {
                    $query->whereNull('admin_id')
                          ->orWhere('admin_id', '!=', $adminId);
                })
                ->update([
                    'admin_id' => $adminId,
                    'status' => 'active',
                    'updated_at' => now()
                ]);

            if ($updated > 0) {
                $this->clearConversationCache($conversationId);
                
                // Add system message
                $this->sendMessage([
                    'conversation_id' => $conversationId,
                    'sender_id' => $adminId,
                    'message' => 'Admin has joined the conversation',
                    'type' => 'system',
                    'sender_type' => 'admin'
                ]);

                Log::info("Conversation {$conversationId} assigned to admin {$adminId}");
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('assignConversation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Close conversation
     */
    public function closeConversation(int $conversationId, int $adminId, string $reason = null): bool
    {
        try {
            DB::transaction(function () use ($conversationId, $adminId, $reason) {
                // Add system message
                $message = $reason ? "Conversation closed: {$reason}" : 'Conversation closed by admin';
                
                $this->sendMessage([
                    'conversation_id' => $conversationId,
                    'sender_id' => $adminId,
                    'message' => $message,
                    'type' => 'system',
                    'sender_type' => 'admin'
                ]);

                // Update conversation status
                DB::table('chat_conversations')
                    ->where('id', $conversationId)
                    ->update([
                        'status' => 'closed',
                        'closed_at' => now(),
                        'updated_at' => now()
                    ]);
            });

            $this->clearConversationCache($conversationId);
            Log::info("Conversation {$conversationId} closed by admin {$adminId}");
            
            return true;

        } catch (\Exception $e) {
            Log::error('closeConversation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get conversation statistics
     */
    public function getAdminStats(int $adminId): array
    {
        $cacheKey = "admin_stats_{$adminId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($adminId) {
            return [
                'total_active' => DB::table('chat_conversations')
                    ->where('status', 'active')
                    ->count(),
                    
                'total_pending' => DB::table('chat_conversations')
                    ->where('status', 'pending')
                    ->count(),
                    
                'my_assigned' => DB::table('chat_conversations')
                    ->where('admin_id', $adminId)
                    ->whereIn('status', ['active', 'pending'])
                    ->count(),
                    
                'unread_total' => DB::table('chat_messages as m')
                    ->join('chat_conversations as c', 'c.id', '=', 'm.conversation_id')
                    ->where('m.sender_id', '!=', $adminId)
                    ->where('m.is_read', false)
                    ->whereIn('c.status', ['active', 'pending'])
                    ->count(),
                    
                'messages_today' => DB::table('chat_messages')
                    ->where('sender_id', $adminId)
                    ->whereDate('created_at', today())
                    ->count(),
                    
                'avg_response_time' => $this->getAverageResponseTime($adminId),
                
                'closed_today' => DB::table('chat_conversations')
                    ->where('admin_id', $adminId)
                    ->where('status', 'closed')
                    ->whereDate('closed_at', today())
                    ->count()
            ];
        });
    }

    /**
     * Get recent activity for admin
     */
    public function getRecentActivity(int $adminId, int $limit = 10): Collection
    {
        return DB::table('chat_messages as m')
            ->select([
                'm.id',
                'm.message',
                'm.created_at',
                'm.type',
                'c.id as conversation_id',
                'u.name as user_name'
            ])
            ->join('chat_conversations as c', 'c.id', '=', 'm.conversation_id')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->where('m.sender_id', $adminId)
            ->orderBy('m.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return (object) [
                    'id' => $activity->id,
                    'message' => Str::limit($activity->message, 50),
                    'created_at' => Carbon::parse($activity->created_at),
                    'time_ago' => Carbon::parse($activity->created_at)->diffForHumans(),
                    'type' => $activity->type,
                    'conversation_id' => $activity->conversation_id,
                    'user_name' => $activity->user_name
                ];
            });
    }

    /**
     * Search conversations
     */
    public function searchConversations(string $query, int $adminId): Collection
    {
        return DB::table('chat_conversations as c')
            ->select([
                'c.id',
                'c.status',
                'c.last_message_at',
                'u.name as user_name',
                'u.email as user_email'
            ])
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->where(function ($q) use ($query) {
                $q->where('u.name', 'LIKE', "%{$query}%")
                  ->orWhere('u.email', 'LIKE', "%{$query}%");
            })
            ->whereIn('c.status', ['active', 'pending'])
            ->orderBy('c.last_message_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Helper methods
     */
    private function getUserInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    private function getSenderType(int $senderId): string
    {
        // This should be improved to actually check user roles
        // For now, assuming admins have specific IDs or roles
        $adminIds = Cache::remember('admin_user_ids', 3600, function () {
            return DB::table('users')
                ->where('role', 'admin') // Adjust based on your user role system
                ->pluck('id')
                ->toArray();
        });

        return in_array($senderId, $adminIds) ? 'admin' : 'user';
    }

    private function getAverageResponseTime(int $adminId): float
    {
        // Calculate average response time in minutes
        // This is a simplified version - you might want to implement more sophisticated logic
        $responses = DB::table('chat_messages as m1')
            ->join('chat_messages as m2', function ($join) use ($adminId) {
                $join->on('m1.conversation_id', '=', 'm2.conversation_id')
                     ->whereRaw('m1.created_at > m2.created_at');
            })
            ->where('m1.sender_id', $adminId)
            ->where('m2.sender_id', '!=', $adminId)
            ->whereDate('m1.created_at', '>=', now()->subDays(7))
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, m2.created_at, m1.created_at)) as avg_time')
            ->first();

        return $responses->avg_time ? round($responses->avg_time, 1) : 0;
    }

    private function clearConversationCache(int $conversationId): void
    {
        $patterns = [
            "admin_conversations_*",
            "conversation_messages_{$conversationId}_*",
            "admin_stats_*"
        ];

        foreach ($patterns as $pattern) {
            Cache::flush(); // In production, use more specific cache clearing
        }
    }

    private function broadcastMessage(int $conversationId, int $messageId): void
    {
        // Implement broadcasting logic here if using WebSockets
        // For example, with Laravel Broadcasting:
        // broadcast(new MessageSent($conversationId, $messageId));
        
        Log::debug("Broadcasting message {$messageId} for conversation {$conversationId}");
    }

    /**
     * Bulk operations
     */
    public function assignMultipleConversations(array $conversationIds, int $adminId): int
    {
        try {
            $updated = DB::table('chat_conversations')
                ->whereIn('id', $conversationIds)
                ->whereNull('admin_id')
                ->update([
                    'admin_id' => $adminId,
                    'status' => 'active',
                    'updated_at' => now()
                ]);

            if ($updated > 0) {
                // Clear cache for all affected conversations
                foreach ($conversationIds as $convId) {
                    $this->clearConversationCache($convId);
                }
                
                Log::info("Bulk assigned {$updated} conversations to admin {$adminId}");
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Bulk assign failed: ' . $e->getMessage());
            return 0;
        }
    }

    public function closeMultipleConversations(array $conversationIds, int $adminId, string $reason = null): int
    {
        try {
            $updated = DB::transaction(function () use ($conversationIds, $adminId, $reason) {
                // Add system messages for each conversation
                foreach ($conversationIds as $convId) {
                    $this->sendMessage([
                        'conversation_id' => $convId,
                        'sender_id' => $adminId,
                        'message' => $reason ? "Conversation closed: {$reason}" : 'Conversation closed by admin (bulk action)',
                        'type' => 'system',
                        'sender_type' => 'admin'
                    ]);
                }

                // Update all conversations
                return DB::table('chat_conversations')
                    ->whereIn('id', $conversationIds)
                    ->update([
                        'status' => 'closed',
                        'closed_at' => now(),
                        'updated_at' => now()
                    ]);
            });

            // Clear cache
            foreach ($conversationIds as $convId) {
                $this->clearConversationCache($convId);
            }

            Log::info("Bulk closed {$updated} conversations by admin {$adminId}");
            return $updated;

        } catch (\Exception $e) {
            Log::error('Bulk close failed: ' . $e->getMessage());
            return 0;
        }
    }
}