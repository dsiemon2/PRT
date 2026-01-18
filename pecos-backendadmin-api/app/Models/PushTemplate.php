<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'title',
        'body',
        'icon',
        'image',
        'url',
        'event_trigger',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function pushNotifications()
    {
        return $this->hasMany(PushNotification::class, 'template_id');
    }

    public function automations()
    {
        return $this->hasMany(NotificationAutomation::class, 'push_template_id');
    }

    public function campaigns()
    {
        return $this->hasMany(NotificationCampaign::class, 'push_template_id');
    }

    /**
     * Render the template with variables.
     */
    public function render(array $data = []): array
    {
        $title = $this->title;
        $body = $this->body;
        $url = $this->url;

        foreach ($data as $key => $value) {
            $title = str_replace('{' . $key . '}', $value, $title);
            $title = str_replace('{{' . $key . '}}', $value, $title);
            $body = str_replace('{' . $key . '}', $value, $body);
            $body = str_replace('{{' . $key . '}}', $value, $body);
            if ($url) {
                $url = str_replace('{' . $key . '}', $value, $url);
                $url = str_replace('{{' . $key . '}}', $value, $url);
            }
        }

        return [
            'title' => $title,
            'body' => $body,
            'icon' => $this->icon,
            'image' => $this->image,
            'url' => $url,
        ];
    }

    /**
     * Get templates by event trigger.
     */
    public static function getByTrigger(string $trigger)
    {
        return static::where('event_trigger', $trigger)
            ->where('is_active', true)
            ->get();
    }
}
