<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    use SoftDeletes;

    protected $table = 'returns';

    protected $fillable = [
        'rma_number',
        'order_id',
        'customer_id',
        'reason_id',
        'status',
        'type',
        'customer_notes',
        'admin_notes',
        'refund_amount',
        'restocking_fee',
        'refund_method',
        'tracking_number',
        'return_label_url',
        'approved_at',
        'received_at',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'restocking_fee' => 'decimal:2',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RECEIVED = 'received';
    const STATUS_INSPECTING = 'inspecting';
    const STATUS_PROCESSED = 'processed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_EXCHANGED = 'exchanged';
    const STATUS_CLOSED = 'closed';

    const TYPE_REFUND = 'refund';
    const TYPE_EXCHANGE = 'exchange';
    const TYPE_STORE_CREDIT = 'store_credit';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->rma_number)) {
                $model->rma_number = self::generateRmaNumber();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                ReturnStatusHistory::create([
                    'return_id' => $model->id,
                    'old_status' => $model->getOriginal('status'),
                    'new_status' => $model->status,
                    'changed_by' => auth()->id(),
                ]);
            }
        });
    }

    /**
     * Generate unique RMA number.
     */
    public static function generateRmaNumber(): string
    {
        $prefix = 'RMA';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get the order for this return.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer for this return.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the reason for this return.
     */
    public function reason(): BelongsTo
    {
        return $this->belongsTo(ReturnReason::class, 'reason_id');
    }

    /**
     * Get the admin who processed this return.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the items for this return.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    /**
     * Get the photos for this return.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ReturnPhoto::class, 'return_id');
    }

    /**
     * Get the status history for this return.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(ReturnStatusHistory::class, 'return_id');
    }

    /**
     * Calculate total refund amount from items.
     */
    public function calculateRefundAmount(): float
    {
        return $this->items->sum('refund_amount') - $this->restocking_fee;
    }

    /**
     * Check if return can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if return can be rejected.
     */
    public function canBeRejected(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_RECEIVED]);
    }

    /**
     * Check if return can be processed.
     */
    public function canBeProcessed(): bool
    {
        return in_array($this->status, [self::STATUS_RECEIVED, self::STATUS_INSPECTING]);
    }

    /**
     * Approve the return.
     */
    public function approve(): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Reject the return.
     */
    public function reject(string $notes = null): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        if ($notes) {
            $this->admin_notes = $notes;
        }
        return $this->save();
    }

    /**
     * Mark as received.
     */
    public function markAsReceived(): bool
    {
        if ($this->status !== self::STATUS_APPROVED) {
            return false;
        }

        $this->status = self::STATUS_RECEIVED;
        $this->received_at = now();
        return $this->save();
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending returns.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_INSPECTING => 'Inspecting',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_EXCHANGED => 'Exchanged',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    /**
     * Get all available types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_REFUND => 'Refund',
            self::TYPE_EXCHANGE => 'Exchange',
            self::TYPE_STORE_CREDIT => 'Store Credit',
        ];
    }
}
