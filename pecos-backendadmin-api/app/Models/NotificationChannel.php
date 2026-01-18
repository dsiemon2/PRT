<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'provider',
        'name',
        'credentials',
        'settings',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $hidden = [
        'credentials',
    ];

    /**
     * Get available providers by type.
     */
    public static function getProviders(string $type): array
    {
        $providers = [
            'sms' => [
                'twilio' => 'Twilio',
                'aws_sns' => 'AWS SNS',
                'nexmo' => 'Nexmo/Vonage',
                'messagebird' => 'MessageBird',
                'plivo' => 'Plivo',
            ],
            'push' => [
                'firebase' => 'Firebase Cloud Messaging',
                'onesignal' => 'OneSignal',
                'pusher' => 'Pusher Beams',
                'aws_sns' => 'AWS SNS',
            ],
        ];

        return $providers[$type] ?? [];
    }

    /**
     * Get the default channel for a type.
     */
    public static function getDefault(string $type): ?self
    {
        return static::where('type', $type)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Set as default channel.
     */
    public function setAsDefault(): void
    {
        // Remove default from other channels of same type
        static::where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Test the channel connection.
     */
    public function testConnection(): array
    {
        // This would implement actual provider testing
        // For now, return a placeholder
        return [
            'success' => true,
            'message' => 'Connection test not implemented',
        ];
    }

    /**
     * Get credential value by key.
     */
    public function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    /**
     * Get setting value by key.
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }
}
