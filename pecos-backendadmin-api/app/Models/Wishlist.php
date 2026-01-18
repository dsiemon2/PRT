<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $table = 'user_wishlists';
    public $timestamps = false;

    const CREATED_AT = 'added_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'product_id',
        'added_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        // product_id (int) references products3.ID
        return $this->belongsTo(Product::class, 'product_id', 'ID');
    }
}
