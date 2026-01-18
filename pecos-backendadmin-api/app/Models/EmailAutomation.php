<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAutomation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'trigger_conditions',
        'email_list_id',
        'is_active',
        'total_subscribers',
        'total_completed',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'is_active' => 'boolean',
    ];

    const TRIGGER_SIGNUP = 'signup';
    const TRIGGER_PURCHASE = 'purchase';
    const TRIGGER_ABANDONED_CART = 'abandoned_cart';
    const TRIGGER_BIRTHDAY = 'birthday';
    const TRIGGER_CUSTOM = 'custom';

    public function emailList()
    {
        return $this->belongsTo(EmailList::class);
    }

    public function steps()
    {
        return $this->hasMany(AutomationStep::class, 'automation_id')->orderBy('step_order');
    }

    public function subscribers()
    {
        return $this->hasMany(AutomationSubscriber::class, 'automation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getCompletionRate(): float
    {
        if ($this->total_subscribers == 0) return 0;
        return round(($this->total_completed / $this->total_subscribers) * 100, 2);
    }
}
