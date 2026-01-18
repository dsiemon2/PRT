<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Models\PushTemplate;
use App\Models\SmsMessage;
use App\Models\PushNotification;
use App\Models\NotificationChannel;
use App\Models\NotificationCampaign;
use App\Models\NotificationAutomation;
use App\Models\CustomerDeviceToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationsController extends Controller
{
    // =====================
    // SMS TEMPLATES
    // =====================

    public function smsTemplates(Request $request): JsonResponse
    {
        $query = SmsTemplate::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('event_trigger')) {
            $query->where('event_trigger', $request->event_trigger);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('name')->paginate($request->get('per_page', 20));

        return response()->json($templates);
    }

    public function showSmsTemplate($id): JsonResponse
    {
        $template = SmsTemplate::findOrFail($id);
        $template->segment_info = $template->getSegmentInfo();

        return response()->json(['data' => $template]);
    }

    public function storeSmsTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:sms_templates,code',
            'content' => 'required|string',
            'event_trigger' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template = SmsTemplate::create($validated);

        return response()->json(['data' => $template, 'message' => 'SMS template created'], 201);
    }

    public function updateSmsTemplate(Request $request, $id): JsonResponse
    {
        $template = SmsTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:sms_templates,code,' . $id,
            'content' => 'string',
            'event_trigger' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return response()->json(['data' => $template, 'message' => 'SMS template updated']);
    }

    public function deleteSmsTemplate($id): JsonResponse
    {
        $template = SmsTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'SMS template deleted']);
    }

    // =====================
    // PUSH TEMPLATES
    // =====================

    public function pushTemplates(Request $request): JsonResponse
    {
        $query = PushTemplate::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('event_trigger')) {
            $query->where('event_trigger', $request->event_trigger);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('name')->paginate($request->get('per_page', 20));

        return response()->json($templates);
    }

    public function showPushTemplate($id): JsonResponse
    {
        $template = PushTemplate::findOrFail($id);

        return response()->json(['data' => $template]);
    }

    public function storePushTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:push_templates,code',
            'title' => 'required|string|max:100',
            'body' => 'required|string',
            'icon' => 'nullable|string',
            'image' => 'nullable|string',
            'url' => 'nullable|string',
            'event_trigger' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template = PushTemplate::create($validated);

        return response()->json(['data' => $template, 'message' => 'Push template created'], 201);
    }

    public function updatePushTemplate(Request $request, $id): JsonResponse
    {
        $template = PushTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:push_templates,code,' . $id,
            'title' => 'string|max:100',
            'body' => 'string',
            'icon' => 'nullable|string',
            'image' => 'nullable|string',
            'url' => 'nullable|string',
            'event_trigger' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return response()->json(['data' => $template, 'message' => 'Push template updated']);
    }

    public function deletePushTemplate($id): JsonResponse
    {
        $template = PushTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Push template deleted']);
    }

    // =====================
    // NOTIFICATION CHANNELS
    // =====================

    public function channels(Request $request): JsonResponse
    {
        $query = NotificationChannel::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $channels = $query->orderBy('type')->orderBy('name')->get();

        return response()->json(['data' => $channels]);
    }

    public function showChannel($id): JsonResponse
    {
        $channel = NotificationChannel::findOrFail($id);

        return response()->json(['data' => $channel]);
    }

    public function storeChannel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:sms,push',
            'provider' => 'required|string',
            'name' => 'required|string|max:255',
            'credentials' => 'required|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $channel = NotificationChannel::create($validated);

        if ($request->boolean('is_default')) {
            $channel->setAsDefault();
        }

        return response()->json(['data' => $channel, 'message' => 'Channel created'], 201);
    }

    public function updateChannel(Request $request, $id): JsonResponse
    {
        $channel = NotificationChannel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'credentials' => 'array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $channel->update($validated);

        if ($request->has('is_default') && $request->boolean('is_default')) {
            $channel->setAsDefault();
        }

        return response()->json(['data' => $channel, 'message' => 'Channel updated']);
    }

    public function deleteChannel($id): JsonResponse
    {
        $channel = NotificationChannel::findOrFail($id);
        $channel->delete();

        return response()->json(['message' => 'Channel deleted']);
    }

    public function testChannel($id): JsonResponse
    {
        $channel = NotificationChannel::findOrFail($id);
        $result = $channel->testConnection();

        return response()->json($result);
    }

    public function getProviders(Request $request): JsonResponse
    {
        $type = $request->get('type', 'sms');
        $providers = NotificationChannel::getProviders($type);

        return response()->json(['data' => $providers]);
    }

    // =====================
    // CAMPAIGNS
    // =====================

    public function campaigns(Request $request): JsonResponse
    {
        $query = NotificationCampaign::with(['smsTemplate', 'pushTemplate', 'creator']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->orderByDesc('created_at')->paginate($request->get('per_page', 20));

        return response()->json($campaigns);
    }

    public function showCampaign($id): JsonResponse
    {
        $campaign = NotificationCampaign::with(['smsTemplate', 'pushTemplate', 'creator'])
            ->findOrFail($id);

        $campaign->sms_stats = $campaign->getSmsStats();
        $campaign->push_stats = $campaign->getPushStats();

        return response()->json(['data' => $campaign]);
    }

    public function storeCampaign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:sms,push,both',
            'sms_template_id' => 'nullable|exists:sms_templates,id',
            'push_template_id' => 'nullable|exists:push_templates,id',
            'audience_filters' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        $campaign = NotificationCampaign::create($validated);

        return response()->json(['data' => $campaign, 'message' => 'Campaign created'], 201);
    }

    public function updateCampaign(Request $request, $id): JsonResponse
    {
        $campaign = NotificationCampaign::findOrFail($id);

        if (!in_array($campaign->status, ['draft', 'scheduled'])) {
            return response()->json(['error' => 'Cannot update a campaign that has started'], 422);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'type' => 'in:sms,push,both',
            'sms_template_id' => 'nullable|exists:sms_templates,id',
            'push_template_id' => 'nullable|exists:push_templates,id',
            'audience_filters' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign->update($validated);

        return response()->json(['data' => $campaign, 'message' => 'Campaign updated']);
    }

    public function deleteCampaign($id): JsonResponse
    {
        $campaign = NotificationCampaign::findOrFail($id);

        if (!in_array($campaign->status, ['draft', 'cancelled'])) {
            return response()->json(['error' => 'Cannot delete an active campaign'], 422);
        }

        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted']);
    }

    public function scheduleCampaign(Request $request, $id): JsonResponse
    {
        $campaign = NotificationCampaign::findOrFail($id);

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $campaign->schedule(new \DateTime($validated['scheduled_at']));

        return response()->json(['data' => $campaign, 'message' => 'Campaign scheduled']);
    }

    public function sendCampaign($id): JsonResponse
    {
        $campaign = NotificationCampaign::findOrFail($id);

        if (!in_array($campaign->status, ['draft', 'scheduled'])) {
            return response()->json(['error' => 'Cannot send this campaign'], 422);
        }

        $campaign->start();

        // Here you would dispatch a job to actually send the notifications
        // dispatch(new SendNotificationCampaignJob($campaign));

        return response()->json(['data' => $campaign, 'message' => 'Campaign sending started']);
    }

    public function pauseCampaign($id): JsonResponse
    {
        $campaign = NotificationCampaign::findOrFail($id);

        if ($campaign->status !== 'sending') {
            return response()->json(['error' => 'Cannot pause this campaign'], 422);
        }

        $campaign->pause();

        return response()->json(['data' => $campaign, 'message' => 'Campaign paused']);
    }

    public function cancelCampaign($id): JsonResponse
    {
        $campaign = NotificationCampaign::findOrFail($id);

        if (in_array($campaign->status, ['sent', 'cancelled'])) {
            return response()->json(['error' => 'Cannot cancel this campaign'], 422);
        }

        $campaign->cancel();

        return response()->json(['data' => $campaign, 'message' => 'Campaign cancelled']);
    }

    // =====================
    // AUTOMATIONS
    // =====================

    public function automations(Request $request): JsonResponse
    {
        $query = NotificationAutomation::with(['smsTemplate', 'pushTemplate']);

        if ($request->has('trigger_event')) {
            $query->where('trigger_event', $request->trigger_event);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $automations = $query->orderBy('name')->paginate($request->get('per_page', 20));

        return response()->json($automations);
    }

    public function showAutomation($id): JsonResponse
    {
        $automation = NotificationAutomation::with(['smsTemplate', 'pushTemplate'])
            ->findOrFail($id);

        $automation->delivery_rate = $automation->getDeliveryRate();

        return response()->json(['data' => $automation]);
    }

    public function storeAutomation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_event' => 'required|string',
            'trigger_conditions' => 'nullable|array',
            'delay_minutes' => 'integer|min:0',
            'notification_type' => 'required|in:sms,push,both',
            'sms_template_id' => 'nullable|exists:sms_templates,id',
            'push_template_id' => 'nullable|exists:push_templates,id',
            'is_active' => 'boolean',
        ]);

        $automation = NotificationAutomation::create($validated);

        return response()->json(['data' => $automation, 'message' => 'Automation created'], 201);
    }

    public function updateAutomation(Request $request, $id): JsonResponse
    {
        $automation = NotificationAutomation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'trigger_event' => 'string',
            'trigger_conditions' => 'nullable|array',
            'delay_minutes' => 'integer|min:0',
            'notification_type' => 'in:sms,push,both',
            'sms_template_id' => 'nullable|exists:sms_templates,id',
            'push_template_id' => 'nullable|exists:push_templates,id',
            'is_active' => 'boolean',
        ]);

        $automation->update($validated);

        return response()->json(['data' => $automation, 'message' => 'Automation updated']);
    }

    public function deleteAutomation($id): JsonResponse
    {
        $automation = NotificationAutomation::findOrFail($id);
        $automation->delete();

        return response()->json(['message' => 'Automation deleted']);
    }

    public function toggleAutomation($id): JsonResponse
    {
        $automation = NotificationAutomation::findOrFail($id);
        $automation->toggleActive();

        return response()->json([
            'data' => $automation,
            'message' => $automation->is_active ? 'Automation activated' : 'Automation deactivated'
        ]);
    }

    public function getTriggerEvents(): JsonResponse
    {
        return response()->json(['data' => NotificationAutomation::getTriggerEvents()]);
    }

    // =====================
    // SMS MESSAGES LOG
    // =====================

    public function smsMessages(Request $request): JsonResponse
    {
        $query = SmsMessage::with(['customer', 'template']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $messages = $query->orderByDesc('created_at')->paginate($request->get('per_page', 50));

        return response()->json($messages);
    }

    public function showSmsMessage($id): JsonResponse
    {
        $message = SmsMessage::with(['customer', 'template'])->findOrFail($id);

        return response()->json(['data' => $message]);
    }

    // =====================
    // PUSH NOTIFICATIONS LOG
    // =====================

    public function pushNotifications(Request $request): JsonResponse
    {
        $query = PushNotification::with(['customer', 'template']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $notifications = $query->orderByDesc('created_at')->paginate($request->get('per_page', 50));

        return response()->json($notifications);
    }

    public function showPushNotification($id): JsonResponse
    {
        $notification = PushNotification::with(['customer', 'template'])->findOrFail($id);

        return response()->json(['data' => $notification]);
    }

    // =====================
    // DEVICE TOKENS
    // =====================

    public function deviceTokens(Request $request): JsonResponse
    {
        $query = CustomerDeviceToken::with('customer');

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $tokens = $query->orderByDesc('last_used_at')->paginate($request->get('per_page', 50));

        return response()->json($tokens);
    }

    public function deactivateToken($id): JsonResponse
    {
        $token = CustomerDeviceToken::findOrFail($id);
        $token->deactivate();

        return response()->json(['message' => 'Token deactivated']);
    }

    // =====================
    // STATS
    // =====================

    public function stats(): JsonResponse
    {
        $stats = [
            'sms' => [
                'templates' => SmsTemplate::where('is_active', true)->count(),
                'sent_today' => SmsMessage::whereDate('created_at', today())->count(),
                'sent_this_month' => SmsMessage::whereMonth('created_at', now()->month)->count(),
                'delivery_rate' => $this->calculateSmsDeliveryRate(),
                'total_cost_this_month' => SmsMessage::whereMonth('created_at', now()->month)->sum('cost'),
            ],
            'push' => [
                'templates' => PushTemplate::where('is_active', true)->count(),
                'sent_today' => PushNotification::whereDate('created_at', today())->count(),
                'sent_this_month' => PushNotification::whereMonth('created_at', now()->month)->count(),
                'delivery_rate' => $this->calculatePushDeliveryRate(),
                'click_rate' => $this->calculatePushClickRate(),
            ],
            'channels' => [
                'sms' => NotificationChannel::where('type', 'sms')->where('is_active', true)->count(),
                'push' => NotificationChannel::where('type', 'push')->where('is_active', true)->count(),
            ],
            'campaigns' => [
                'draft' => NotificationCampaign::where('status', 'draft')->count(),
                'scheduled' => NotificationCampaign::where('status', 'scheduled')->count(),
                'sending' => NotificationCampaign::where('status', 'sending')->count(),
                'sent' => NotificationCampaign::where('status', 'sent')->count(),
            ],
            'automations' => [
                'active' => NotificationAutomation::where('is_active', true)->count(),
                'inactive' => NotificationAutomation::where('is_active', false)->count(),
            ],
            'device_tokens' => [
                'total' => CustomerDeviceToken::where('is_active', true)->count(),
                'ios' => CustomerDeviceToken::where('is_active', true)->where('platform', 'ios')->count(),
                'android' => CustomerDeviceToken::where('is_active', true)->where('platform', 'android')->count(),
                'web' => CustomerDeviceToken::where('is_active', true)->where('platform', 'web')->count(),
            ],
        ];

        return response()->json(['data' => $stats]);
    }

    private function calculateSmsDeliveryRate(): float
    {
        $sent = SmsMessage::whereMonth('created_at', now()->month)
            ->whereIn('status', ['sent', 'delivered', 'failed'])
            ->count();

        if ($sent === 0) {
            return 0;
        }

        $delivered = SmsMessage::whereMonth('created_at', now()->month)
            ->where('status', 'delivered')
            ->count();

        return round(($delivered / $sent) * 100, 2);
    }

    private function calculatePushDeliveryRate(): float
    {
        $sent = PushNotification::whereMonth('created_at', now()->month)
            ->whereIn('status', ['sent', 'delivered', 'clicked', 'failed'])
            ->count();

        if ($sent === 0) {
            return 0;
        }

        $delivered = PushNotification::whereMonth('created_at', now()->month)
            ->whereIn('status', ['delivered', 'clicked'])
            ->count();

        return round(($delivered / $sent) * 100, 2);
    }

    private function calculatePushClickRate(): float
    {
        $delivered = PushNotification::whereMonth('created_at', now()->month)
            ->whereIn('status', ['delivered', 'clicked'])
            ->count();

        if ($delivered === 0) {
            return 0;
        }

        $clicked = PushNotification::whereMonth('created_at', now()->month)
            ->where('status', 'clicked')
            ->count();

        return round(($clicked / $delivered) * 100, 2);
    }
}
