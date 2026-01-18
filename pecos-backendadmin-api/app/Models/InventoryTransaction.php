<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'transaction_type',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'reference_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the product for this transaction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }

    /**
     * Get the user who made this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for purchase transactions.
     */
    public function scopePurchase($query)
    {
        return $query->where('transaction_type', 'purchase');
    }

    /**
     * Scope for sale transactions.
     */
    public function scopeSale($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    /**
     * Scope for return transactions.
     */
    public function scopeReturn($query)
    {
        return $query->where('transaction_type', 'return');
    }

    /**
     * Scope for adjustment transactions.
     */
    public function scopeAdjustment($query)
    {
        return $query->where('transaction_type', 'adjustment');
    }

    /**
     * Scope for damaged transactions.
     */
    public function scopeDamaged($query)
    {
        return $query->where('transaction_type', 'damaged');
    }

    /**
     * Scope for transfer transactions.
     */
    public function scopeTransfer($query)
    {
        return $query->where('transaction_type', 'transfer');
    }
}
