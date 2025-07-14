<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',        // GANTI dari 'total' ke 'total_amount'
        'shipping_cost',
        'tax_amount',          // GANTI dari 'tax' ke 'tax_amount'
        'discount_amount',
        'customer_name',       // TAMBAH
        'customer_email',      // TAMBAH
        'customer_phone',      // TAMBAH
        'shipping_address',
        'shipping_city',       // TAMBAH
        'shipping_postal_code', // TAMBAH
        'shipping_province',   // TAMBAH
        'payment_method',
        'payment_status',
        'payment_proof',
        'notes',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';      // TAMBAH sesuai migration
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_PENDING_VERIFICATION = 'pending_verification'; // TAMBAH INI

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get formatted total - ACCESSOR untuk backward compatibility
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Accessor untuk 'total' agar code lama tetap jalan
     */
    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    /**
     * Accessor untuk 'tax' agar code lama tetap jalan
     */
    public function getTaxAttribute()
    {
        return $this->tax_amount;
    }

    /**
     * Accessor untuk 'subtotal' - hitung dari total_amount - tax - shipping
     */
    public function getSubtotalAttribute()
    {
        return $this->total_amount - $this->tax_amount - $this->shipping_cost;
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get order status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_SHIPPED => 'bg-purple-100 text-purple-800',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get payment status badge class
     */
    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::PAYMENT_STATUS_PENDING_VERIFICATION => 'bg-blue-100 text-blue-800', // TAMBAH INI
            self::PAYMENT_STATUS_PAID => 'bg-green-100 text-green-800',
            self::PAYMENT_STATUS_FAILED => 'bg-red-100 text-red-800',
            self::PAYMENT_STATUS_REFUNDED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getProgressPercentage(): int
{
    return match($this->status) {
        self::STATUS_PENDING => 25,
        self::STATUS_CONFIRMED => 50,
        self::STATUS_PROCESSING => 75,
        self::STATUS_SHIPPED => 90,
        self::STATUS_DELIVERED => 100,
        self::STATUS_CANCELLED => 0,
        default => 0
    };
}

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_PROCESSING]);
    }

    /**
     * Scope for user orders
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    // TAMBAHKAN methods ini di Order.php model:

/**
 * Check if payment proof is uploaded
 */
public function hasPaymentProof(): bool
{
    return !empty($this->payment_proof);
}

/**
 * Get effective payment status - SMART DETECTION
 */
public function getEffectivePaymentStatusAttribute(): string
{
    // Jika payment proof ada tapi status masih pending = sedang verifikasi
    if ($this->hasPaymentProof() && $this->payment_status === 'pending') {
        return 'pending_verification';
    }
    
    return $this->payment_status;
}

/**
 * Check if payment is under verification
 */
public function isPaymentUnderVerification(): bool
{
    return $this->hasPaymentProof() && $this->payment_status === 'pending';
}

/**
 * Get payment status label for display
 */
public function getPaymentStatusLabelAttribute(): string
{
    $status = $this->effective_payment_status;
    
    return match($status) {
        'pending' => $this->hasPaymentProof() ? 'Pending Verification' : 'Pending Payment',
        'pending_verification' => 'Pending Verification',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        default => ucfirst($status)
    };
}

/**
 * Updated canUploadPaymentProof method
 */
public function canUploadPaymentProof(): bool
{
    return $this->payment_method === 'bank_transfer' && 
           $this->payment_status === self::PAYMENT_STATUS_PENDING && 
           !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_DELIVERED]);
}
}