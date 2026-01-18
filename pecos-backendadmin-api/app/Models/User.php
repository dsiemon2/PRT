<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'oauth_provider',
        'oauth_uid',
        'oauth_token',
        'profile_picture',
        'first_name',
        'last_name',
        'phone',
        'is_active',
        'role',
    ];

    protected $hidden = [
        'password',
        'oauth_token',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'last_login' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'user_id');
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    /**
     * Get the wishlists for the user.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    /**
     * Get the cart items for the user.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    /**
     * Get the loyalty member record for the user.
     */
    public function loyaltyMember(): HasOne
    {
        return $this->hasOne(LoyaltyMember::class, 'user_id');
    }

    /**
     * Get the loyalty transactions for the user.
     */
    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'user_id');
    }

    /**
     * Get the gift cards for the user.
     */
    public function giftCards(): HasMany
    {
        return $this->hasMany(UserGiftCard::class, 'user_id');
    }

    /**
     * Get the tax exemptions for the user.
     */
    public function taxExemptions(): HasMany
    {
        return $this->hasMany(TaxExemption::class, 'user_id');
    }

    /**
     * Get the delivery preferences for the user.
     */
    public function deliveryPreferences(): HasOne
    {
        return $this->hasOne(UserDeliveryPreference::class, 'user_id');
    }

    /**
     * Get the notification preferences for the user.
     */
    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class, 'user_id');
    }

    /**
     * Get the coupon usages for the user.
     */
    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'user_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager or higher.
     */
    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
