<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnPhoto extends Model
{
    protected $fillable = [
        'return_id',
        'return_item_id',
        'photo_url',
        'photo_type',
        'description',
    ];

    const TYPE_CONDITION = 'condition';
    const TYPE_DAMAGE = 'damage';
    const TYPE_PACKAGING = 'packaging';
    const TYPE_LABEL = 'label';
    const TYPE_OTHER = 'other';

    /**
     * Get the return request.
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    /**
     * Get the return item.
     */
    public function returnItem(): BelongsTo
    {
        return $this->belongsTo(ReturnItem::class, 'return_item_id');
    }

    /**
     * Get all available photo types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_CONDITION => 'Condition',
            self::TYPE_DAMAGE => 'Damage',
            self::TYPE_PACKAGING => 'Packaging',
            self::TYPE_LABEL => 'Label',
            self::TYPE_OTHER => 'Other',
        ];
    }
}
