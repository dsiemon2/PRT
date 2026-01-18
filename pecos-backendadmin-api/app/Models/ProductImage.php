<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $table = 'product_images';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_path',
        'display_order',
        'is_primary',
        'alt_text',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the product that owns the image.
     * Note: product_id (double) references products3.ID
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }

    /**
     * Scope for primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for ordered images.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
