<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributeType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'display_type',
        'is_visible',
        'is_variation',
        'is_filterable',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_variation' => 'boolean',
        'is_filterable' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_type_id')->orderBy('sort_order');
    }

    public function activeValues(): HasMany
    {
        return $this->values()->where('is_active', true);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_assignments', 'attribute_type_id', 'product_id')
            ->withPivot('is_visible', 'sort_order')
            ->withTimestamps();
    }

    public static function getDisplayTypes(): array
    {
        return [
            'dropdown' => 'Dropdown Select',
            'swatch' => 'Color/Image Swatch',
            'radio' => 'Radio Buttons',
            'buttons' => 'Button Group',
        ];
    }
}
