<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxClass extends Model
{
    protected $table = 'tax_classes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for default tax class.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
