<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'language_id',
        'name',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the language.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Scope by language.
     */
    public function scopeForLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }

    /**
     * Get or create translation for a product.
     */
    public static function getOrCreateForProduct($productId, $languageId): self
    {
        return static::firstOrCreate(
            ['product_id' => $productId, 'language_id' => $languageId],
            ['name' => '']
        );
    }
}
