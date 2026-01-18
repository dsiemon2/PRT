<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'customer_email',
        'customer_phone',
        'customer_first_name',
        'customer_last_name',
        'order_number',
        'order_date',
        'total_amount',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'payment_last4',
        'payment_card_type',
        'order_notes',
        'status',
        'shipping_address_id',
        'billing_address_id',
        'payment_method_id',
        'billing_address1',
        'billing_address2',
        'billing_city',
        'billing_state',
        'billing_zip',
        'shipping_address1',
        'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    /**
     * Get the payment method for the order.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(UserPaymentMethod::class, 'payment_method_id');
    }

    /**
     * Get the status history for the order.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id');
    }

    /**
     * Get the gift card transactions for the order.
     */
    public function giftCardTransactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class, 'order_id');
    }

    /**
     * Get the loyalty transaction for this order.
     */
    public function loyaltyTransaction(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'order_id');
    }

    /**
     * Get the coupon usage for this order.
     */
    public function couponUsage(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'order_id');
    }

    /**
     * Get the customer's full name.
     */
    public function getCustomerNameAttribute(): string
    {
        return trim($this->customer_first_name . ' ' . $this->customer_last_name);
    }

    /**
     * Scope for orders by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for recent orders.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('order_date', 'desc');
    }
}
