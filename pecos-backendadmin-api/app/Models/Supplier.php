<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'tax_id',
        'payment_terms',
        'notes',
        'total_orders',
        'total_amount',
    ];

    protected $casts = [
        'total_orders' => 'integer',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase orders for this supplier.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    /**
     * Get products that prefer this supplier.
     */
    public function preferredProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'preferred_supplier_id', 'id');
    }

    /**
     * Get products that last ordered from this supplier.
     */
    public function lastOrderedProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'last_supplier_id', 'id');
    }

    /**
     * Scope for active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive suppliers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for pending suppliers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
