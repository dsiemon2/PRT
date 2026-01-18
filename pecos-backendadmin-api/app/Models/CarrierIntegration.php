<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierIntegration extends Model
{
    protected $table = 'carrier_integrations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'carrier_code',
        'carrier_name',
        'api_key',
        'api_secret',
        'account_number',
        'is_connected',
        'is_enabled',
        'settings',
        'last_connected_at',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    protected $casts = [
        'is_connected' => 'boolean',
        'is_enabled' => 'boolean',
        'settings' => 'array',
        'last_connected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for connected carriers.
     */
    public function scopeConnected($query)
    {
        return $query->where('is_connected', true);
    }

    /**
     * Scope for enabled carriers.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}
