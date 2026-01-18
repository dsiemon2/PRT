<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'sms_template_id',
        'push_template_id',
        'audience_filters',
        'audience_count',
        'scheduled_at',
        'started_at',
        'completed_at',
        'sms_sent',
        'sms_delivered',
        'sms_failed',
        'push_sent',
        'push_delivered',
        'push_clicked',
        'push_failed',
        'total_cost',
        'created_by',
    ];

    protected $casts = [
        'audience_filters' => 'array',
        'audience_count' => 'integer',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'sms_sent' => 'integer',
        'sms_delivered' => 'integer',
        'sms_failed' => 'integer',
        'push_sent' => 'integer',
        'push_delivered' => 'integer',
        'push_clicked' => 'integer',
        'push_failed' => 'integer',
        'total_cost' => 'decimal:2',
    ];

    public function smsTemplate()
    {
        return $this->belongsTo(SmsTemplate::class);
    }

    public function pushTemplate()
    {
        return $this->belongsTo(PushTemplate::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(NotificationCampaignRecipient::class, 'campaign_id');
    }

    /**
     * Calculate SMS stats.
     */
    public function getSmsStats(): array
    {
        $total = $this->sms_sent;
        $deliveryRate = $total > 0 ? round(($this->sms_delivered / $total) * 100, 2) : 0;

        return [
            'sent' => $this->sms_sent,
            'delivered' => $this->sms_delivered,
            'failed' => $this->sms_failed,
            'delivery_rate' => $deliveryRate,
        ];
    }

    /**
     * Calculate push stats.
     */
    public function getPushStats(): array
    {
        $total = $this->push_sent;
        $deliveryRate = $total > 0 ? round(($this->push_delivered / $total) * 100, 2) : 0;
        $clickRate = $this->push_delivered > 0 ? round(($this->push_clicked / $this->push_delivered) * 100, 2) : 0;

        return [
            'sent' => $this->push_sent,
            'delivered' => $this->push_delivered,
            'clicked' => $this->push_clicked,
            'failed' => $this->push_failed,
            'delivery_rate' => $deliveryRate,
            'click_rate' => $clickRate,
        ];
    }

    /**
     * Schedule the campaign.
     */
    public function schedule(\DateTime $dateTime): void
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $dateTime,
        ]);
    }

    /**
     * Start the campaign.
     */
    public function start(): void
    {
        $this->update([
            'status' => 'sending',
            'started_at' => now(),
        ]);
    }

    /**
     * Complete the campaign.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'sent',
            'completed_at' => now(),
        ]);
    }

    /**
     * Pause the campaign.
     */
    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    /**
     * Cancel the campaign.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Scope for scheduled campaigns ready to send.
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }
}
