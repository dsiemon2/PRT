<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPaymentMethod extends Model
{
    protected $table = 'user_payment_methods';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
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
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user for this payment method.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the billing address for this payment method.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    /**
     * Scope for default payment methods.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Check if card is expired.
     */
    public function isExpired(): bool
    {
        $now = now();
        $expiryDate = \Carbon\Carbon::create($this->expiry_year, $this->expiry_month, 1)->endOfMonth();
        return $now->greaterThan($expiryDate);
    }
}
