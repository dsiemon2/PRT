<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $table = 'shipping_zones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'regions',
        'is_active',
    ];

    protected $casts = [
        'regions' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shipping methods for this zone.
     */
    public function methods(): HasMany
    {
        return $this->hasMany(ShippingMethod::class, 'zone_id');
    }

    /**
     * Scope for active zones.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
