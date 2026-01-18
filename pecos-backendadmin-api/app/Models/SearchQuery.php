<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'customer_id',
        'results_count',
        'has_results',
        'filters_applied',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'results_count' => 'integer',
        'has_results' => 'boolean',
        'filters_applied' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Log a search query.
     */
    public static function log(
        string $query,
        int $resultsCount,
        ?int $customerId = null,
        ?array $filters = null
    ): self {
        return static::create([
            'query' => $query,
            'customer_id' => $customerId,
            'results_count' => $resultsCount,
            'has_results' => $resultsCount > 0,
            'filters_applied' => $filters,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get zero-result queries.
     */
    public static function getZeroResultQueries(int $limit = 50)
    {
        return static::select('query')
            ->selectRaw('COUNT(*) as count')
            ->where('has_results', false)
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }
}
