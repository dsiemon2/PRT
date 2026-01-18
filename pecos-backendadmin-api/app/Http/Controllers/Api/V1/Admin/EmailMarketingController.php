<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailList;
use App\Models\EmailSubscriber;
use App\Models\EmailCampaign;
use App\Models\EmailAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailMarketingController extends Controller
{
    // =====================
    // EMAIL LISTS
    // =====================

    public function lists()
    {
        $lists = EmailList::withCount(['subscribers' => function ($q) {
            $q->where('status', 'subscribed');
        }])->orderBy('name')->get();

        return response()->json(['success' => true, 'data' => $lists]);
    }

    public function showList($id)
    {
        $list = EmailList::withCount(['subscribers' => function ($q) {
            $q->where('status', 'subscribed');
        }])->find($id);

        if (!$list) {
            return response()->json(['error' => 'List not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function storeList(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'double_optin' => 'boolean',
        ]);

        $list = EmailList::create($request->only(['name', 'description', 'double_optin', 'is_active']));

        return response()->json(['success' => true, 'message' => 'List created', 'data' => $list], 201);
    }

    public function updateList(Request $request, $id)
    {
        $list = EmailList::find($id);
        if (!$list) {
            return response()->json(['error' => 'List not found'], 404);
        }

        $list->update($request->only(['name', 'description', 'double_optin', 'is_active']));

        return response()->json(['success' => true, 'message' => 'List updated']);
    }

    public function destroyList($id)
    {
        $list = EmailList::find($id);
        if (!$list) {
            return response()->json(['error' => 'List not found'], 404);
        }

        $list->delete();

        return response()->json(['success' => true, 'message' => 'List deleted']);
    }

    // =====================
    // SUBSCRIBERS
    // =====================

    public function subscribers(Request $request, $listId)
    {
        $query = EmailSubscriber::where('email_list_id', $listId);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json(['success' => true, 'data' => $subscribers]);
    }

    public function storeSubscriber(Request $request, $listId)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
        ]);

        $list = EmailList::find($listId);
        if (!$list) {
            return response()->json(['error' => 'List not found'], 404);
        }

        $existing = EmailSubscriber::where('email_list_id', $listId)
            ->where('email', $request->email)
            ->first();

        if ($existing) {
            if ($existing->status === 'subscribed') {
                return response()->json(['error' => 'Email already subscribed'], 400);
            }
            $existing->subscribe();
            return response()->json(['success' => true, 'message' => 'Subscriber reactivated']);
        }

        $subscriber = EmailSubscriber::create([
            'email_list_id' => $listId,
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'status' => $list->double_optin ? 'pending' : 'subscribed',
            'source' => $request->source ?? 'admin',
            'subscribed_at' => $list->double_optin ? null : now(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'Subscriber added', 'data' => $subscriber], 201);
    }

    public function unsubscribe(Request $request, $id)
    {
        $subscriber = EmailSubscriber::find($id);
        if (!$subscriber) {
            return response()->json(['error' => 'Subscriber not found'], 404);
        }

        $subscriber->unsubscribe($request->reason);

        return response()->json(['success' => true, 'message' => 'Unsubscribed successfully']);
    }

    public function destroySubscriber($id)
    {
        $subscriber = EmailSubscriber::find($id);
        if (!$subscriber) {
            return response()->json(['error' => 'Subscriber not found'], 404);
        }

        $subscriber->delete();

        return response()->json(['success' => true, 'message' => 'Subscriber deleted']);
    }

    public function importSubscribers(Request $request, $listId)
    {
        $request->validate([
            'subscribers' => 'required|array',
            'subscribers.*.email' => 'required|email',
        ]);

        $list = EmailList::find($listId);
        if (!$list) {
            return response()->json(['error' => 'List not found'], 404);
        }

        $imported = 0;
        $skipped = 0;

        foreach ($request->subscribers as $sub) {
            $existing = EmailSubscriber::where('email_list_id', $listId)
                ->where('email', $sub['email'])
                ->exists();

            if ($existing) {
                $skipped++;
                continue;
            }

            EmailSubscriber::create([
                'email_list_id' => $listId,
                'email' => $sub['email'],
                'first_name' => $sub['first_name'] ?? null,
                'last_name' => $sub['last_name'] ?? null,
                'status' => 'subscribed',
                'source' => 'import',
                'subscribed_at' => now(),
            ]);
            $imported++;
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$imported} subscribers, skipped {$skipped} duplicates"
        ]);
    }

    // =====================
    // CAMPAIGNS
    // =====================

    public function campaigns(Request $request)
    {
        $query = EmailCampaign::with(['emailList:id,name']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $campaigns = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json(['success' => true, 'data' => $campaigns]);
    }

    public function showCampaign($id)
    {
        $campaign = EmailCampaign::with(['emailList', 'template'])->find($id);

        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        // Add performance metrics
        $campaign->open_rate = $campaign->getOpenRate();
        $campaign->click_rate = $campaign->getClickRate();
        $campaign->bounce_rate = $campaign->getBounceRate();
        $campaign->unsubscribe_rate = $campaign->getUnsubscribeRate();

        return response()->json(['success' => true, 'data' => $campaign]);
    }

    public function storeCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:100',
            'from_email' => 'required|email',
            'email_list_id' => 'required|exists:email_lists,id',
        ]);

        $campaign = EmailCampaign::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'preview_text' => $request->preview_text,
            'from_name' => $request->from_name,
            'from_email' => $request->from_email,
            'reply_to' => $request->reply_to ?? $request->from_email,
            'type' => $request->type ?? 'regular',
            'status' => 'draft',
            'html_content' => $request->html_content,
            'text_content' => $request->text_content,
            'template_id' => $request->template_id,
            'email_list_id' => $request->email_list_id,
            'segment_id' => $request->segment_id,
            'settings' => $request->settings,
        ]);

        return response()->json(['success' => true, 'message' => 'Campaign created', 'data' => $campaign], 201);
    }

    public function updateCampaign(Request $request, $id)
    {
        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        if (!in_array($campaign->status, ['draft', 'scheduled'])) {
            return response()->json(['error' => 'Cannot edit a campaign that has been sent'], 400);
        }

        $campaign->update($request->only([
            'name', 'subject', 'preview_text', 'from_name', 'from_email', 'reply_to',
            'html_content', 'text_content', 'template_id', 'email_list_id', 'segment_id', 'settings'
        ]));

        return response()->json(['success' => true, 'message' => 'Campaign updated']);
    }

    public function scheduleCampaign(Request $request, $id)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        if ($campaign->status !== 'draft') {
            return response()->json(['error' => 'Only draft campaigns can be scheduled'], 400);
        }

        // Count recipients
        $recipientCount = EmailSubscriber::where('email_list_id', $campaign->email_list_id)
            ->where('status', 'subscribed')
            ->count();

        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => $request->scheduled_at,
            'total_recipients' => $recipientCount,
        ]);

        return response()->json(['success' => true, 'message' => 'Campaign scheduled']);
    }

    public function sendCampaign($id)
    {
        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        if (!in_array($campaign->status, ['draft', 'scheduled'])) {
            return response()->json(['error' => 'Campaign cannot be sent'], 400);
        }

        // In production, this would queue the campaign for sending
        $recipientCount = EmailSubscriber::where('email_list_id', $campaign->email_list_id)
            ->where('status', 'subscribed')
            ->count();

        $campaign->update([
            'status' => 'sending',
            'sent_at' => now(),
            'total_recipients' => $recipientCount,
        ]);

        // Queue the actual send job here
        // SendCampaignJob::dispatch($campaign);

        return response()->json(['success' => true, 'message' => 'Campaign is being sent']);
    }

    public function pauseCampaign($id)
    {
        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        if ($campaign->status !== 'sending') {
            return response()->json(['error' => 'Only sending campaigns can be paused'], 400);
        }

        $campaign->update(['status' => 'paused']);

        return response()->json(['success' => true, 'message' => 'Campaign paused']);
    }

    public function cancelCampaign($id)
    {
        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        if (!in_array($campaign->status, ['scheduled', 'sending', 'paused'])) {
            return response()->json(['error' => 'Campaign cannot be cancelled'], 400);
        }

        $campaign->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Campaign cancelled']);
    }

    public function duplicateCampaign($id)
    {
        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (Copy)';
        $newCampaign->status = 'draft';
        $newCampaign->scheduled_at = null;
        $newCampaign->sent_at = null;
        $newCampaign->total_recipients = 0;
        $newCampaign->sent_count = 0;
        $newCampaign->open_count = 0;
        $newCampaign->click_count = 0;
        $newCampaign->bounce_count = 0;
        $newCampaign->unsubscribe_count = 0;
        $newCampaign->spam_count = 0;
        $newCampaign->save();

        return response()->json(['success' => true, 'message' => 'Campaign duplicated', 'data' => $newCampaign]);
    }

    public function destroyCampaign($id)
    {
        $campaign = EmailCampaign::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }

        $campaign->delete();

        return response()->json(['success' => true, 'message' => 'Campaign deleted']);
    }

    // =====================
    // AUTOMATIONS
    // =====================

    public function automations()
    {
        $automations = EmailAutomation::with(['emailList:id,name'])
            ->withCount('steps')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $automations]);
    }

    public function showAutomation($id)
    {
        $automation = EmailAutomation::with(['emailList', 'steps'])->find($id);

        if (!$automation) {
            return response()->json(['error' => 'Automation not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $automation]);
    }

    public function storeAutomation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string',
        ]);

        $automation = EmailAutomation::create([
            'name' => $request->name,
            'description' => $request->description,
            'trigger_type' => $request->trigger_type,
            'trigger_conditions' => $request->trigger_conditions,
            'email_list_id' => $request->email_list_id,
            'is_active' => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Automation created', 'data' => $automation], 201);
    }

    public function updateAutomation(Request $request, $id)
    {
        $automation = EmailAutomation::find($id);
        if (!$automation) {
            return response()->json(['error' => 'Automation not found'], 404);
        }

        $automation->update($request->only([
            'name', 'description', 'trigger_type', 'trigger_conditions', 'email_list_id'
        ]));

        return response()->json(['success' => true, 'message' => 'Automation updated']);
    }

    public function toggleAutomation($id)
    {
        $automation = EmailAutomation::find($id);
        if (!$automation) {
            return response()->json(['error' => 'Automation not found'], 404);
        }

        $automation->is_active = !$automation->is_active;
        $automation->save();

        return response()->json([
            'success' => true,
            'message' => $automation->is_active ? 'Automation activated' : 'Automation deactivated'
        ]);
    }

    public function destroyAutomation($id)
    {
        $automation = EmailAutomation::find($id);
        if (!$automation) {
            return response()->json(['error' => 'Automation not found'], 404);
        }

        $automation->delete();

        return response()->json(['success' => true, 'message' => 'Automation deleted']);
    }

    // =====================
    // STATS
    // =====================

    public function stats()
    {
        $stats = [
            'total_lists' => EmailList::count(),
            'total_subscribers' => EmailSubscriber::where('status', 'subscribed')->count(),
            'total_campaigns' => EmailCampaign::count(),
            'sent_campaigns' => EmailCampaign::where('status', 'sent')->count(),
            'draft_campaigns' => EmailCampaign::where('status', 'draft')->count(),
            'scheduled_campaigns' => EmailCampaign::where('status', 'scheduled')->count(),
            'total_automations' => EmailAutomation::count(),
            'active_automations' => EmailAutomation::where('is_active', true)->count(),
            'total_emails_sent' => EmailCampaign::sum('sent_count'),
            'total_opens' => EmailCampaign::sum('open_count'),
            'total_clicks' => EmailCampaign::sum('click_count'),
            'avg_open_rate' => $this->calculateAvgOpenRate(),
            'avg_click_rate' => $this->calculateAvgClickRate(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    private function calculateAvgOpenRate(): float
    {
        $campaigns = EmailCampaign::where('sent_count', '>', 0)->get();
        if ($campaigns->isEmpty()) return 0;

        $totalRate = $campaigns->sum(function ($c) {
            return $c->getOpenRate();
        });

        return round($totalRate / $campaigns->count(), 2);
    }

    private function calculateAvgClickRate(): float
    {
        $campaigns = EmailCampaign::where('sent_count', '>', 0)->get();
        if ($campaigns->isEmpty()) return 0;

        $totalRate = $campaigns->sum(function ($c) {
            return $c->getClickRate();
        });

        return round($totalRate / $campaigns->count(), 2);
    }
}
