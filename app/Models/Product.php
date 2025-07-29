<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'compare_price',
        'stock_quantity',
        'min_stock_level',
        'weight',
        'dimensions',
        'brand',
        'warranty',
        'is_active',
        'is_featured',
        'meta_title',
        'image',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // Auto generate slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shoppingCarts()
    {
        return $this->hasMany(ShoppingCart::class);
    }

    // Accessors untuk gambar
    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first() 
            ?? $this->images()->first();
    }

    // NEW: Accessor untuk URL gambar utama dengan prioritas
    public function getImageUrlAttribute()
    {
        // Prioritas: kolom image -> primary image dari galeri -> null
        if ($this->attributes['image']) {
            return Storage::url($this->attributes['image']);
        }
        
        $primaryImage = $this->primary_image;
        return $primaryImage ? Storage::url($primaryImage->image_path) : null;
    }

    // NEW: Accessor untuk semua URL gambar
    public function getImageUrlsAttribute()
    {
        $urls = [];
        
        // Tambahkan gambar utama jika ada
        if ($this->attributes['image']) {
            $urls[] = Storage::url($this->attributes['image']);
        }
        
        // Tambahkan gambar dari galeri yang tidak primary (untuk menghindari duplikasi)
        foreach ($this->images()->ordered()->get() as $image) {
            $imageUrl = Storage::url($image->image_path);
            if (!in_array($imageUrl, $urls)) {
                $urls[] = $imageUrl;
            }
        }
        
        return $urls;
    }

    // NEW: Method untuk mendapatkan gambar yang akan ditampilkan di tabel
    public function getDisplayImageAttribute()
    {
        // Prioritas: kolom image -> primary image dari galeri -> placeholder
        return $this->attributes['image'] ?? $this->primary_image?->image_path;
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedComparePriceAttribute()
    {
        if (!$this->compare_price) return null;
        return 'Rp ' . number_format($this->compare_price, 0, ',', '.');
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->compare_price || $this->compare_price <= $this->price) {
            return 0;
        }
        
        return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getIsInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->min_stock_level && $this->stock_quantity > 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->min_stock_level) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
            default => 'Unknown',
        };
    }

    public function getStockStatusColorAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'in_stock' => 'success',
            default => 'gray',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function decrementStock($quantity = 1)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    public function incrementStock($quantity = 1)
    {
        $this->increment('stock_quantity', $quantity);
        return true;
    }

    public function setAsPrimary($imageId)
    {
        // Remove primary from all images
        $this->images()->update(['is_primary' => false]);
        
        // Set new primary
        $this->images()->where('id', $imageId)->update(['is_primary' => true]);
    }

    // NEW: Helper method untuk mendapatkan thumbnail
    public function getThumbnailAttribute()
    {
        $imagePath = $this->display_image;
        return $imagePath ? Storage::url($imagePath) : asset('images/placeholder-product.png');
    }

    // NEW: Helper method untuk cek apakah ada gambar
    public function hasImage(): bool
    {
        return !empty($this->attributes['image']) || $this->images()->exists();
    }

    // NEW: Helper method untuk mendapatkan total gambar
    public function getTotalImagesAttribute(): int
    {
        $count = 0;
        
        if ($this->attributes['image']) {
            $count++;
        }
        
        $count += $this->images()->count();
        
        return $count;
    }
}