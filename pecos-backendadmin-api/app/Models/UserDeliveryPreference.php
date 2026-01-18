<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeliveryPreference extends Model
{
    protected $table = 'user_delivery_preferences';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

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
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user for this preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if user is on vacation.
     */
    public function isOnVacation(): bool
    {
        if (!$this->vacation_mode) {
            return false;
        }

        $now = now()->startOfDay();

        if ($this->vacation_start && $this->vacation_end) {
            return $now->between($this->vacation_start, $this->vacation_end);
        }

        return false;
    }
}
