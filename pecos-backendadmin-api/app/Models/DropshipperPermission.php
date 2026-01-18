<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DropshipperPermission extends Model
{
    protected $table = 'dropshipper_permissions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'dropshipper_id',
        'permission',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Get the dropshipper for this permission.
     */
    public function dropshipper(): BelongsTo
    {
        return $this->belongsTo(Dropshipper::class, 'dropshipper_id');
    }

    /**
     * Scope for granted permissions.
     */
    public function scopeGranted($query)
    {
        return $query->where('granted', true);
    }
}
