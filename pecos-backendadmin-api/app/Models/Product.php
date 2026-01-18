<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products3';
    // Using ID as primary key since order_items and wishlists reference by integer ID
    // ID is stored as double in MySQL but functions like an integer
    protected $primaryKey = 'ID';
    public $incrementing = false;
    protected $keyType = 'string'; // Use string to handle double type correctly
    public $timestamps = false;

    protected $fillable = [
        'UPC',
        'ItemNumber',
        'ShortDescription',
        'LngDescription',
        'UnitPrice',
        'QTY',
        'Image',
        'CategoryCode',
        'sOrder',
        'ItemSize',
        'color',
        'material',
        'stock_quantity',
        'reserved_quantity',
        'reorder_point',
        'reorder_quantity',
        'cost_price',
        'last_restock_date',
        'track_inventory',
        'allow_backorder',
        'low_stock_threshold',
        'preferred_supplier_id',
        'last_supplier_id',
        'last_purchase_date',
        'last_purchase_cost',
        'meta_title',
        'meta_description',
        'is_deleted',
        'deleted_at',
    ];

    protected $casts = [
        'UnitPrice' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'CategoryCode' => 'integer',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'reorder_point' => 'integer',
        'reorder_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
        'last_restock_date' => 'datetime',
        'preferred_supplier_id' => 'integer',
        'last_supplier_id' => 'integer',
        'last_purchase_date' => 'date',
        'last_purchase_cost' => 'decimal:2',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Accessors for consistent API response
    public function getDescriptionAttribute()
    {
        return $this->ShortDescription;
    }

    public function getPriceAttribute()
    {
        return $this->UnitPrice;
    }

    public function getQtyAvailAttribute()
    {
        return $this->stock_quantity ?? $this->QTY ?? 0;
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'CategoryCode', 'CategoryCode');
    }

    /**
     * Get the images for the product.
     * Note: product_images.product_id is double type, matches products3.ID
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'ID');
    }

    /**
     * Get the reviews for the product.
     * Note: product_reviews.product_id is varchar(50), matches products3.UPC
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'UPC');
    }

    /**
     * Get the preferred supplier for the product.
     */
    public function preferredSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id', 'id');
    }

    /**
     * Get the last supplier for the product.
     */
    public function lastSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'last_supplier_id', 'id');
    }

    /**
     * Get the inventory transactions for the product.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'product_id', 'ID');
    }

    /**
     * Get the stock alerts for the product.
     */
    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class, 'product_id', 'ID');
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'ID');
    }

    /**
     * Get the wishlist items for this product.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'ID');
    }

    /**
     * Get the purchase order items for this product.
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id', 'ID');
    }

    /**
     * Get the history/change log for this product.
     */
    public function history(): HasMany
    {
        return $this->hasMany(ProductHistory::class, 'product_id', 'ID');
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        $qty = $this->stock_quantity ?? $this->QTY ?? 0;
        if (is_string($qty)) {
            $qty = (int) $qty;
        }
        return $qty > 0 || $this->allow_backorder;
    }

    /**
     * Check if product is low on stock.
     */
    public function isLowStock(): bool
    {
        $qty = $this->stock_quantity ?? $this->QTY ?? 0;
        if (is_string($qty)) {
            $qty = (int) $qty;
        }
        $threshold = $this->low_stock_threshold ?? $this->reorder_point ?? 10;
        return $qty <= $threshold;
    }

    /**
     * Scope for active products (all products are active in products3).
     */
    public function scopeActive($query)
    {
        return $query;
    }

    /**
     * Scope for in-stock products.
     * Note: QTY is varchar in database, so we cast it to compare numerically
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('stock_quantity', '>', 0)
              ->orWhereRaw("CAST(QTY AS SIGNED) > 0")
              ->orWhere('allow_backorder', true);
        });
    }
}
