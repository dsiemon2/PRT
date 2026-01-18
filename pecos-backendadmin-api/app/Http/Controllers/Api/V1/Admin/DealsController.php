<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DealsController extends Controller
{
    /**
     * Get all deals with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = DB::table('deals')
            ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
            ->leftJoin('leads', 'deals.lead_id', '=', 'leads.id')
            ->select(
                'deals.*',
                'deal_stages.name as stage_name',
                'deal_stages.color as stage_color',
                'deal_stages.is_won',
                'deal_stages.is_lost',
                'leads.first_name as lead_first_name',
                'leads.last_name as lead_last_name',
                'leads.company as lead_company'
            )
            ->whereNull('deals.deleted_at');

        // Filters
        if ($request->stage_id) {
            $query->where('deals.stage_id', $request->stage_id);
        }

        if ($request->assigned_to) {
            $query->where('deals.assigned_to', $request->assigned_to);
        }

        if ($request->is_won) {
            $query->where('deal_stages.is_won', true);
        }

        if ($request->is_lost) {
            $query->where('deal_stages.is_lost', true);
        }

        if ($request->is_open) {
            $query->where('deal_stages.is_won', false)
                  ->where('deal_stages.is_lost', false);
        }

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('deals.title', 'like', $search)
                  ->orWhere('deals.deal_number', 'like', $search)
                  ->orWhere('leads.company', 'like', $search);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy("deals.{$sortField}", $sortDir);

        $perPage = $request->get('per_page', 20);
        $deals = $query->paginate($perPage);

        return response()->json($deals);
    }

    /**
     * Get deal pipeline view (grouped by stage)
     */
    public function pipeline()
    {
        $stages = DB::table('deal_stages')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $pipeline = [];
        foreach ($stages as $stage) {
            $deals = DB::table('deals')
                ->leftJoin('leads', 'deals.lead_id', '=', 'leads.id')
                ->select(
                    'deals.*',
                    'leads.company as lead_company',
                    'leads.first_name as lead_first_name',
                    'leads.last_name as lead_last_name'
                )
                ->where('deals.stage_id', $stage->id)
                ->whereNull('deals.deleted_at')
                ->orderBy('deals.expected_close_date')
                ->get();

            $pipeline[] = [
                'stage' => $stage,
                'deals' => $deals,
                'total_value' => $deals->sum('value'),
                'count' => $deals->count(),
            ];
        }

        return response()->json($pipeline);
    }

    /**
     * Get deal statistics
     */
    public function stats()
    {
        $openDeals = DB::table('deals')
            ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
            ->where('deal_stages.is_won', false)
            ->where('deal_stages.is_lost', false)
            ->whereNull('deals.deleted_at');

        $stats = [
            'total_open' => (clone $openDeals)->count(),
            'total_value' => (clone $openDeals)->sum('deals.value'),
            'weighted_value' => (clone $openDeals)->selectRaw('SUM(deals.value * deals.probability / 100) as weighted')->first()->weighted ?? 0,
            'avg_deal_value' => round((clone $openDeals)->avg('deals.value') ?? 0, 2),
            'won_this_month' => DB::table('deals')
                ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
                ->where('deal_stages.is_won', true)
                ->whereMonth('deals.won_at', Carbon::now()->month)
                ->whereYear('deals.won_at', Carbon::now()->year)
                ->count(),
            'won_value_this_month' => DB::table('deals')
                ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
                ->where('deal_stages.is_won', true)
                ->whereMonth('deals.won_at', Carbon::now()->month)
                ->whereYear('deals.won_at', Carbon::now()->year)
                ->sum('deals.value'),
            'lost_this_month' => DB::table('deals')
                ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
                ->where('deal_stages.is_lost', true)
                ->whereMonth('deals.lost_at', Carbon::now()->month)
                ->whereYear('deals.lost_at', Carbon::now()->year)
                ->count(),
            'closing_soon' => (clone $openDeals)
                ->where('deals.expected_close_date', '<=', Carbon::now()->addDays(7))
                ->where('deals.expected_close_date', '>=', Carbon::now())
                ->count(),
        ];

        // By stage
        $stats['by_stage'] = DB::table('deals')
            ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
            ->select(
                'deal_stages.name',
                'deal_stages.color',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(deals.value) as total_value')
            )
            ->whereNull('deals.deleted_at')
            ->groupBy('deal_stages.id', 'deal_stages.name', 'deal_stages.color')
            ->orderBy('deal_stages.sort_order')
            ->get();

        return response()->json($stats);
    }

    /**
     * Get a single deal
     */
    public function show($id)
    {
        $deal = DB::table('deals')
            ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
            ->leftJoin('leads', 'deals.lead_id', '=', 'leads.id')
            ->select(
                'deals.*',
                'deal_stages.name as stage_name',
                'deal_stages.color as stage_color',
                'deal_stages.is_won',
                'deal_stages.is_lost',
                'leads.first_name as lead_first_name',
                'leads.last_name as lead_last_name',
                'leads.email as lead_email',
                'leads.phone as lead_phone',
                'leads.company as lead_company'
            )
            ->where('deals.id', $id)
            ->first();

        if (!$deal) {
            return response()->json(['error' => 'Deal not found'], 404);
        }

        // Get activities
        $activities = DB::table('deal_activities')
            ->where('deal_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get stages for pipeline view
        $stages = DB::table('deal_stages')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'deal' => $deal,
            'activities' => $activities,
            'stages' => $stages,
        ]);
    }

    /**
     * Create a new deal
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'stage_id' => 'required|exists:deal_stages,id',
            'value' => 'required|numeric|min:0',
        ]);

        // Generate deal number
        $lastDeal = DB::table('deals')->orderBy('id', 'desc')->first();
        $nextNum = $lastDeal ? (intval(substr($lastDeal->deal_number, 5)) + 1) : 1;
        $dealNumber = 'DEAL-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // Get stage probability
        $stage = DB::table('deal_stages')->where('id', $request->stage_id)->first();

        $id = DB::table('deals')->insertGetId([
            'deal_number' => $dealNumber,
            'title' => $request->title,
            'lead_id' => $request->lead_id,
            'customer_id' => $request->customer_id,
            'stage_id' => $request->stage_id,
            'value' => $request->value,
            'currency' => $request->currency ?? 'USD',
            'probability' => $request->probability ?? $stage->probability ?? 0,
            'expected_close_date' => $request->expected_close_date,
            'assigned_to' => $request->assigned_to,
            'notes' => $request->notes,
            'line_items' => $request->line_items ? json_encode($request->line_items) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log activity
        $this->logActivity($id, 'note', 'Deal created', "Deal {$dealNumber} was created");

        return response()->json([
            'message' => 'Deal created successfully',
            'deal_id' => $id,
            'deal_number' => $dealNumber,
        ], 201);
    }

    /**
     * Update a deal
     */
    public function update(Request $request, $id)
    {
        $deal = DB::table('deals')->where('id', $id)->first();
        if (!$deal) {
            return response()->json(['error' => 'Deal not found'], 404);
        }

        $oldStageId = $deal->stage_id;
        $updateData = ['updated_at' => now()];

        $fields = ['title', 'stage_id', 'value', 'currency', 'probability',
                   'expected_close_date', 'actual_close_date', 'assigned_to',
                   'notes', 'line_items', 'customer_id'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $value = $request->$field;
                if ($field === 'line_items' && is_array($value)) {
                    $value = json_encode($value);
                }
                $updateData[$field] = $value;
            }
        }

        // Handle stage change
        if (isset($updateData['stage_id']) && $updateData['stage_id'] != $oldStageId) {
            $newStage = DB::table('deal_stages')->where('id', $updateData['stage_id'])->first();
            $oldStage = DB::table('deal_stages')->where('id', $oldStageId)->first();

            if ($newStage->is_won) {
                $updateData['won_at'] = now();
                $updateData['actual_close_date'] = now();
            } elseif ($newStage->is_lost) {
                $updateData['lost_at'] = now();
                $updateData['actual_close_date'] = now();
                if ($request->lost_reason) {
                    $updateData['lost_reason'] = $request->lost_reason;
                }
            }

            // Update probability based on stage
            if (!$request->has('probability')) {
                $updateData['probability'] = $newStage->probability;
            }

            $this->logActivity($id, 'stage_change', 'Stage changed',
                "Stage changed from {$oldStage->name} to {$newStage->name}");
        }

        DB::table('deals')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Deal updated successfully']);
    }

    /**
     * Delete a deal (soft delete)
     */
    public function destroy($id)
    {
        DB::table('deals')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Deal deleted successfully']);
    }

    /**
     * Move deal to a different stage
     */
    public function moveStage(Request $request, $id)
    {
        $request->validate([
            'stage_id' => 'required|exists:deal_stages,id',
        ]);

        return $this->update($request, $id);
    }

    /**
     * Add activity to a deal
     */
    public function addActivity(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:call,email,meeting,note,task,stage_change,other',
            'subject' => 'required|string|max:255',
        ]);

        $activityId = DB::table('deal_activities')->insertGetId([
            'deal_id' => $id,
            'user_id' => $request->user_id ?? 1,
            'type' => $request->type,
            'subject' => $request->subject,
            'description' => $request->description,
            'metadata' => $request->metadata ? json_encode($request->metadata) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Activity added successfully',
            'activity_id' => $activityId,
        ], 201);
    }

    /**
     * Get deal stages
     */
    public function stages()
    {
        $stages = DB::table('deal_stages')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($stages);
    }

    /**
     * Helper to log activity
     */
    private function logActivity($dealId, $type, $subject, $description = null)
    {
        DB::table('deal_activities')->insert([
            'deal_id' => $dealId,
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
