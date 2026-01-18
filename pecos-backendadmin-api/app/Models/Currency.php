<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'decimal_places',
        'decimal_separator',
        'thousand_separator',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the current exchange rate.
     */
    public function exchangeRate(): HasOne
    {
        return $this->hasOne(ExchangeRate::class)->latest();
    }

    /**
     * Get all exchange rates.
     */
    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class);
    }

    /**
     * Get exchange rate history.
     */
    public function rateHistory(): HasMany
    {
        return $this->hasMany(ExchangeRateHistory::class);
    }

    /**
     * Get product prices for this currency.
     */
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductCurrencyPrice::class);
    }

    /**
     * Scope for active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered currencies.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the default currency.
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get the current exchange rate value.
     */
    public function getCurrentRate(): float
    {
        return $this->exchangeRate?->rate ?? 1.0;
    }

    /**
     * Convert an amount from base currency to this currency.
     */
    public function convertFromBase(float $amount): float
    {
        return $amount * $this->getCurrentRate();
    }

    /**
     * Convert an amount from this currency to base currency.
     */
    public function convertToBase(float $amount): float
    {
        $rate = $this->getCurrentRate();
        return $rate > 0 ? $amount / $rate : $amount;
    }

    /**
     * Format an amount in this currency.
     */
    public function format(float $amount): string
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousand_separator
        );

        if ($this->symbol_position === 'before') {
            return $this->symbol . $formatted;
        }

        return $formatted . ' ' . $this->symbol;
    }

    /**
     * Set this currency as default.
     */
    public function setAsDefault(): bool
    {
        static::where('is_default', true)->update(['is_default' => false]);
        $this->is_default = true;
        return $this->save();
    }
}
