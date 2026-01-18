<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchSynonym extends Model
{
    use HasFactory;

    protected $fillable = [
        'term',
        'synonyms',
        'is_bidirectional',
        'is_active',
    ];

    protected $casts = [
        'is_bidirectional' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get synonyms as array.
     */
    public function getSynonymsArray(): array
    {
        return array_map('trim', explode(',', $this->synonyms));
    }

    /**
     * Find synonyms for a term.
     */
    public static function findSynonyms(string $term): array
    {
        $synonyms = [];
        $term = strtolower(trim($term));

        // Direct match
        $record = static::where('is_active', true)
            ->whereRaw('LOWER(term) = ?', [$term])
            ->first();

        if ($record) {
            $synonyms = $record->getSynonymsArray();
        }

        // Bidirectional match
        $bidirectional = static::where('is_active', true)
            ->where('is_bidirectional', true)
            ->whereRaw('LOWER(synonyms) LIKE ?', ['%' . $term . '%'])
            ->get();

        foreach ($bidirectional as $record) {
            $synonyms[] = $record->term;
            $synonyms = array_merge($synonyms, $record->getSynonymsArray());
        }

        // Remove original term and duplicates
        $synonyms = array_filter(array_unique($synonyms), fn($s) => strtolower($s) !== $term);

        return array_values($synonyms);
    }
}
