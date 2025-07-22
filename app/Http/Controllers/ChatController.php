<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Check if user is admin (customize this logic)
     */
    private function isAdmin($user): bool
    {
        // Simple approach - check if user email contains 'admin' or has specific email
        return str_contains($user->email, 'admin') || $user->email === 'admin@example.com';
    }
    
    /**
     * FINAL OPTIMIZED Send Message - Zero overhead
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            // Minimal validation
            $conversationId = (int) $request->get('conversation_id');
            $messageText = trim($request->get('message', ''));
            
            if (!$conversationId || empty($messageText) || strlen($messageText) > 1000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid input'
                ], 400);
            }
            
            $user = Auth::user();
            
            // Skip rate limiting for now - add back later if needed
            /*
            $rateLimitKey = "user_msg_rate_{$user->id}";
            $messageCount = Cache::get($rateLimitKey, 0);
            if ($messageCount >= 20) {
                return response()->json(['success' => false, 'message' => 'Rate limited'], 429);
            }
            */
            
            // Minimal conversation check
            $conversationExists = DB::table('chat_conversations')
                ->where('id', $conversationId)
                ->where('user_id', $user->id)
                ->exists();
                
            if (!$conversationExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }
            
            // Direct DB insert - fastest way
            $messageId = DB::table('chat_messages')->insertGetId([
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'message' => $messageText,
                'type' => 'text',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Update conversation timestamp
            DB::table('chat_conversations')
                ->where('id', $conversationId)
                ->update(['updated_at' => now()]);
            
            $totalTime = microtime(true) - $startTime;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $messageId,
                    'message' => $messageText,
                    'sender_name' => $user->name,
                    'is_from_admin' => $this->isAdmin($user),
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'formatted_time' => now()->format('H:i'),
                    'debug_time' => $totalTime
                ]
            ]);
            
        } catch (\Exception $e) {
            $totalTime = microtime(true) - $startTime;
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to send message',
                'debug_time' => $totalTime,
                'error' => $e->getMessage() // Only for debugging, remove in production
            ], 500);
        }
    }
    
    /**
     * FINAL OPTIMIZED Get Messages - Zero overhead
     */
    public function getMessages(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $conversationId = (int) $request->get('conversation_id');
            $afterId = (int) $request->get('after_id', 0);
            
            if (!$conversationId) {
                return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
            }
            
            // Direct SQL query for maximum speed
            $messages = DB::select("
                SELECT 
                    m.id,
                    m.message,
                    m.created_at,
                    u.name as sender_name,
                    u.email as sender_email
                FROM chat_messages m
                JOIN users u ON u.id = m.sender_id
                WHERE m.conversation_id = ? 
                AND m.id > ?
                ORDER BY m.created_at ASC
                LIMIT 50
            ", [$conversationId, $afterId]);
            
            // Format response - optimized
            $formattedMessages = [];
            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_name' => $message->sender_name,
                    'is_from_admin' => str_contains($message->sender_email, 'admin'),
                    'formatted_time' => \Carbon\Carbon::parse($message->created_at)->diffForHumans()
                ];
            }
            
            $totalTime = microtime(true) - $startTime;
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages,
                'debug_time' => $totalTime
            ]);
            
        } catch (\Exception $e) {
            $totalTime = microtime(true) - $startTime;
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to load messages',
                'debug_time' => $totalTime,
                'error' => $e->getMessage() // Only for debugging
            ], 500);
        }
    }
    
    /**
     * Get or create conversation - FINAL OPTIMIZED
     */
    public function getOrCreateConversation(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $user = Auth::user();
            
            // Direct SQL for speed
            $conversation = DB::selectOne("
                SELECT id FROM chat_conversations 
                WHERE user_id = ? AND status = 'active' 
                ORDER BY updated_at DESC 
                LIMIT 1
            ", [$user->id]);
            
            if (!$conversation) {
                $conversationId = DB::table('chat_conversations')->insertGetId([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'last_message_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $conversationId = $conversation->id;
            }
            
            $totalTime = microtime(true) - $startTime;
            
            return response()->json([
                'success' => true,
                'conversation_id' => $conversationId,
                'debug_time' => $totalTime
            ]);
            
        } catch (\Exception $e) {
            $totalTime = microtime(true) - $startTime;
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to create conversation',
                'debug_time' => $totalTime,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get or create conversation for current user
     */
    public function getOrCreateConversation_OLD(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user already has an active conversation
        $conversation = ChatConversation::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
        
        // Create new conversation if none exists
        if (!$conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'status' => 'active',
                'last_message_at' => now()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'status' => $conversation->status
        ]);
    }
    
    /**
     * Mark conversation as read (for admin)
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id'
        ]);
        
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($request->conversation_id);
        
        // Mark as read
        $conversation->markAsRead($user->id);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get conversations for admin (Filament)
     */
    public function getConversationsForAdmin(): JsonResponse
    {
        // Only allow admin users
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $conversations = ChatConversation::with(['user'])
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('is_read', false)
                      ->whereHas('sender', function($q) {
                          $q->where('id', '!=', Auth::id());
                      });
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) {
                $latestMessage = $conversation->getLatestMessage();
                
                return [
                    'id' => $conversation->id,
                    'user_name' => $conversation->user->name,
                    'user_email' => $conversation->user->email,
                    'status' => $conversation->status,
                    'unread_count' => $conversation->unread_count,
                    'latest_message' => $latestMessage ? [
                        'message' => $latestMessage->message,
                        'created_at' => $latestMessage->created_at->format('Y-m-d H:i:s')
                    ] : null,
                    'last_message_at' => $conversation->last_message_at?->format('Y-m-d H:i:s')
                ];
            });
        
        return response()->json([
            'success' => true,
            'conversations' => $conversations
        ]);
    }
    
    /**
     * Close conversation (admin only)
     */
    public function closeConversation(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id'
        ]);
        
        if (!$this->isAdmin(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $conversation = ChatConversation::findOrFail($request->conversation_id);
        $conversation->update(['status' => 'closed']);
        
        return response()->json(['success' => true]);
    }
}