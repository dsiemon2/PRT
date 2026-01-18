<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'compare_price',
        'cost',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'weight_unit',
        'length',
        'width',
        'height',
        'dimension_unit',
        'barcode',
        'is_active',
        'track_inventory',
        'allow_backorder',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'is_active' => 'boolean',
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class, 'variant_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VariantImage::class, 'variant_id')->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(VariantImage::class, 'variant_id')->where('is_primary', true);
    }

    public function priceRules(): HasMany
    {
        return $this->hasMany(VariantPriceRule::class, 'variant_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(VariantInventoryLog::class, 'variant_id')->orderBy('created_at', 'desc');
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->price ?? $this->product->price ?? 0;
    }

    public function getIsInStockAttribute(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        return $this->stock_quantity > 0 || $this->allow_backorder;
    }

    public function getIsLowStockAttribute(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        $threshold = $this->low_stock_threshold ?? 5;
        return $this->stock_quantity <= $threshold && $this->stock_quantity > 0;
    }

    public function getAttributesStringAttribute(): string
    {
        return $this->attributeValues()
            ->with(['attributeType', 'attributeValue'])
            ->get()
            ->map(fn($av) => $av->attributeValue->display_label)
            ->implode(' / ');
    }

    public function adjustStock(int $quantity, string $action, ?string $reason = null, ?string $referenceType = null, ?int $referenceId = null, ?int $userId = null): void
    {
        $before = $this->stock_quantity;

        $this->stock_quantity += $quantity;
        $this->save();

        VariantInventoryLog::create([
            'variant_id' => $this->id,
            'action' => $action,
            'quantity_change' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $this->stock_quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reason' => $reason,
            'user_id' => $userId,
        ]);
    }
}
