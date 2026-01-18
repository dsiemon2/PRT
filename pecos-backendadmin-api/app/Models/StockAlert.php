<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    protected $table = 'stock_alerts';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'alert_type',
        'current_quantity',
        'threshold_quantity',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'current_quantity' => 'integer',
        'threshold_quantity' => 'integer',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the product for this alert.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }

    /**
     * Scope for low stock alerts.
     */
    public function scopeLowStock($query)
    {
        return $query->where('alert_type', 'low_stock');
    }

    /**
     * Scope for out of stock alerts.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('alert_type', 'out_of_stock');
    }

    /**
     * Scope for overstock alerts.
     */
    public function scopeOverstock($query)
    {
        return $query->where('alert_type', 'overstock');
    }

    /**
     * Scope for unresolved alerts.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for resolved alerts.
     */
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }
}
