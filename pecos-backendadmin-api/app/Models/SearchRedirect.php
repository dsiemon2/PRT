<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchRedirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_term',
        'redirect_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Find redirect for a search term.
     */
    public static function findRedirect(string $term): ?string
    {
        $redirect = static::where('is_active', true)
            ->whereRaw('LOWER(search_term) = ?', [strtolower(trim($term))])
            ->first();

        return $redirect?->redirect_url;
    }
}
