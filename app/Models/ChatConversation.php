<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'product_id',
        'status',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    /**
     * User yang memulai conversation (customer)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin yang handle conversation
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Product yang terkait dengan conversation (optional)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * All messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * Get messages ordered by creation time
     */
    public function getOrderedMessages()
    {
        return $this->messages()->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get latest message in conversation
     */
    public function getLatestMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get unread messages count for specific user
     */
    public function getUnreadCountForUser(?int $userId = null): int
    {
        $userId = $userId ?? $this->getAttribute('user_id');
        
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get unread messages count for admin
     */
    public function getUnreadCountForAdmin(): int
    {
        return $this->messages()
            ->where('sender_id', $this->getAttribute('user_id'))
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get unread messages for specific user
     */
    public function getUnreadMessagesForUser(?int $userId = null)
    {
        $userId = $userId ?? $this->getAttribute('user_id');
        
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->get();
    }

    /**
     * Get unread messages for admin
     */
    public function getUnreadMessagesForAdmin()
    {
        return $this->messages()
            ->where('sender_id', $this->getAttribute('user_id'))
            ->where('is_read', false)
            ->get();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Helper methods
     */
    public function markAsRead($userId)
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }
}