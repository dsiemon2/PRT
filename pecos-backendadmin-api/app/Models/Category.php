<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'CategoryCode';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Category',
        'ShrtDescription',
        'lngDescription',
        'image',
        'Directory',
        'CategoryCode',
        'sOrder',
        'Level',
        'IsBottom',
        'IsOrdered',
    ];

    protected $casts = [
        'CategoryCode' => 'integer',
        'sOrder' => 'integer',
        'Level' => 'integer',
        'IsBottom' => 'boolean',
        'IsOrdered' => 'boolean',
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'CategoryCode', 'CategoryCode');
    }

    /**
     * Scope for ordered categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sOrder', 'asc');
    }

    /**
     * Scope for bottom-level categories (with products).
     */
    public function scopeBottomLevel($query)
    {
        return $query->where('IsBottom', true);
    }
}
