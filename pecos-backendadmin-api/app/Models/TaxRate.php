<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $table = 'tax_rates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'country_code',
        'state_code',
        'state_name',
        'city',
        'rate',
        'is_compound',
        'tax_shipping',
        'is_active',
        'is_local',
    ];

    protected $casts = [
        'rate' => 'decimal:3',
        'is_compound' => 'boolean',
        'tax_shipping' => 'boolean',
        'is_active' => 'boolean',
        'is_local' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for active tax rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for compound tax rates.
     */
    public function scopeCompound($query)
    {
        return $query->where('is_compound', true);
    }

    /**
     * Scope for tax rates that apply to shipping.
     */
    public function scopeTaxesShipping($query)
    {
        return $query->where('tax_shipping', true);
    }

    /**
     * Scope for local tax rates.
     */
    public function scopeLocal($query)
    {
        return $query->where('is_local', true);
    }

    /**
     * Scope by country.
     */
    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    /**
     * Scope by state.
     */
    public function scopeByState($query, $stateCode)
    {
        return $query->where('state_code', $stateCode);
    }
}
