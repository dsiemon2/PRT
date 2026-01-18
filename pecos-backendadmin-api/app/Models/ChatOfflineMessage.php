<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatOfflineMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'department_code',
        'message',
        'status',
        'assigned_to',
        'reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(ChatAgent::class, 'assigned_to');
    }

    public static function getStatuses(): array
    {
        return [
            'new' => 'New',
            'read' => 'Read',
            'replied' => 'Replied',
            'closed' => 'Closed',
        ];
    }

    public function markAsRead(): void
    {
        if ($this->status === 'new') {
            $this->update(['status' => 'read']);
        }
    }

    public function reply(string $reply, int $agentId): void
    {
        $this->update([
            'status' => 'replied',
            'reply' => $reply,
            'assigned_to' => $agentId,
            'replied_at' => now(),
        ]);
    }
}
