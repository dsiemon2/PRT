<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTier extends Model
{
    protected $table = 'loyalty_tiers';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'tier_name',
        'min_lifetime_points',
        'points_multiplier',
        'benefits',
        'display_order',
    ];

    protected $casts = [
        'min_lifetime_points' => 'integer',
        'points_multiplier' => 'decimal:2',
        'benefits' => 'array',
        'display_order' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the members in this tier.
     */
    public function members(): HasMany
    {
        return $this->hasMany(LoyaltyMember::class, 'tier_id');
    }

    /**
     * Get the rewards for this tier.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(LoyaltyReward::class, 'tier_id');
    }
}
