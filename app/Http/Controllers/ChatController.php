<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Check if user is admin (customize this logic)
     */
    private function isAdmin($user): bool
    {
        // Simple approach - check if user email contains 'admin' or has specific email
        return str_contains($user->email, 'admin') || $user->email === 'admin@example.com';
        
        // Or check by user table field if you have is_admin column:
        // return $user->is_admin ?? false;
        
        // Or use role package later:
        // return $user->hasRole('admin');
    }
    
    /**
     * Get or create conversation for current user
     */
    public function getOrCreateConversation(Request $request): JsonResponse
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
     * Send a message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'message' => 'required|string|max:1000'
        ]);
        
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($request->conversation_id);
        
        // Verify user owns this conversation or is admin
        if ($conversation->user_id !== $user->id && !$this->isAdmin($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Create message
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'type' => 'text',
            'is_read' => false
        ]);
        
        // Load sender relationship
        $message->load('sender');
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_name' => $message->sender->name,
                'is_from_admin' => $this->isAdmin($message->sender),
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'formatted_time' => $message->getAttribute('created_at')?->format('H:i') ?? ''
            ]
        ]);
    }
    
    /**
     * Get messages for a conversation
     */
    public function getMessages(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id'
        ]);
        
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($request->conversation_id);
        
        // Verify user owns this conversation or is admin
        if ($conversation->user_id !== $user->id && !$this->isAdmin($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get messages
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_name' => $message->sender->name,
                    'is_from_admin' => $this->isAdmin($message->sender),
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'formatted_time' => $message->getAttribute('created_at')?->format('H:i') ?? ''
                ];
            });
        
        // Mark messages as read for current user
        $conversation->markAsRead($user->id);
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'conversation' => [
                'id' => $conversation->id,
                'status' => $conversation->status,
                'last_message_at' => $conversation->last_message_at?->format('Y-m-d H:i:s')
            ]
        ]);
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