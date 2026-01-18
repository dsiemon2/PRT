<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model
{
    protected $table = 'shipping_classes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'slug',
        'surcharge',
        'is_default',
    ];

    protected $casts = [
        'surcharge' => 'decimal:2',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for default class.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
