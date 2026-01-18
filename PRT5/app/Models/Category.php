<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'CategoryCode';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'CategoryCode',
        'Category',
        'Description',
        'image',
        'is_featured',
        'display_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'display_order' => 'integer',
    ];

    // Relationships

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'CategoryCode', 'CategoryCode');
    }

    // Scopes

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('Category');
    }

    // Accessors

    public function getProductCountAttribute(): int
    {
        return $this->products()->count();
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            return asset('assets/' . $this->image);
        }
        return asset('assets/images/no-image.svg');
    }
}
