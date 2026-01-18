<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    protected $table = 'product_history';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'upc',
        'field_name',
        'old_value',
        'new_value',
        'action',
        'user_id',
        'user_name',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the product that this history belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }

    /**
     * Log a product change.
     */
    public static function logChange(
        $productId,
        string $upc,
        string $fieldName,
        $oldValue,
        $newValue,
        string $action = 'update',
        ?int $userId = null,
        ?string $userName = null,
        ?string $ipAddress = null,
        ?string $notes = null
    ): self {
        return self::create([
            'product_id' => $productId,
            'upc' => $upc,
            'field_name' => $fieldName,
            'old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : $newValue,
            'action' => $action,
            'user_id' => $userId,
            'user_name' => $userName ?? 'System',
            'ip_address' => $ipAddress,
            'notes' => $notes,
        ]);
    }

    /**
     * Log multiple field changes at once.
     */
    public static function logMultipleChanges(
        $productId,
        string $upc,
        array $changes,
        string $action = 'update',
        ?int $userId = null,
        ?string $userName = null,
        ?string $ipAddress = null
    ): void {
        foreach ($changes as $fieldName => $values) {
            if (isset($values['old']) || isset($values['new'])) {
                self::logChange(
                    $productId,
                    $upc,
                    $fieldName,
                    $values['old'] ?? null,
                    $values['new'] ?? null,
                    $action,
                    $userId,
                    $userName,
                    $ipAddress
                );
            }
        }
    }

    /**
     * Get formatted action label.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'stock_adjustment' => 'Stock Adjusted',
            'image_upload' => 'Image Uploaded',
            'image_delete' => 'Image Deleted',
            'price_change' => 'Price Changed',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get field name in human-readable format.
     */
    public function getFieldLabelAttribute(): string
    {
        return match ($this->field_name) {
            'ShortDescription' => 'Name',
            'LngDescription' => 'Description',
            'UnitPrice' => 'Price',
            'cost_price' => 'Cost Price',
            'stock_quantity' => 'Stock Quantity',
            'CategoryCode' => 'Category',
            'low_stock_threshold' => 'Low Stock Threshold',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'ItemSize' => 'Size',
            'is_deleted' => 'Deleted Status',
            default => ucwords(str_replace('_', ' ', $this->field_name)),
        };
    }
}
