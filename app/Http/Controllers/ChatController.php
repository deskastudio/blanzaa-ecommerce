<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    /**
     * Get/Create Conversation WITH Initial Messages - Single API Call
     */
  // REPLACE method getOrCreateConversation di ChatController dengan ini:
public function getOrCreateConversation(Request $request): JsonResponse
{
    $startTime = microtime(true);
    
    try {
        Log::info('🚀 getOrCreateConversation started');
        
        $userId = Auth::id();
        Log::info('👤 User ID: ' . $userId);
        
        if (!$userId) {
            Log::error('❌ No authenticated user');
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }
        
        // Try to get existing conversation first
        Log::info('🔍 Looking for existing conversation...');
        $conversation = DB::selectOne("
            SELECT id FROM chat_conversations 
            WHERE user_id = ? AND status = 'active' 
            LIMIT 1
        ", [$userId]);
        
        Log::info('📄 Existing conversation query completed');
        
        if (!$conversation) {
            Log::info('➕ Creating new conversation...');
            
            // Create with minimal data
            $conversationId = DB::table('chat_conversations')->insertGetId([
                'user_id' => $userId,
                'status' => 'active',
                'last_message_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('✅ New conversation created: ' . $conversationId);
        } else {
            $conversationId = $conversation->id;
            Log::info('✅ Using existing conversation: ' . $conversationId);
        }
        
        // Get recent messages with LIMIT for speed
        Log::info('📨 Fetching messages...');
        $messages = DB::select("
            SELECT 
                m.id,
                m.message,
                m.is_read,
                DATE_FORMAT(m.created_at, '%H:%i') as formatted_time,
                u.name as sender_name,
                u.email as sender_email
            FROM chat_messages m
            JOIN users u ON u.id = m.sender_id
            WHERE m.conversation_id = ?
            ORDER BY m.id DESC
            LIMIT 10
        ", [$conversationId]);
        
        Log::info('📨 Messages query completed, count: ' . count($messages));
        
        // Process messages in PHP (faster than multiple queries)
        $formatted = [];
        $unreadCount = 0;
        
        foreach (array_reverse($messages) as $msg) {
            $isFromAdmin = str_contains($msg->sender_email, 'admin');
            
            $formatted[] = [
                'id' => (int) $msg->id,
                'message' => $msg->message,
                'sender_name' => $msg->sender_name,
                'is_from_admin' => $isFromAdmin,
                'is_read' => (bool) $msg->is_read,
                'formatted_time' => $msg->formatted_time
            ];
            
            if ($isFromAdmin && !$msg->is_read) {
                $unreadCount++;
            }
        }
        
        $responseTime = round((microtime(true) - $startTime) * 1000, 1);
        Log::info("✅ getOrCreateConversation completed in {$responseTime}ms");
        
        return response()->json([
            'success' => true,
            'conversation_id' => $conversationId,
            'messages' => $formatted,
            'unread_count' => $unreadCount,
            'debug_time' => $responseTime
        ]);
        
    } catch (\Exception $e) {
        $errorTime = round((microtime(true) - $startTime) * 1000, 1);
        Log::error("❌ getOrCreateConversation error after {$errorTime}ms: " . $e->getMessage());
        Log::error("❌ Stack trace: " . $e->getTraceAsString());
        
        return response()->json([
            'success' => false, 
            'message' => 'Server error',
            'debug_time' => $errorTime
        ], 500);
    }
}
    
    /**
     * Send Message - Ultra Fast with Optimistic Response
     */
 public function sendMessage(Request $request): JsonResponse
{
    $startTime = microtime(true);
    
    try {
        // Ultra fast validation - no complex checks
        $message = $request->input('message');
        $conversationId = $request->input('conversation_id');
        
        if (!$message || !$conversationId) {
            return response()->json(['success' => false], 400);
        }
        
        $userId = Auth::id();
        $now = now();
        
        // FASTEST: Single raw insert without any additional queries
        DB::insert("
            INSERT INTO chat_messages (conversation_id, sender_id, message, type, is_read, created_at, updated_at)
            VALUES (?, ?, ?, 'text', 0, ?, ?)
        ", [$conversationId, $userId, $message, $now, $now]);
        
        // Get last insert ID
        $messageId = DB::getPdo()->lastInsertId();
        
        // ASYNC: Update conversation timestamp in background (fire and forget)
        DB::connection()->getPdo()->exec("
            UPDATE chat_conversations 
            SET last_message_at = '{$now}' 
            WHERE id = {$conversationId}
        ");
        
        $responseTime = round((microtime(true) - $startTime) * 1000, 1);
        
        // Return immediately with minimal data
        return response()->json([
            'success' => true,
            'data' => [
                'id' => (int) $messageId,
                'message' => $message,
                'sender_name' => Auth::user()->name ?? 'User',
                'is_from_admin' => false,
                'formatted_time' => $now->format('H:i'),
                'created_at' => $now->toISOString()
            ],
            'debug_time' => $responseTime
        ]);
        
    } catch (\Exception $e) {
        Log::error('Fast send error: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}
    
    /**
     * Get Messages - Only for Polling (Incremental)
     */
    public function getMessages(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $conversationId = (int) $request->input('conversation_id');
            $afterId = (int) $request->input('after_id', 0);
            
            if (!$conversationId || !$afterId) {
                return response()->json(['success' => true, 'messages' => []], 200);
            }
            
            // Only get NEW messages (for polling)
            $messages = DB::select("
                SELECT 
                    m.id,
                    m.message,
                    m.is_read,
                    DATE_FORMAT(m.created_at, '%H:%i') as formatted_time,
                    u.name as sender_name,
                    u.email as sender_email
                FROM chat_messages m
                JOIN users u ON u.id = m.sender_id
                WHERE m.conversation_id = ? AND m.id > ?
                ORDER BY m.id ASC
                LIMIT 20
            ", [$conversationId, $afterId]);
            
            $formatted = [];
            $newUnreadCount = 0;
            
            foreach ($messages as $msg) {
                $isFromAdmin = $this->isAdminByEmail($msg->sender_email);
                
                $formatted[] = [
                    'id' => (int) $msg->id,
                    'message' => $msg->message,
                    'sender_name' => $msg->sender_name,
                    'is_from_admin' => $isFromAdmin,
                    'is_read' => (bool) $msg->is_read,
                    'formatted_time' => $msg->formatted_time
                ];
                
                if ($isFromAdmin && !$msg->is_read) {
                    $newUnreadCount++;
                }
            }
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 1);
            
            return response()->json([
                'success' => true,
                'messages' => $formatted,
                'new_unread_count' => $newUnreadCount,
                'debug_time' => $responseTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat get messages error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Mark Messages as Read - Optimized
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $conversationId = (int) $request->input('conversation_id');
            $userId = Auth::id();
            
            if (!$conversationId || !$userId) {
                return response()->json(['success' => false], 400);
            }
            
            // Single optimized update
            $updated = DB::update("
                UPDATE chat_messages 
                SET is_read = 1 
                WHERE conversation_id = ? 
                AND sender_id != ? 
                AND is_read = 0
            ", [$conversationId, $userId]);
            
            return response()->json([
                'success' => true,
                'marked_count' => $updated
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat mark read error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Get Unread Count - Cached & Fast
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        try {
            $conversationId = (int) $request->input('conversation_id');
            $userId = Auth::id();
            
            if (!$conversationId || !$userId) {
                return response()->json(['count' => 0]);
            }
            
            // Cache key for unread count
            $cacheKey = "chat_unread_{$userId}_{$conversationId}";
            
            $count = Cache::remember($cacheKey, 30, function () use ($conversationId, $userId) {
                $result = DB::selectOne("
                    SELECT COUNT(*) as count 
                    FROM chat_messages 
                    WHERE conversation_id = ? 
                    AND sender_id != ? 
                    AND is_read = 0
                ", [$conversationId, $userId]);
                
                return $result->count ?? 0;
            });
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }
    
    /**
     * Helper: Check if user is admin (cached)
     */
    private function isAdmin($user): bool
    {
        if (!$user) return false;
        
        static $adminCache = [];
        $userId = $user->id;
        
        if (!isset($adminCache[$userId])) {
            $adminCache[$userId] = $user->is_admin ?? false || 
                                  $user->role === 'admin' || 
                                  str_contains($user->email ?? '', 'admin');
        }
        
        return $adminCache[$userId];
    }
    
    /**
     * Helper: Check if email indicates admin
     */
    private function isAdminByEmail(string $email): bool
    {
        static $emailCache = [];
        
        if (!isset($emailCache[$email])) {
            $emailCache[$email] = str_contains($email, 'admin') || 
                                 str_contains($email, 'support');
        }
        
        return $emailCache[$email];
    }
    
    /**
     * Bulk Operations for Admin - Send Reply
     */
    public function sendAdminReply(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $userId = Auth::id();
            $user = Auth::user();
            
            if (!$this->isAdmin($user)) {
                return response()->json(['success' => false], 403);
            }
            
            $conversationId = (int) $request->input('conversation_id');
            $message = trim($request->input('message', ''));
            
            if (!$conversationId || !$message) {
                return response()->json(['success' => false], 400);
            }
            
            $now = now();
            
            // Insert admin message
            $messageId = DB::table('chat_messages')->insertGetId([
                'conversation_id' => $conversationId,
                'sender_id' => $userId,
                'message' => $message,
                'type' => 'text',
                'is_read' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            // Update conversation
            DB::table('chat_conversations')
                ->where('id', $conversationId)
                ->update(['last_message_at' => $now]);
            
            // Clear cache
            $conversation = DB::selectOne("SELECT user_id FROM chat_conversations WHERE id = ?", [$conversationId]);
            if ($conversation) {
                Cache::forget("chat_unread_{$conversation->user_id}_{$conversationId}");
            }
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 1);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $messageId,
                    'message' => $message,
                    'sender_name' => $user->name,
                    'is_from_admin' => true,
                    'formatted_time' => $now->format('H:i'),
                    'created_at' => $now->toISOString()
                ],
                'debug_time' => $responseTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Admin reply error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}