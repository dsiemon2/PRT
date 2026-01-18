<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatDepartment extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'email',
        'is_active',
        'working_hours',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'working_hours' => 'array',
    ];

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(ChatAgent::class, 'chat_department_agents', 'department_id', 'agent_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function availableAgents(): BelongsToMany
    {
        return $this->agents()
            ->where('is_active', true)
            ->where('status', 'online')
            ->whereColumn('current_chat_count', '<', 'max_concurrent_chats');
    }

    public function isWithinWorkingHours(): bool
    {
        if (empty($this->working_hours)) {
            return true; // Always available if no hours set
        }

        $now = now();
        $dayOfWeek = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        $schedule = $this->working_hours[$dayOfWeek] ?? null;
        if (!$schedule || !($schedule['enabled'] ?? false)) {
            return false;
        }

        $start = $schedule['start'] ?? '00:00';
        $end = $schedule['end'] ?? '23:59';

        return $currentTime >= $start && $currentTime <= $end;
    }
}
