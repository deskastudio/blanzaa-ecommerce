<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'type',
        'metadata',
        'is_read'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean'
    ];

    /**
     * Conversation that this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * User who sent this message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Check if message is from admin
     */
    public function isFromAdmin(): bool
    {
        // Assuming admin users have a specific role or you can customize this logic
        return $this->sender->hasRole('admin') ?? false;
    }

    /**
     * Check if message is from customer
     */
    public function isFromCustomer(): bool
    {
        return !$this->isFromAdmin();
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeFromUser($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Boot method to handle events
     */
    protected static function boot()
    {
        parent::boot();

        // Update conversation's last_message_at when new message is created
        static::created(function ($message) {
            $message->conversation->updateLastMessageTime();
        });
    }

    /**
     * Helper methods
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function getFormattedTimeAttribute()
    {
        return $this->getAttribute('created_at')?->format('H:i') ?? '';
    }

    public function getFormattedDateAttribute()
    {
        return $this->getAttribute('created_at')?->format('d M Y') ?? '';
    }
}