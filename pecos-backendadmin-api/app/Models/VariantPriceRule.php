<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantPriceRule extends Model
{
    protected $fillable = [
        'variant_id',
        'name',
        'rule_type',
        'min_quantity',
        'max_quantity',
        'customer_group',
        'price',
        'discount_percent',
        'starts_at',
        'expires_at',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public static function getRuleTypes(): array
    {
        return [
            'quantity_discount' => 'Quantity Discount',
            'customer_group' => 'Customer Group Price',
            'date_range' => 'Date Range Price',
        ];
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function calculatePrice(float $basePrice, int $quantity = 1): float
    {
        if (!$this->isCurrentlyActive()) {
            return $basePrice;
        }

        // Check quantity requirements
        if ($this->min_quantity && $quantity < $this->min_quantity) {
            return $basePrice;
        }
        if ($this->max_quantity && $quantity > $this->max_quantity) {
            return $basePrice;
        }

        // Apply discount
        if ($this->price !== null) {
            return (float) $this->price;
        }
        if ($this->discount_percent !== null) {
            return $basePrice * (1 - ($this->discount_percent / 100));
        }

        return $basePrice;
    }
}
