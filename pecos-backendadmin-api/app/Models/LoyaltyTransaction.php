<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $table = 'loyalty_transactions';

    protected $fillable = [
        'user_id',
        'points',
        'transaction_type',
        'description',
        'order_id',
        'reference_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeEarned($query)
    {
        return $query->where('transaction_type', 'earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('transaction_type', 'redeemed');
    }

    public function scopeExpired($query)
    {
        return $query->where('transaction_type', 'expired');
    }
}
