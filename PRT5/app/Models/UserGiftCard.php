<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGiftCard extends Model
{
    protected $table = 'user_gift_cards';

    public $timestamps = false;

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
        'expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
