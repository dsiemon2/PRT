<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatTrigger extends Model
{
    protected $fillable = [
        'name',
        'trigger_type',
        'conditions',
        'message',
        'department_code',
        'delay_seconds',
        'is_active',
        'triggered_count',
        'accepted_count',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getTriggerTypes(): array
    {
        return [
            'page_time' => 'Time on Page',
            'scroll_depth' => 'Scroll Depth',
            'exit_intent' => 'Exit Intent',
            'page_url' => 'Page URL Match',
            'cart_value' => 'Cart Value',
            'returning_visitor' => 'Returning Visitor',
        ];
    }

    public function recordTriggered(): void
    {
        $this->increment('triggered_count');
    }

    public function recordAccepted(): void
    {
        $this->increment('accepted_count');
    }

    public function getAcceptanceRateAttribute(): float
    {
        if ($this->triggered_count === 0) return 0;
        return round(($this->accepted_count / $this->triggered_count) * 100, 2);
    }
}
