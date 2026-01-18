<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'customer_id',
        'visitor_name',
        'visitor_email',
        'agent_id',
        'status',
        'channel',
        'department',
        'subject',
        'priority',
        'wait_time_seconds',
        'initial_message',
        'visitor_ip',
        'visitor_user_agent',
        'visitor_page_url',
        'visitor_metadata',
        'rating',
        'feedback',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'visitor_metadata' => 'array',
        'rating' => 'decimal:1',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_id)) {
                $session->session_id = 'CHAT-' . strtoupper(Str::random(10));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(ChatAgent::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id')->orderBy('created_at');
    }

    public static function getStatuses(): array
    {
        return [
            'waiting' => 'Waiting',
            'active' => 'Active',
            'closed' => 'Closed',
            'transferred' => 'Transferred',
        ];
    }

    public static function getChannels(): array
    {
        return [
            'website' => 'Website',
            'mobile_app' => 'Mobile App',
            'facebook' => 'Facebook Messenger',
            'instagram' => 'Instagram',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
        ];
    }

    public function assignAgent(ChatAgent $agent): void
    {
        $this->update([
            'agent_id' => $agent->id,
            'status' => 'active',
            'started_at' => now(),
            'wait_time_seconds' => now()->diffInSeconds($this->created_at),
        ]);

        $agent->increment('current_chat_count');
    }

    public function close(?float $rating = null, ?string $feedback = null): void
    {
        $this->update([
            'status' => 'closed',
            'ended_at' => now(),
            'rating' => $rating,
            'feedback' => $feedback,
        ]);

        if ($this->agent) {
            $this->agent->decrement('current_chat_count');
        }
    }

    public function getDurationAttribute(): int
    {
        if (!$this->started_at) return 0;
        $end = $this->ended_at ?? now();
        return $end->diffInSeconds($this->started_at);
    }
}
