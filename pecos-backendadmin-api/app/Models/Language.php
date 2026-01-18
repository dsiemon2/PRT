<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'locale',
        'name',
        'native_name',
        'flag_icon',
        'direction',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get default language.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get translations for this language.
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Get product translations for this language.
     */
    public function productTranslations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    /**
     * Get category translations for this language.
     */
    public function categoryTranslations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    /**
     * Set this language as default.
     */
    public function setAsDefault(): bool
    {
        // Remove default from all languages
        static::where('is_default', true)->update(['is_default' => false]);

        // Set this as default
        $this->is_default = true;
        return $this->save();
    }

    /**
     * Get flag display (emoji or icon class).
     */
    public function getFlagDisplay(): string
    {
        return $this->flag_icon ?? '';
    }

    /**
     * Check if language is RTL.
     */
    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }
}
