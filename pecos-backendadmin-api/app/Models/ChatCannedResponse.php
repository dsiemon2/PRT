<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatCannedResponse extends Model
{
    protected $fillable = [
        'title',
        'shortcut',
        'content',
        'category',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public static function findByShortcut(string $shortcut): ?self
    {
        return static::where('shortcut', $shortcut)
            ->where('is_active', true)
            ->first();
    }
}
