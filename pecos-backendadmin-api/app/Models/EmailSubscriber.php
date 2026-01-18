<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_list_id',
        'email',
        'first_name',
        'last_name',
        'customer_id',
        'status',
        'source',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_reason',
        'custom_fields',
        'ip_address',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    const STATUS_SUBSCRIBED = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_PENDING = 'pending';
    const STATUS_BOUNCED = 'bounced';

    public function emailList()
    {
        return $this->belongsTo(EmailList::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function campaignRecipients()
    {
        return $this->hasMany(CampaignRecipient::class, 'subscriber_id');
    }

    public function scopeSubscribed($query)
    {
        return $query->where('status', self::STATUS_SUBSCRIBED);
    }

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->email;
    }

    public function subscribe(): bool
    {
        $this->status = self::STATUS_SUBSCRIBED;
        $this->subscribed_at = now();
        $this->unsubscribed_at = null;
        $this->unsubscribe_reason = null;
        return $this->save();
    }

    public function unsubscribe(?string $reason = null): bool
    {
        $this->status = self::STATUS_UNSUBSCRIBED;
        $this->unsubscribed_at = now();
        $this->unsubscribe_reason = $reason;
        return $this->save();
    }
}
