<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopularSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'search_count',
        'click_count',
        'conversion_rate',
        'is_featured',
    ];

    protected $casts = [
        'search_count' => 'integer',
        'click_count' => 'integer',
        'conversion_rate' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    /**
     * Increment search count for a query.
     */
    public static function incrementSearch(string $query): self
    {
        return static::updateOrCreate(
            ['query' => strtolower(trim($query))],
            ['search_count' => \DB::raw('search_count + 1')]
        );
    }

    /**
     * Get top popular searches.
     */
    public static function getTop(int $limit = 10)
    {
        return static::orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured searches.
     */
    public static function getFeatured(int $limit = 5)
    {
        return static::where('is_featured', true)
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }
}
