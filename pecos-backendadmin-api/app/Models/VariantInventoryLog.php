<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantInventoryLog extends Model
{
    protected $fillable = [
        'variant_id',
        'action',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'reason',
        'user_id',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getActions(): array
    {
        return [
            'add' => 'Stock Added',
            'remove' => 'Stock Removed',
            'adjust' => 'Stock Adjusted',
            'reserve' => 'Stock Reserved',
            'release' => 'Stock Released',
            'sold' => 'Sold',
            'returned' => 'Returned',
        ];
    }

    public function getActionLabelAttribute(): string
    {
        return self::getActions()[$this->action] ?? $this->action;
    }
}
