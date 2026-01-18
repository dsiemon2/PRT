<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeliveryPreference extends Model
{
    protected $table = 'user_delivery_preferences';

    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'special_instructions',
        'backup_location',
        'door_to_door',
        'weekend_delivery',
        'signature_required',
        'leave_with_neighbor',
        'authority_to_leave',
        'weekday_time',
        'weekend_time',
        'vacation_mode',
        'vacation_start',
        'vacation_end',
        'vacation_instructions',
    ];

    protected $casts = [
        'door_to_door' => 'boolean',
        'weekend_delivery' => 'boolean',
        'signature_required' => 'boolean',
        'leave_with_neighbor' => 'boolean',
        'authority_to_leave' => 'boolean',
        'vacation_mode' => 'boolean',
        'vacation_start' => 'date',
        'vacation_end' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
