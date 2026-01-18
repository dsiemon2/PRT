<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'description',
        'is_html',
    ];

    protected $casts = [
        'is_html' => 'boolean',
    ];

    /**
     * Get translations for this key.
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Get translation for a specific language.
     */
    public function getTranslation($languageId)
    {
        return $this->translations()->where('language_id', $languageId)->first();
    }

    /**
     * Get the full key identifier (group.key).
     */
    public function getFullKey(): string
    {
        return $this->group . '.' . $this->key;
    }

    /**
     * Scope by group.
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get or create a translation key.
     */
    public static function findOrCreateKey(string $group, string $key, ?string $description = null): self
    {
        return static::firstOrCreate(
            ['group' => $group, 'key' => $key],
            ['description' => $description]
        );
    }
}
