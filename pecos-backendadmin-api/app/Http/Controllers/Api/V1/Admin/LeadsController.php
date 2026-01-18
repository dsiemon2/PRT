<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadsController extends Controller
{
    /**
     * Get all leads with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = DB::table('leads')
            ->leftJoin('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
            ->select(
                'leads.*',
                'lead_sources.name as source_name'
            )
            ->whereNull('leads.deleted_at');

        // Filters
        if ($request->status) {
            $query->where('leads.status', $request->status);
        }

        if ($request->priority) {
            $query->where('leads.priority', $request->priority);
        }

        if ($request->source_id) {
            $query->where('leads.source_id', $request->source_id);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('leads.first_name', 'like', $search)
                  ->orWhere('leads.last_name', 'like', $search)
                  ->orWhere('leads.email', 'like', $search)
                  ->orWhere('leads.company', 'like', $search)
                  ->orWhere('leads.lead_number', 'like', $search);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy("leads.{$sortField}", $sortDir);

        $perPage = $request->get('per_page', 20);
        $leads = $query->paginate($perPage);

        return response()->json($leads);
    }

    /**
     * Get lead statistics
     */
    public function stats()
    {
        $stats = [
            'total' => DB::table('leads')->whereNull('deleted_at')->count(),
            'new' => DB::table('leads')->where('status', 'new')->whereNull('deleted_at')->count(),
            'qualified' => DB::table('leads')->where('status', 'qualified')->whereNull('deleted_at')->count(),
            'proposal' => DB::table('leads')->where('status', 'proposal')->whereNull('deleted_at')->count(),
            'negotiation' => DB::table('leads')->where('status', 'negotiation')->whereNull('deleted_at')->count(),
            'won' => DB::table('leads')->where('status', 'won')->whereNull('deleted_at')->count(),
            'lost' => DB::table('leads')->where('status', 'lost')->whereNull('deleted_at')->count(),
            'hot_leads' => DB::table('leads')->where('priority', 'hot')->whereNotIn('status', ['won', 'lost'])->whereNull('deleted_at')->count(),
            'total_value' => DB::table('leads')->whereNotIn('status', ['won', 'lost'])->whereNull('deleted_at')->sum('estimated_value'),
            'avg_score' => round(DB::table('leads')->whereNull('deleted_at')->avg('lead_score') ?? 0),
        ];

        // By source
        $stats['by_source'] = DB::table('leads')
            ->leftJoin('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
            ->select('lead_sources.name as source', DB::raw('COUNT(*) as count'))
            ->whereNull('leads.deleted_at')
            ->groupBy('lead_sources.name')
            ->get();

        return response()->json($stats);
    }

    /**
     * Get a single lead with activities
     */
    public function show($id)
    {
        $lead = DB::table('leads')
            ->leftJoin('lead_sources', 'leads.source_id', '=', 'lead_sources.id')
            ->select('leads.*', 'lead_sources.name as source_name')
            ->where('leads.id', $id)
            ->first();

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        // Get activities
        $activities = DB::table('lead_activities')
            ->where('lead_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get related deals
        $deals = DB::table('deals')
            ->leftJoin('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
            ->select('deals.*', 'deal_stages.name as stage_name', 'deal_stages.color as stage_color')
            ->where('deals.lead_id', $id)
            ->whereNull('deals.deleted_at')
            ->get();

        return response()->json([
            'lead' => $lead,
            'activities' => $activities,
            'deals' => $deals,
        ]);
    }

    /**
     * Create a new lead
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Generate lead number
        $lastLead = DB::table('leads')->orderBy('id', 'desc')->first();
        $nextNum = $lastLead ? (intval(substr($lastLead->lead_number, 5)) + 1) : 1;
        $leadNumber = 'LEAD-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $id = DB::table('leads')->insertGetId([
            'lead_number' => $leadNumber,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'job_title' => $request->job_title,
            'source_id' => $request->source_id,
            'status' => $request->status ?? 'new',
            'priority' => $request->priority ?? 'medium',
            'estimated_value' => $request->estimated_value,
            'probability' => $request->probability ?? 0,
            'expected_close_date' => $request->expected_close_date,
            'assigned_to' => $request->assigned_to,
            'notes' => $request->notes,
            'lead_score' => $request->lead_score ?? 0,
            'customer_id' => $request->customer_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log activity
        $this->logActivity($id, 'note', 'Lead created', 'Lead was created in the system');

        return response()->json([
            'message' => 'Lead created successfully',
            'lead_id' => $id,
            'lead_number' => $leadNumber,
        ], 201);
    }

    /**
     * Update a lead
     */
    public function update(Request $request, $id)
    {
        $lead = DB::table('leads')->where('id', $id)->first();
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        $oldStatus = $lead->status;
        $updateData = [
            'updated_at' => now(),
        ];

        $fields = ['first_name', 'last_name', 'email', 'phone', 'company', 'job_title',
                   'source_id', 'status', 'priority', 'estimated_value', 'probability',
                   'expected_close_date', 'assigned_to', 'notes', 'lead_score', 'customer_id'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->$field;
            }
        }

        // Track status changes
        if (isset($updateData['status']) && $updateData['status'] !== $oldStatus) {
            if ($updateData['status'] === 'qualified') {
                $updateData['qualified_at'] = now();
            } elseif ($updateData['status'] === 'won') {
                $updateData['converted_at'] = now();
            } elseif ($updateData['status'] === 'lost' && $request->lost_reason) {
                $updateData['lost_reason'] = $request->lost_reason;
            }

            $this->logActivity($id, 'status_change', 'Status changed', "Status changed from {$oldStatus} to {$updateData['status']}");
        }

        DB::table('leads')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Lead updated successfully']);
    }

    /**
     * Delete a lead (soft delete)
     */
    public function destroy($id)
    {
        DB::table('leads')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Lead deleted successfully']);
    }

    /**
     * Add activity to a lead
     */
    public function addActivity(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:call,email,meeting,note,task,other',
            'subject' => 'required|string|max:255',
        ]);

        $activityId = DB::table('lead_activities')->insertGetId([
            'lead_id' => $id,
            'user_id' => $request->user_id ?? 1,
            'type' => $request->type,
            'subject' => $request->subject,
            'description' => $request->description,
            'outcome' => $request->outcome,
            'scheduled_at' => $request->scheduled_at,
            'completed_at' => $request->completed_at,
            'duration_minutes' => $request->duration_minutes,
            'metadata' => $request->metadata ? json_encode($request->metadata) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update last contacted timestamp
        DB::table('leads')->where('id', $id)->update([
            'last_contacted_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Activity added successfully',
            'activity_id' => $activityId,
        ], 201);
    }

    /**
     * Convert lead to deal
     */
    public function convertToDeal(Request $request, $id)
    {
        $lead = DB::table('leads')->where('id', $id)->first();
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        // Get first stage
        $firstStage = DB::table('deal_stages')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        // Generate deal number
        $lastDeal = DB::table('deals')->orderBy('id', 'desc')->first();
        $nextNum = $lastDeal ? (intval(substr($lastDeal->deal_number, 5)) + 1) : 1;
        $dealNumber = 'DEAL-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $dealId = DB::table('deals')->insertGetId([
            'deal_number' => $dealNumber,
            'title' => $request->title ?? "{$lead->company} - New Deal",
            'lead_id' => $id,
            'customer_id' => $lead->customer_id,
            'stage_id' => $request->stage_id ?? $firstStage->id,
            'value' => $request->value ?? $lead->estimated_value ?? 0,
            'probability' => $request->probability ?? $lead->probability ?? 0,
            'expected_close_date' => $request->expected_close_date ?? $lead->expected_close_date,
            'assigned_to' => $lead->assigned_to,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update lead status
        DB::table('leads')->where('id', $id)->update([
            'status' => 'won',
            'converted_at' => now(),
            'updated_at' => now(),
        ]);

        $this->logActivity($id, 'status_change', 'Converted to deal', "Lead converted to deal {$dealNumber}");

        return response()->json([
            'message' => 'Lead converted to deal successfully',
            'deal_id' => $dealId,
            'deal_number' => $dealNumber,
        ], 201);
    }

    /**
     * Get lead sources
     */
    public function sources()
    {
        $sources = DB::table('lead_sources')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($sources);
    }

    /**
     * Helper to log activity
     */
    private function logActivity($leadId, $type, $subject, $description = null)
    {
        DB::table('lead_activities')->insert([
            'lead_id' => $leadId,
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'outcome' => 'completed',
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
