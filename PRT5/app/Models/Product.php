<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products3';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'UPC',
        'ItemNumber',
        'ShortDescription',
        'LongDescription',
        'UnitPrice',
        'Cost',
        'CategoryCode',
        'Image',
        'stock_quantity',
        'reserved_quantity',
        'low_stock_threshold',
        'reorder_level',
        'track_inventory',
        'allow_backorders',
    ];

    protected $casts = [
        'UnitPrice' => 'decimal:2',
        'Cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'reorder_level' => 'integer',
        'track_inventory' => 'boolean',
        'allow_backorders' => 'boolean',
    ];

    // Relationships

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'CategoryCode', 'CategoryCode');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'ID');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'UPC');
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class, 'product_id', 'ID');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'product_id', 'ID');
    }

    // Scopes

    public function scopeInStock($query)
    {
        // Use available quantity (stock - reserved)
        return $query->whereRaw('(stock_quantity - COALESCE(reserved_quantity, 0)) > 0');
    }

    public function scopeOutOfStock($query)
    {
        // Use available quantity (stock - reserved), only for tracked products
        return $query->where(function($q) {
            $q->where('track_inventory', true)->orWhereNull('track_inventory');
        })->whereRaw('(stock_quantity - COALESCE(reserved_quantity, 0)) <= 0');
    }

    public function scopeLowStock($query)
    {
        // Use available quantity and default threshold of 5, only for tracked products
        return $query->where(function($q) {
            $q->where('track_inventory', true)->orWhereNull('track_inventory');
        })->whereRaw('(stock_quantity - COALESCE(reserved_quantity, 0)) > 0')
          ->whereRaw('(stock_quantity - COALESCE(reserved_quantity, 0)) <= COALESCE(low_stock_threshold, 5)');
    }

    public function scopeTracked($query)
    {
        return $query->where(function($q) {
            $q->where('track_inventory', true)->orWhereNull('track_inventory');
        });
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('ShortDescription', 'like', "%{$term}%")
              ->orWhere('ItemNumber', 'like', "%{$term}%")
              ->orWhere('UPC', 'like', "%{$term}%");
        });
    }

    // Accessors

    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function getStockStatusAttribute(): string
    {
        $available = $this->availableQuantity;
        $threshold = $this->low_stock_threshold ?? 5;

        if ($available <= 0) {
            return 'out_of_stock';
        }
        if ($available <= $threshold) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->UnitPrice, 2);
    }

    public function getPrimaryImageAttribute(): ?string
    {
        // First check product_images table
        $primaryImage = $this->images()->where('is_primary', true)->first();
        if ($primaryImage) {
            return asset('assets/' . $primaryImage->image_path);
        }

        // Fall back to first image in product_images
        $firstImage = $this->images()->orderBy('display_order')->first();
        if ($firstImage) {
            return asset('assets/' . $firstImage->image_path);
        }

        // Fall back to Image column in products3 table
        if (!empty($this->Image)) {
            return asset('assets/' . $this->Image);
        }

        return asset('assets/images/no-image.svg');
    }
}
