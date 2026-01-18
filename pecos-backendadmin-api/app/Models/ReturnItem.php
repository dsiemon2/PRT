<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'order_item_id',
        'product_upc',
        'product_name',
        'quantity',
        'unit_price',
        'refund_amount',
        'condition',
        'condition_notes',
        'restock',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'restock' => 'boolean',
    ];

    const CONDITION_UNOPENED = 'unopened';
    const CONDITION_LIKE_NEW = 'like_new';
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';
    const CONDITION_DAMAGED = 'damaged';
    const CONDITION_DEFECTIVE = 'defective';

    /**
     * Get the return request.
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    /**
     * Get the original order item.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * Get photos for this item.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ReturnPhoto::class, 'return_item_id');
    }

    /**
     * Calculate the refund amount based on condition.
     */
    public function calculateRefundAmount(): float
    {
        $percentage = match ($this->condition) {
            self::CONDITION_UNOPENED => 100,
            self::CONDITION_LIKE_NEW => 100,
            self::CONDITION_GOOD => 85,
            self::CONDITION_FAIR => 70,
            self::CONDITION_DAMAGED => 50,
            self::CONDITION_DEFECTIVE => 100,
            default => 100,
        };

        return ($this->unit_price * $this->quantity) * ($percentage / 100);
    }

    /**
     * Get all available conditions.
     */
    public static function getConditions(): array
    {
        return [
            self::CONDITION_UNOPENED => 'Unopened',
            self::CONDITION_LIKE_NEW => 'Like New',
            self::CONDITION_GOOD => 'Good',
            self::CONDITION_FAIR => 'Fair',
            self::CONDITION_DAMAGED => 'Damaged',
            self::CONDITION_DEFECTIVE => 'Defective',
        ];
    }

    /**
     * Check if item should be restocked.
     */
    public function shouldRestock(): bool
    {
        return $this->restock && in_array($this->condition, [
            self::CONDITION_UNOPENED,
            self::CONDITION_LIKE_NEW,
            self::CONDITION_GOOD,
        ]);
    }
}
