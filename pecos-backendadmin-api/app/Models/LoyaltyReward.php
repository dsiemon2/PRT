<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyReward extends Model
{
    protected $table = 'loyalty_rewards';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tier_id',
        'name',
        'point_cost',
        'description',
    ];

    protected $casts = [
        'point_cost' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tier for this reward.
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'tier_id');
    }
}
