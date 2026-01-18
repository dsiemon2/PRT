<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $table = 'user_notification_preferences';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_id',
        'delivery_email',
        'delivery_sms',
        'delivery_push',
        'promo_email',
        'promo_sms',
        'promo_push',
        'payment_email',
        'payment_sms',
        'payment_push',
        'security_email',
        'security_sms',
        'security_push',
    ];

    protected $casts = [
        'delivery_email' => 'boolean',
        'delivery_sms' => 'boolean',
        'delivery_push' => 'boolean',
        'promo_email' => 'boolean',
        'promo_sms' => 'boolean',
        'promo_push' => 'boolean',
        'payment_email' => 'boolean',
        'payment_sms' => 'boolean',
        'payment_push' => 'boolean',
        'security_email' => 'boolean',
        'security_sms' => 'boolean',
        'security_push' => 'boolean',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user for this preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
