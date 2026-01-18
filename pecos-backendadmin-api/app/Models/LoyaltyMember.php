<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyMember extends Model
{
    protected $table = 'loyalty_members';
    protected $primaryKey = 'id';
    public $timestamps = false;

    const CREATED_AT = 'joined_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'tier_id',
        'total_points',
        'available_points',
        'lifetime_points',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'available_points' => 'integer',
        'lifetime_points' => 'integer',
        'joined_at' => 'datetime',
    ];

    /**
     * Get the user for this member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the tier for this member.
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'tier_id');
    }

    /**
     * Get the transactions for this member.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'user_id', 'user_id');
    }
}
