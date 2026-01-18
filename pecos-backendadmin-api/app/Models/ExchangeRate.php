<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_id',
        'rate',
        'source',
        'fetched_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'fetched_at' => 'datetime',
    ];

    /**
     * Get the currency.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Create or update exchange rate for a currency.
     */
    public static function updateRate(int $currencyId, float $rate, string $source = 'manual'): self
    {
        $existing = static::where('currency_id', $currencyId)->first();

        // Store old rate in history
        if ($existing) {
            ExchangeRateHistory::create([
                'currency_id' => $currencyId,
                'rate' => $existing->rate,
                'source' => $existing->source,
                'recorded_at' => $existing->fetched_at ?? $existing->updated_at,
            ]);
        }

        return static::updateOrCreate(
            ['currency_id' => $currencyId],
            [
                'rate' => $rate,
                'source' => $source,
                'fetched_at' => now(),
            ]
        );
    }
}
