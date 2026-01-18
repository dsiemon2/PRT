<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'sender_type',
        'agent_id',
        'message',
        'message_type',
        'attachments',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(ChatAgent::class, 'agent_id');
    }

    public static function getSenderTypes(): array
    {
        return [
            'visitor' => 'Visitor',
            'agent' => 'Agent',
            'system' => 'System',
        ];
    }

    public static function getMessageTypes(): array
    {
        return [
            'text' => 'Text',
            'image' => 'Image',
            'file' => 'File',
            'link' => 'Link',
            'card' => 'Card',
        ];
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
