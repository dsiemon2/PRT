<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'language_id',
        'name',
        'description',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get the category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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
     * Get or create translation for a category.
     */
    public static function getOrCreateForCategory($categoryId, $languageId): self
    {
        return static::firstOrCreate(
            ['category_id' => $categoryId, 'language_id' => $languageId],
            ['name' => '']
        );
    }
}
