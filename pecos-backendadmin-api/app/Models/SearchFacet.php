<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchFacet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'attribute_name',
        'options',
        'is_active',
        'is_collapsed',
        'sort_order',
        'max_options',
        'show_count',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'is_collapsed' => 'boolean',
        'sort_order' => 'integer',
        'max_options' => 'integer',
        'show_count' => 'boolean',
    ];

    /**
     * Available facet types.
     */
    public static function getTypes(): array
    {
        return [
            'category' => 'Category',
            'price_range' => 'Price Range',
            'brand' => 'Brand',
            'attribute' => 'Product Attribute',
            'rating' => 'Customer Rating',
            'availability' => 'Availability',
            'size' => 'Size',
            'color' => 'Color',
        ];
    }

    /**
     * Get active facets ordered by sort order.
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Scope for active facets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
