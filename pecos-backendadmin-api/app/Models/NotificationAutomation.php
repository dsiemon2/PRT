<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationAutomation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_event',
        'trigger_conditions',
        'delay_minutes',
        'notification_type',
        'sms_template_id',
        'push_template_id',
        'is_active',
        'sent_count',
        'delivered_count',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'delay_minutes' => 'integer',
        'is_active' => 'boolean',
        'sent_count' => 'integer',
        'delivered_count' => 'integer',
    ];

    /**
     * Available trigger events.
     */
    public static function getTriggerEvents(): array
    {
        return [
            'order_placed' => 'Order Placed',
            'order_confirmed' => 'Order Confirmed',
            'order_shipped' => 'Order Shipped',
            'order_delivered' => 'Order Delivered',
            'order_cancelled' => 'Order Cancelled',
            'abandoned_cart' => 'Abandoned Cart',
            'customer_signup' => 'Customer Sign Up',
            'customer_birthday' => 'Customer Birthday',
            'password_reset' => 'Password Reset',
            'back_in_stock' => 'Product Back in Stock',
            'price_drop' => 'Product Price Drop',
            'review_request' => 'Review Request',
            'loyalty_points_earned' => 'Loyalty Points Earned',
            'loyalty_tier_upgrade' => 'Loyalty Tier Upgrade',
            'subscription_renewal' => 'Subscription Renewal',
            'subscription_cancelled' => 'Subscription Cancelled',
        ];
    }

    public function smsTemplate()
    {
        return $this->belongsTo(SmsTemplate::class);
    }

    public function pushTemplate()
    {
        return $this->belongsTo(PushTemplate::class);
    }

    /**
     * Get automations for a specific trigger.
     */
    public static function getForTrigger(string $event)
    {
        return static::where('trigger_event', $event)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Increment sent count.
     */
    public function incrementSent(int $count = 1): void
    {
        $this->increment('sent_count', $count);
    }

    /**
     * Increment delivered count.
     */
    public function incrementDelivered(int $count = 1): void
    {
        $this->increment('delivered_count', $count);
    }

    /**
     * Get delivery rate.
     */
    public function getDeliveryRate(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->delivered_count / $this->sent_count) * 100, 2);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }
}
