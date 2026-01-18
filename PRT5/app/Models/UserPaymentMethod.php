<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPaymentMethod extends Model
{
    protected $table = 'user_payment_methods';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'is_default',
        'card_type',
        'card_last4',
        'card_holder_name',
        'expiry_month',
        'expiry_year',
        'billing_address_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }
}
