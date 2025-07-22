<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Send Message - Simple & Fast
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $userId = Auth::id();
            $conversationId = (int) $request->input('conversation_id');
            $message = trim($request->input('message', ''));
            
            // Basic validation
            if (!$conversationId || !$message || !$userId) {
                return response()->json(['success' => false], 400);
            }
            
            $now = now();
            
            // Single insert with minimal data
            $messageId = DB::table('chat_messages')->insertGetId([
                'conversation_id' => $conversationId,
                'sender_id' => $userId,
                'message' => $message,
                'type' => 'text',
                'is_read' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            // Update conversation timestamp
            DB::table('chat_conversations')
                ->where('id', $conversationId)
                ->update(['last_message_at' => $now]);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 1);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $messageId,
                    'message' => $message,
                    'sender_name' => Auth::user()->name ?? 'User',
                    'is_from_admin' => str_contains(Auth::user()->email ?? '', 'admin'),
                    'formatted_time' => $now->format('H:i'),
                    'debug_time' => $responseTime
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat send error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Get Messages - Incremental Loading
     */
    public function getMessages(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $conversationId = (int) $request->input('conversation_id');
            $afterId = (int) $request->input('after_id', 0);
            
            if (!$conversationId) {
                return response()->json(['success' => false], 400);
            }
            
            // Simple query - uses idx_conv_msg_id index
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
                LIMIT 50
            ", [$conversationId, $afterId]);
            
            // Format response
            $formatted = [];
            foreach ($messages as $msg) {
                $formatted[] = [
                    'id' => (int) $msg->id,
                    'message' => $msg->message,
                    'sender_name' => $msg->sender_name,
                    'is_from_admin' => str_contains($msg->sender_email, 'admin'),
                    'is_read' => (bool) $msg->is_read,
                    'formatted_time' => $msg->formatted_time
                ];
            }
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 1);
            
            return response()->json([
                'success' => true,
                'messages' => $formatted,
                'debug_time' => $responseTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat get messages error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Get/Create Conversation - Simple
     */
    public function getOrCreateConversation(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            // Find existing conversation
            $conversation = DB::selectOne("
                SELECT id FROM chat_conversations 
                WHERE user_id = ? AND status = 'active' 
                ORDER BY id DESC 
                LIMIT 1
            ", [$userId]);
            
            if (!$conversation) {
                // Create new conversation
                $conversationId = DB::table('chat_conversations')->insertGetId([
                    'user_id' => $userId,
                    'status' => 'active',
                    'last_message_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $conversationId = $conversation->id;
            }
            
            return response()->json([
                'success' => true,
                'conversation_id' => $conversationId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat conversation error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Mark Messages as Read - Simple
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $conversationId = (int) $request->input('conversation_id');
            $userId = Auth::id();
            
            if (!$conversationId || !$userId) {
                return response()->json(['success' => false], 400);
            }
            
            // Mark unread messages as read - uses idx_chat_messages_conv_sender_read
            DB::statement("
                UPDATE chat_messages 
                SET is_read = 1 
                WHERE conversation_id = ? 
                AND sender_id != ? 
                AND is_read = 0
            ", [$conversationId, $userId]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Chat mark read error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    /**
     * Get Unread Count - Simple
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        try {
            $conversationId = (int) $request->input('conversation_id');
            $userId = Auth::id();
            
            if (!$conversationId || !$userId) {
                return response()->json(['count' => 0]);
            }
            
            // Count unread - uses idx_chat_messages_conv_sender_read
            $count = DB::selectOne("
                SELECT COUNT(*) as count 
                FROM chat_messages 
                WHERE conversation_id = ? 
                AND sender_id != ? 
                AND is_read = 0
            ", [$conversationId, $userId]);
            
            return response()->json(['count' => $count->count ?? 0]);
            
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }
}