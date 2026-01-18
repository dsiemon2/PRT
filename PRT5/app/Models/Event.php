<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'EventName',
        'EnteredBy',
        'StartDate',
        'EndDate',
        'StartTime',
        'EndTime',
    ];

    protected $casts = [
        'StartDate' => 'datetime',
        'EndDate' => 'datetime',
    ];

    // Accessors for consistent naming

    public function getTitleAttribute()
    {
        return $this->EventName;
    }

    public function getStartDateAttribute()
    {
        return $this->attributes['StartDate'] ? \Carbon\Carbon::parse($this->attributes['StartDate']) : null;
    }

    public function getEndDateAttribute()
    {
        return $this->attributes['EndDate'] ? \Carbon\Carbon::parse($this->attributes['EndDate']) : null;
    }

    public function getStartTimeAttribute()
    {
        return $this->attributes['StartTime'] ?? null;
    }

    public function getEndTimeAttribute()
    {
        return $this->attributes['EndTime'] ?? null;
    }

    public function getEnteredByAttribute()
    {
        return $this->attributes['EnteredBy'] ?? null;
    }

    // Scopes

    public function scopeUpcoming($query)
    {
        return $query->where('StartDate', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('EndDate', '<', now());
    }

    // Calendar URL accessors

    public function getGoogleCalendarUrlAttribute(): string
    {
        $startDate = $this->start_date ? $this->start_date->format('Ymd') : date('Ymd');
        $endDate = $this->end_date ? $this->end_date->format('Ymd') : $startDate;

        if ($this->start_time) {
            $startDate .= 'T' . str_replace(':', '', $this->start_time) . '00';
        }
        if ($this->end_time) {
            $endDate .= 'T' . str_replace(':', '', $this->end_time) . '00';
        }

        $params = [
            'action' => 'TEMPLATE',
            'text' => $this->title,
            'dates' => $startDate . '/' . $endDate,
            'details' => '',
            'location' => '',
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    public function getIcsDownloadUrlAttribute(): string
    {
        return route('events.ics', $this->ID);
    }
}
