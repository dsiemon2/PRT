<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'preview_text',
        'from_name',
        'from_email',
        'reply_to',
        'type',
        'status',
        'html_content',
        'text_content',
        'template_id',
        'email_list_id',
        'segment_id',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'open_count',
        'click_count',
        'bounce_count',
        'unsubscribe_count',
        'spam_count',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    const TYPE_REGULAR = 'regular';
    const TYPE_AUTOMATED = 'automated';
    const TYPE_AB_TEST = 'ab_test';

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELLED = 'cancelled';

    public function emailList()
    {
        return $this->belongsTo(EmailList::class);
    }

    public function segment()
    {
        return $this->belongsTo(CrmSegment::class);
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class, 'campaign_id');
    }

    public function links()
    {
        return $this->hasMany(CampaignLink::class, 'campaign_id');
    }

    public function abVariants()
    {
        return $this->hasMany(CampaignAbVariant::class, 'campaign_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function getOpenRate(): float
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->open_count / $this->sent_count) * 100, 2);
    }

    public function getClickRate(): float
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->click_count / $this->sent_count) * 100, 2);
    }

    public function getBounceRate(): float
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->bounce_count / $this->sent_count) * 100, 2);
    }

    public function getUnsubscribeRate(): float
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->unsubscribe_count / $this->sent_count) * 100, 2);
    }
}
