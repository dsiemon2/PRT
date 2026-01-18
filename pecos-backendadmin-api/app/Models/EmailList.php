<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'double_optin',
        'welcome_email_template',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'double_optin' => 'boolean',
    ];

    public function subscribers()
    {
        return $this->hasMany(EmailSubscriber::class);
    }

    public function campaigns()
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function automations()
    {
        return $this->hasMany(EmailAutomation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getSubscriberCount(): int
    {
        return $this->subscribers()->where('status', 'subscribed')->count();
    }
}
