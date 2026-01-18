<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $table = 'user_devices';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'os_name',
        'os_version',
        'browser',
        'ip_address',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'last_seen' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
