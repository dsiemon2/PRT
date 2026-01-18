<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DropshipWebhook extends Model
{
    protected $table = 'dropship_webhooks';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'dropshipper_id',
        'event_type',
        'url',
        'secret',
        'active',
    ];

    protected $hidden = [
        'secret',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the dropshipper for this webhook.
     */
    public function dropshipper(): BelongsTo
    {
        return $this->belongsTo(Dropshipper::class, 'dropshipper_id');
    }

    /**
     * Scope for active webhooks.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope by event type.
     */
    public function scopeForEvent($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
