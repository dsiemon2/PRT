<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnReason extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_photo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_photo' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all returns with this reason.
     */
    public function returns(): HasMany
    {
        return $this->hasMany(ReturnRequest::class, 'reason_id');
    }

    /**
     * Scope to get only active reasons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
