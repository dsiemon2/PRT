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
        'StartDate',
        'EndDate',
        'StartTime',
        'EndTime',
        'EnteredBy',
    ];

    protected $casts = [
        'StartDate' => 'date',
        'EndDate' => 'date',
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('EndDate', '>=', now()->toDateString())
                     ->orderBy('StartDate', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('EndDate', '<', now()->toDateString())
                     ->orderBy('StartDate', 'desc');
    }
}
