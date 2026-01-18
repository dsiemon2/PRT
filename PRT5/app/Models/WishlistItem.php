<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    protected $table = 'user_wishlists';

    const CREATED_AT = 'added_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }
}
