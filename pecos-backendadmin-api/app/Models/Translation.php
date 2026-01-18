<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'translation_key_id',
        'value',
        'is_reviewed',
        'translated_by',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
    ];

    /**
     * Get the language.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the translation key.
     */
    public function translationKey()
    {
        return $this->belongsTo(TranslationKey::class);
    }

    /**
     * Scope by language.
     */
    public function scopeForLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }

    /**
     * Scope unreviewed translations.
     */
    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    /**
     * Mark as reviewed.
     */
    public function markReviewed(string $reviewer = 'human'): bool
    {
        $this->is_reviewed = true;
        $this->translated_by = $reviewer;
        return $this->save();
    }

    /**
     * Get translation by group and key for a language.
     */
    public static function getTranslation(string $group, string $key, $languageId): ?string
    {
        $translationKey = TranslationKey::where('group', $group)
            ->where('key', $key)
            ->first();

        if (!$translationKey) {
            return null;
        }

        $translation = static::where('translation_key_id', $translationKey->id)
            ->where('language_id', $languageId)
            ->first();

        return $translation?->value;
    }

    /**
     * Set translation by group and key.
     */
    public static function setTranslation(
        string $group,
        string $key,
        $languageId,
        string $value,
        ?string $translatedBy = 'manual'
    ): self {
        $translationKey = TranslationKey::findOrCreateKey($group, $key);

        return static::updateOrCreate(
            [
                'translation_key_id' => $translationKey->id,
                'language_id' => $languageId,
            ],
            [
                'value' => $value,
                'translated_by' => $translatedBy,
            ]
        );
    }

    /**
     * Get all translations for a group and language.
     */
    public static function getGroupTranslations(string $group, $languageId): array
    {
        $keys = TranslationKey::where('group', $group)->get();
        $translations = [];

        foreach ($keys as $key) {
            $translation = static::where('translation_key_id', $key->id)
                ->where('language_id', $languageId)
                ->first();

            $translations[$key->key] = $translation?->value ?? '';
        }

        return $translations;
    }
}
