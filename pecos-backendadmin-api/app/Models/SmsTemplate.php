<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'content',
        'event_trigger',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function smsMessages()
    {
        return $this->hasMany(SmsMessage::class, 'template_id');
    }

    public function automations()
    {
        return $this->hasMany(NotificationAutomation::class, 'sms_template_id');
    }

    public function campaigns()
    {
        return $this->hasMany(NotificationCampaign::class, 'sms_template_id');
    }

    /**
     * Render the template with variables.
     */
    public function render(array $data = []): string
    {
        $content = $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
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

    /**
     * Get character count and segment count.
     */
    public function getSegmentInfo(): array
    {
        $length = mb_strlen($this->content);
        $hasUnicode = preg_match('/[^\x00-\x7F]/', $this->content);

        if ($hasUnicode) {
            $charsPerSegment = 70;
        } else {
            $charsPerSegment = 160;
        }

        $segments = ceil($length / $charsPerSegment);

        return [
            'characters' => $length,
            'segments' => max(1, $segments),
            'chars_per_segment' => $charsPerSegment,
            'has_unicode' => $hasUnicode,
        ];
    }
}
