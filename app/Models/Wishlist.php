<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id'
    ];

    /**
     * Get the user that owns the wishlist item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for user wishlist
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if product is in user's wishlist
     */
    public static function isInWishlist($userId, $productId): bool
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->exists();
    }

    /**
     * Add product to wishlist
     */
    public static function addToWishlist($userId, $productId): bool
    {
        if (!static::isInWishlist($userId, $productId)) {
            static::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return true;
        }
        return false;
    }

    /**
     * Remove product from wishlist
     */
    public static function removeFromWishlist($userId, $productId): bool
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete() > 0;
    }

    /**
     * Toggle product in wishlist
     */
    public static function toggleWishlist($userId, $productId): bool
    {
        if (static::isInWishlist($userId, $productId)) {
            static::removeFromWishlist($userId, $productId);
            return false; // Removed
        } else {
            static::addToWishlist($userId, $productId);
            return true; // Added
        }
    }
}