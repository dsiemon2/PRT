<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'token',
        'platform',
        'device_name',
        'device_model',
        'os_version',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Register or update a device token.
     */
    public static function registerToken(
        int $customerId,
        string $token,
        string $platform,
        array $deviceInfo = []
    ): self {
        return static::updateOrCreate(
            [
                'customer_id' => $customerId,
                'token' => $token,
            ],
            array_merge([
                'platform' => $platform,
                'is_active' => true,
                'last_used_at' => now(),
            ], $deviceInfo)
        );
    }

    /**
     * Mark token as used.
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Deactivate token.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Scope for active tokens.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by platform.
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}
