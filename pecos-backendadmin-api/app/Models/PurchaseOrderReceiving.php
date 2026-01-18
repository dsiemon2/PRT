<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderReceiving extends Model
{
    protected $table = 'purchase_order_receiving';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'purchase_order_id',
        'purchase_order_item_id',
        'product_id',
        'quantity_received',
        'received_date',
        'condition',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'quantity_received' => 'integer',
        'received_date' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Get the purchase order for this receiving record.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the purchase order item for this receiving record.
     */
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    /**
     * Get the product for this receiving record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }

    /**
     * Get the user who received this.
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
