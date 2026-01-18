<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGiftCard extends Model
{
    protected $table = 'user_gift_cards';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'added_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'card_code',
        'balance',
        'initial_amount',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'initial_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'added_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user for this gift card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for active gift cards.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the gift card is valid for use.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->balance <= 0) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
