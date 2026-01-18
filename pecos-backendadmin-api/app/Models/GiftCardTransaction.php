<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCardTransaction extends Model
{
    protected $table = 'gift_card_transactions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'gift_card_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'order_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the gift card for this transaction.
     */
    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class, 'gift_card_id');
    }

    /**
     * Get the order for this transaction (if applicable).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Scope for purchase transactions.
     */
    public function scopePurchase($query)
    {
        return $query->where('type', 'purchase');
    }

    /**
     * Scope for redemption transactions.
     */
    public function scopeRedemption($query)
    {
        return $query->where('type', 'redemption');
    }

    /**
     * Scope for credit transactions.
     */
    public function scopeCredit($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope for debit transactions.
     */
    public function scopeDebit($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Scope for void transactions.
     */
    public function scopeVoid($query)
    {
        return $query->where('type', 'void');
    }
}
