<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dropshipper extends Model
{
    protected $table = 'dropshippers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'api_key',
        'api_secret',
        'status',
        'commission_rate',
        'total_orders',
        'total_revenue',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'notes',
    ];

    protected $hidden = [
        'api_secret',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_orders' => 'integer',
        'total_revenue' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the orders for this dropshipper.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(DropshipOrder::class, 'dropshipper_id');
    }

    /**
     * Get the purchase orders for this dropshipper.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'dropshipper_id');
    }

    /**
     * Get the API logs for this dropshipper.
     */
    public function apiLogs(): HasMany
    {
        return $this->hasMany(ApiLog::class, 'dropshipper_id');
    }

    /**
     * Get the permissions for this dropshipper.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(DropshipperPermission::class, 'dropshipper_id');
    }

    /**
     * Get the webhooks for this dropshipper.
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(DropshipWebhook::class, 'dropshipper_id');
    }

    /**
     * Scope for active dropshippers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive dropshippers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for pending dropshippers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
