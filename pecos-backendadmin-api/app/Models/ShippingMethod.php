<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingMethod extends Model
{
    protected $table = 'shipping_methods';
    protected $primaryKey = 'id';

    protected $fillable = [
        'zone_id',
        'name',
        'rate',
        'delivery_time',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the zone for this shipping method.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'zone_id');
    }

    /**
     * Scope for active methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
