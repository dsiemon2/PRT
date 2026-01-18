<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRateHistory extends Model
{
    protected $table = 'exchange_rate_history';

    protected $fillable = [
        'currency_id',
        'rate',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the currency.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
