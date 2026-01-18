<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatAgent extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'avatar_url',
        'status',
        'max_concurrent_chats',
        'current_chat_count',
        'skills',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'agent_id');
    }

    public function activeSessions(): HasMany
    {
        return $this->sessions()->where('status', 'active');
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(ChatDepartment::class, 'chat_department_agents', 'agent_id', 'department_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'agent_id');
    }

    public static function getStatuses(): array
    {
        return [
            'online' => 'Online',
            'offline' => 'Offline',
            'away' => 'Away',
            'busy' => 'Busy',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->is_active
            && $this->status === 'online'
            && $this->current_chat_count < $this->max_concurrent_chats;
    }

    public function goOnline(): void
    {
        $this->update([
            'status' => 'online',
            'last_activity_at' => now(),
        ]);
    }

    public function goOffline(): void
    {
        $this->update(['status' => 'offline']);
    }
}
