<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCard extends Model
{
    protected $table = 'gift_cards';
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'initial_balance',
        'current_balance',
        'purchaser_email',
        'recipient_email',
        'recipient_name',
        'message',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'expires_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the transactions for this gift card.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class, 'gift_card_id');
    }

    /**
     * Check if gift card is valid for use.
     */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->current_balance <= 0) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active gift cards.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for used gift cards.
     */
    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * Scope for expired gift cards.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope for voided gift cards.
     */
    public function scopeVoided($query)
    {
        return $query->where('status', 'voided');
    }
}
