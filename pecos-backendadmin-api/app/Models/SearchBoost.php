<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchBoost extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_term',
        'product_id',
        'boost_value',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'boost_value' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get active boosts for a search term.
     */
    public static function getForTerm(string $term)
    {
        return static::where('search_term', strtolower(trim($term)))
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('boost_value')
            ->get();
    }

    /**
     * Check if boost is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at <= now()) {
            return false;
        }

        return true;
    }
}
