<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'attribute_type_id',
        'value',
        'label',
        'swatch_value',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attributeType(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeType::class, 'attribute_type_id');
    }

    public function variantAttributes(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class, 'attribute_value_id');
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->label ?? $this->value;
    }
}
