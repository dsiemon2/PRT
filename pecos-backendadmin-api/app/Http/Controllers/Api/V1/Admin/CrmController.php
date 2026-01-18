<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CRM Controller
 *
 * Handles all CRM-related API endpoints:
 * - Customer Tags
 * - Customer Activities/Timeline
 * - Customer Notes
 * - Customer Communications
 * - Customer Segments
 * - Customer Metrics
 */
class CrmController extends Controller
{
    // ==========================================
    // CUSTOMER TAGS
    // ==========================================

    /**
     * Get all available tags
     */
    public function tags(Request $request)
    {
        $query = DB::table('customer_tags');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('is_auto')) {
            $query->where('is_auto', $request->boolean('is_auto'));
        }

        $tags = $query->orderBy('usage_count', 'desc')->get();

        return response()->json(['data' => $tags]);
    }

    /**
     * Create a new tag
     */
    public function createTag(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:customer_tags,name',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string'
        ]);

        $id = DB::table('customer_tags')->insertGetId([
            'name' => $request->name,
            'color' => $request->input('color', '#6c757d'),
            'description' => $request->description,
            'is_auto' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Tag created successfully',
            'id' => $id
        ], 201);
    }

    /**
     * Update a tag
     */
    public function updateTag(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:50|unique:customer_tags,name,' . $id,
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string'
        ]);

        $updateData = ['updated_at' => now()];
        if ($request->filled('name')) $updateData['name'] = $request->name;
        if ($request->filled('color')) $updateData['color'] = $request->color;
        if ($request->has('description')) $updateData['description'] = $request->description;

        DB::table('customer_tags')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Tag updated successfully']);
    }

    /**
     * Delete a tag
     */
    public function deleteTag($id)
    {
        // Remove all assignments first
        DB::table('customer_tag_assignments')->where('tag_id', $id)->delete();
        DB::table('customer_tags')->where('id', $id)->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    /**
     * Get tags for a specific customer
     */
    public function customerTags($customerId)
    {
        $tags = DB::table('customer_tag_assignments')
            ->join('customer_tags', 'customer_tag_assignments.tag_id', '=', 'customer_tags.id')
            ->where('customer_tag_assignments.customer_id', $customerId)
            ->select('customer_tags.*', 'customer_tag_assignments.assigned_at', 'customer_tag_assignments.assigned_by')
            ->get();

        return response()->json(['data' => $tags]);
    }

    /**
     * Assign tag to customer
     */
    public function assignTag(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'tag_id' => 'required|integer|exists:customer_tags,id'
        ]);

        // Check if already assigned
        $exists = DB::table('customer_tag_assignments')
            ->where('customer_id', $request->customer_id)
            ->where('tag_id', $request->tag_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Tag already assigned'], 400);
        }

        DB::table('customer_tag_assignments')->insert([
            'customer_id' => $request->customer_id,
            'tag_id' => $request->tag_id,
            'assigned_by' => $request->input('assigned_by'),
            'assigned_at' => now()
        ]);

        // Update usage count
        DB::table('customer_tags')
            ->where('id', $request->tag_id)
            ->increment('usage_count');

        // Log activity
        $this->logActivity($request->customer_id, 'other', 'Tag assigned', null, [
            'tag_id' => $request->tag_id
        ]);

        return response()->json(['message' => 'Tag assigned successfully']);
    }

    /**
     * Remove tag from customer
     */
    public function removeTag(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'tag_id' => 'required|integer'
        ]);

        $deleted = DB::table('customer_tag_assignments')
            ->where('customer_id', $request->customer_id)
            ->where('tag_id', $request->tag_id)
            ->delete();

        if ($deleted) {
            DB::table('customer_tags')
                ->where('id', $request->tag_id)
                ->decrement('usage_count');
        }

        return response()->json(['message' => 'Tag removed successfully']);
    }

    // ==========================================
    // CUSTOMER ACTIVITIES / TIMELINE
    // ==========================================

    /**
     * Get customer activity timeline
     */
    public function activities($customerId, Request $request)
    {
        $query = DB::table('customer_activities')
            ->where('customer_id', $customerId);

        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->input('per_page', 20);
        $activities = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($activities);
    }

    /**
     * Log a customer activity
     */
    public function logActivity($customerId, $type, $title, $description = null, $metadata = null, $createdBy = null)
    {
        return DB::table('customer_activities')->insertGetId([
            'customer_id' => $customerId,
            'activity_type' => $type,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_by' => $createdBy,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Create activity via API
     */
    public function createActivity(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'activity_type' => 'required|in:order,email,support,review,loyalty,login,note,wishlist,cart,account,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'metadata' => 'nullable|array'
        ]);

        $id = $this->logActivity(
            $request->customer_id,
            $request->activity_type,
            $request->title,
            $request->description,
            $request->metadata,
            $request->input('created_by')
        );

        return response()->json([
            'message' => 'Activity logged successfully',
            'id' => $id
        ], 201);
    }

    // ==========================================
    // CUSTOMER NOTES
    // ==========================================

    /**
     * Get customer notes
     */
    public function notes($customerId, Request $request)
    {
        $query = DB::table('customer_notes')
            ->where('customer_id', $customerId);

        // Filter private notes if not the author
        if ($request->filled('user_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('is_private', false)
                    ->orWhere('created_by', $request->user_id);
            });
        } else {
            $query->where('is_private', false);
        }

        $notes = $query->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $notes]);
    }

    /**
     * Create a customer note
     */
    public function createNote(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'note' => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'is_private' => 'nullable|boolean',
            'created_by' => 'required|integer'
        ]);

        $id = DB::table('customer_notes')->insertGetId([
            'customer_id' => $request->customer_id,
            'note' => $request->note,
            'is_pinned' => $request->boolean('is_pinned', false),
            'is_private' => $request->boolean('is_private', false),
            'created_by' => $request->created_by,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log activity
        $this->logActivity($request->customer_id, 'note', 'Note added', null, [
            'note_id' => $id,
            'preview' => substr($request->note, 0, 100)
        ], $request->created_by);

        return response()->json([
            'message' => 'Note created successfully',
            'id' => $id
        ], 201);
    }

    /**
     * Update a note
     */
    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string',
            'is_pinned' => 'nullable|boolean',
            'is_private' => 'nullable|boolean'
        ]);

        $updateData = ['updated_at' => now()];
        if ($request->filled('note')) $updateData['note'] = $request->note;
        if ($request->has('is_pinned')) $updateData['is_pinned'] = $request->boolean('is_pinned');
        if ($request->has('is_private')) $updateData['is_private'] = $request->boolean('is_private');

        DB::table('customer_notes')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Note updated successfully']);
    }

    /**
     * Delete a note
     */
    public function deleteNote($id)
    {
        DB::table('customer_notes')->where('id', $id)->delete();
        return response()->json(['message' => 'Note deleted successfully']);
    }

    // ==========================================
    // CUSTOMER COMMUNICATIONS
    // ==========================================

    /**
     * Get customer communications
     */
    public function communications($customerId, Request $request)
    {
        $query = DB::table('customer_communications')
            ->where('customer_id', $customerId);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        $perPage = $request->input('per_page', 20);
        $communications = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($communications);
    }

    /**
     * Log a communication
     */
    public function logCommunication(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'type' => 'required|in:email,sms,chat,phone,social,internal',
            'direction' => 'required|in:inbound,outbound',
            'subject' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'status' => 'nullable|in:draft,scheduled,sent,delivered,opened,clicked,bounced,failed'
        ]);

        $id = DB::table('customer_communications')->insertGetId([
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'direction' => $request->direction,
            'subject' => $request->subject,
            'content' => $request->content,
            'template_id' => $request->template_id,
            'status' => $request->input('status', 'sent'),
            'scheduled_at' => $request->scheduled_at,
            'sent_at' => $request->direction == 'outbound' ? now() : null,
            'metadata' => $request->metadata ? json_encode($request->metadata) : null,
            'created_by' => $request->created_by,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log activity
        $this->logActivity($request->customer_id, 'email', 'Communication logged', $request->subject, [
            'communication_id' => $id,
            'type' => $request->type,
            'direction' => $request->direction
        ], $request->created_by);

        return response()->json([
            'message' => 'Communication logged successfully',
            'id' => $id
        ], 201);
    }

    // ==========================================
    // CUSTOMER SEGMENTS
    // ==========================================

    /**
     * Get all segments
     */
    public function segments(Request $request)
    {
        $query = DB::table('customer_segments');

        if ($request->filled('is_preset')) {
            $query->where('is_preset', $request->boolean('is_preset'));
        }

        $segments = $query->orderBy('name')->get();

        return response()->json(['data' => $segments]);
    }

    /**
     * Get segment details with members
     */
    public function segmentDetails($id)
    {
        $segment = DB::table('customer_segments')->where('id', $id)->first();

        if (!$segment) {
            return response()->json(['error' => 'Segment not found'], 404);
        }

        // Get member preview (first 10)
        $members = DB::table('customer_segment_members')
            ->join('users', 'customer_segment_members.customer_id', '=', 'users.id')
            ->where('customer_segment_members.segment_id', $id)
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->limit(10)
            ->get();

        return response()->json([
            'segment' => $segment,
            'member_preview' => $members
        ]);
    }

    /**
     * Create a segment
     */
    public function createSegment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:customer_segments,name',
            'description' => 'nullable|string',
            'rules' => 'required|array',
            'is_dynamic' => 'nullable|boolean'
        ]);

        $id = DB::table('customer_segments')->insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'rules' => json_encode($request->rules),
            'is_dynamic' => $request->boolean('is_dynamic', true),
            'is_preset' => false,
            'created_by' => $request->created_by,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Calculate initial membership
        $this->recalculateSegment($id);

        return response()->json([
            'message' => 'Segment created successfully',
            'id' => $id
        ], 201);
    }

    /**
     * Update a segment
     */
    public function updateSegment(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:100|unique:customer_segments,name,' . $id,
            'description' => 'nullable|string',
            'rules' => 'nullable|array',
            'is_dynamic' => 'nullable|boolean'
        ]);

        $updateData = ['updated_at' => now()];
        if ($request->filled('name')) $updateData['name'] = $request->name;
        if ($request->has('description')) $updateData['description'] = $request->description;
        if ($request->filled('rules')) $updateData['rules'] = json_encode($request->rules);
        if ($request->has('is_dynamic')) $updateData['is_dynamic'] = $request->boolean('is_dynamic');

        DB::table('customer_segments')->where('id', $id)->update($updateData);

        // Recalculate if rules changed
        if ($request->filled('rules')) {
            $this->recalculateSegment($id);
        }

        return response()->json(['message' => 'Segment updated successfully']);
    }

    /**
     * Delete a segment
     */
    public function deleteSegment($id)
    {
        // Check if preset
        $segment = DB::table('customer_segments')->where('id', $id)->first();
        if ($segment && $segment->is_preset) {
            return response()->json(['error' => 'Cannot delete preset segments'], 400);
        }

        DB::table('customer_segment_members')->where('segment_id', $id)->delete();
        DB::table('customer_segments')->where('id', $id)->delete();

        return response()->json(['message' => 'Segment deleted successfully']);
    }

    /**
     * Export segment members
     */
    public function exportSegment($id)
    {
        $members = DB::table('customer_segment_members')
            ->join('users', 'customer_segment_members.customer_id', '=', 'users.id')
            ->where('customer_segment_members.segment_id', $id)
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.created_at')
            ->get();

        return response()->json(['data' => $members]);
    }

    /**
     * Recalculate segment membership
     */
    public function recalculateSegment($id)
    {
        $segment = DB::table('customer_segments')->where('id', $id)->first();
        if (!$segment) return;

        $rules = json_decode($segment->rules, true);
        $query = DB::table('users')->where('role', 'customer');

        // Apply rules
        foreach ($rules as $rule) {
            $field = $rule['field'] ?? '';
            $operator = $rule['operator'] ?? '=';
            $value = $rule['value'] ?? '';

            switch ($field) {
                case 'total_spent':
                    $subquery = DB::table('orders')
                        ->selectRaw('user_id, COALESCE(SUM(total_amount), 0) as total')
                        ->whereNotIn('status', ['cancelled', 'refunded'])
                        ->groupBy('user_id');

                    $query->joinSub($subquery, 'order_totals', function ($join) {
                        $join->on('users.id', '=', 'order_totals.user_id');
                    });
                    $query->where('order_totals.total', $operator, $value);
                    break;

                case 'order_count':
                    $subquery = DB::table('orders')
                        ->selectRaw('user_id, COUNT(*) as cnt')
                        ->whereNotIn('status', ['cancelled', 'refunded'])
                        ->groupBy('user_id');

                    $query->joinSub($subquery, 'order_counts', function ($join) {
                        $join->on('users.id', '=', 'order_counts.user_id');
                    });
                    $query->where('order_counts.cnt', $operator, $value);
                    break;

                case 'last_order_days':
                    $subquery = DB::table('orders')
                        ->selectRaw('user_id, MAX(order_date) as last_order')
                        ->groupBy('user_id');

                    $query->joinSub($subquery, 'last_orders', function ($join) {
                        $join->on('users.id', '=', 'last_orders.user_id');
                    });
                    $query->whereRaw("DATEDIFF(NOW(), last_orders.last_order) $operator ?", [$value]);
                    break;

                case 'created_at':
                    if ($operator == 'within_days') {
                        $query->where('users.created_at', '>=', now()->subDays($value));
                    } else {
                        $query->where('users.created_at', $operator, $value);
                    }
                    break;

                case 'loyalty_tier':
                    $query->join('loyalty_members', 'users.id', '=', 'loyalty_members.user_id')
                        ->join('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
                        ->where('loyalty_tiers.tier_name', $operator, $value);
                    break;

                case 'has_tag':
                    $query->whereExists(function ($q) use ($value) {
                        $q->select(DB::raw(1))
                            ->from('customer_tag_assignments')
                            ->join('customer_tags', 'customer_tag_assignments.tag_id', '=', 'customer_tags.id')
                            ->whereColumn('customer_tag_assignments.customer_id', 'users.id')
                            ->where('customer_tags.name', $value);
                    });
                    break;
            }
        }

        $customerIds = $query->pluck('users.id');

        // Clear existing and insert new
        DB::table('customer_segment_members')->where('segment_id', $id)->delete();

        $inserts = [];
        foreach ($customerIds as $customerId) {
            $inserts[] = [
                'segment_id' => $id,
                'customer_id' => $customerId,
                'added_at' => now()
            ];
        }

        if (!empty($inserts)) {
            DB::table('customer_segment_members')->insert($inserts);
        }

        // Update count
        DB::table('customer_segments')
            ->where('id', $id)
            ->update([
                'customer_count' => count($customerIds),
                'last_calculated' => now()
            ]);

        return count($customerIds);
    }

    /**
     * Get customers in a segment
     */
    public function segmentMembers($id, Request $request)
    {
        $perPage = $request->input('per_page', 20);

        $members = DB::table('customer_segment_members')
            ->join('users', 'customer_segment_members.customer_id', '=', 'users.id')
            ->where('customer_segment_members.segment_id', $id)
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.created_at',
                'customer_segment_members.added_at'
            )
            ->orderBy('customer_segment_members.added_at', 'desc')
            ->paginate($perPage);

        return response()->json($members);
    }

    // ==========================================
    // CUSTOMER METRICS
    // ==========================================

    /**
     * Get customer metrics
     */
    public function customerMetrics($customerId)
    {
        $metrics = DB::table('customer_metrics')
            ->where('customer_id', $customerId)
            ->first();

        if (!$metrics) {
            // Calculate on-the-fly if not cached
            return response()->json(['data' => $this->calculateCustomerMetrics($customerId)]);
        }

        return response()->json(['data' => $metrics]);
    }

    /**
     * Calculate and cache customer metrics
     */
    public function calculateCustomerMetrics($customerId)
    {
        // Get order stats - using order_date column instead of created_at
        $orderStats = DB::table('orders')
            ->where('user_id', $customerId)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as lifetime_value,
                COALESCE(AVG(total_amount), 0) as avg_order_value,
                MIN(order_date) as first_order_date,
                MAX(order_date) as last_order_date
            ')
            ->first();

        $daysSinceLastOrder = null;
        $purchaseFrequency = null;

        if ($orderStats->last_order_date) {
            $daysSinceLastOrder = now()->diffInDays($orderStats->last_order_date);

            // Calculate purchase frequency (orders per month)
            $firstOrderDate = \Carbon\Carbon::parse($orderStats->first_order_date);
            $monthsActive = max(1, $firstOrderDate->diffInMonths(now()));
            $purchaseFrequency = round($orderStats->total_orders / $monthsActive, 2);
        }

        // Calculate RFM scores (1-5 scale)
        $rfmRecency = $this->calculateRfmScore('recency', $daysSinceLastOrder);
        $rfmFrequency = $this->calculateRfmScore('frequency', $orderStats->total_orders);
        $rfmMonetary = $this->calculateRfmScore('monetary', $orderStats->lifetime_value);
        $rfmSegment = $this->determineRfmSegment($rfmRecency, $rfmFrequency, $rfmMonetary);

        // Calculate health score (1-100)
        $healthScore = $this->calculateHealthScore($rfmRecency, $rfmFrequency, $rfmMonetary, $daysSinceLastOrder);

        // Churn risk (0.00 - 1.00)
        $churnRisk = $this->calculateChurnRisk($daysSinceLastOrder, $orderStats->total_orders, $purchaseFrequency);

        $metrics = [
            'customer_id' => $customerId,
            'lifetime_value' => $orderStats->lifetime_value,
            'total_orders' => $orderStats->total_orders,
            'avg_order_value' => round($orderStats->avg_order_value, 2),
            'first_order_date' => $orderStats->first_order_date ? date('Y-m-d', strtotime($orderStats->first_order_date)) : null,
            'last_order_date' => $orderStats->last_order_date ? date('Y-m-d', strtotime($orderStats->last_order_date)) : null,
            'days_since_last_order' => $daysSinceLastOrder,
            'purchase_frequency' => $purchaseFrequency,
            'rfm_recency_score' => $rfmRecency,
            'rfm_frequency_score' => $rfmFrequency,
            'rfm_monetary_score' => $rfmMonetary,
            'rfm_segment' => $rfmSegment,
            'churn_risk_score' => $churnRisk,
            'health_score' => $healthScore,
            'calculated_at' => now()
        ];

        // Upsert metrics
        DB::table('customer_metrics')->updateOrInsert(
            ['customer_id' => $customerId],
            $metrics
        );

        return $metrics;
    }

    /**
     * Calculate RFM score (1-5)
     */
    private function calculateRfmScore($type, $value)
    {
        if ($value === null) return 1;

        switch ($type) {
            case 'recency':
                // Lower days = higher score
                if ($value <= 30) return 5;
                if ($value <= 60) return 4;
                if ($value <= 90) return 3;
                if ($value <= 180) return 2;
                return 1;

            case 'frequency':
                if ($value >= 10) return 5;
                if ($value >= 6) return 4;
                if ($value >= 3) return 3;
                if ($value >= 2) return 2;
                return 1;

            case 'monetary':
                if ($value >= 1000) return 5;
                if ($value >= 500) return 4;
                if ($value >= 250) return 3;
                if ($value >= 100) return 2;
                return 1;
        }

        return 1;
    }

    /**
     * Determine RFM segment name
     */
    private function determineRfmSegment($r, $f, $m)
    {
        $avg = ($r + $f + $m) / 3;

        if ($r >= 4 && $f >= 4 && $m >= 4) return 'Champion';
        if ($r >= 4 && $f >= 3 && $m >= 3) return 'Loyal';
        if ($r >= 3 && $f >= 1 && $m >= 4) return 'Big Spender';
        if ($r >= 4 && $f <= 2 && $m <= 2) return 'New Customer';
        if ($r >= 3 && $avg >= 2.5) return 'Promising';
        if ($r <= 2 && $f >= 3 && $m >= 3) return 'At Risk';
        if ($r <= 2 && $f >= 2) return 'Needs Attention';
        if ($r <= 2 && $f <= 2 && $m <= 2) return 'Hibernating';
        if ($r <= 1) return 'Lost';

        return 'Regular';
    }

    /**
     * Calculate customer health score (1-100)
     */
    private function calculateHealthScore($r, $f, $m, $daysSinceLastOrder)
    {
        $baseScore = (($r + $f + $m) / 15) * 100;

        // Penalty for inactivity
        if ($daysSinceLastOrder !== null && $daysSinceLastOrder > 90) {
            $penalty = min(30, ($daysSinceLastOrder - 90) / 10);
            $baseScore -= $penalty;
        }

        return max(1, min(100, round($baseScore)));
    }

    /**
     * Calculate churn risk (0.00 - 1.00)
     */
    private function calculateChurnRisk($daysSinceLastOrder, $orderCount, $frequency)
    {
        if ($daysSinceLastOrder === null) return 0.5; // No orders = moderate risk

        $risk = 0;

        // Base risk from recency
        if ($daysSinceLastOrder > 180) $risk += 0.5;
        elseif ($daysSinceLastOrder > 90) $risk += 0.3;
        elseif ($daysSinceLastOrder > 60) $risk += 0.15;
        elseif ($daysSinceLastOrder > 30) $risk += 0.05;

        // Adjust for order history
        if ($orderCount == 1) $risk += 0.2; // One-time buyers at higher risk
        if ($orderCount >= 5) $risk -= 0.1; // Repeat customers at lower risk

        // Adjust for frequency decline (would need historical data)

        return max(0, min(1, round($risk, 2)));
    }

    /**
     * Recalculate all customer metrics
     */
    public function recalculateAllMetrics(Request $request)
    {
        $customerIds = DB::table('users')
            ->where('role', 'customer')
            ->pluck('id');

        $count = 0;
        foreach ($customerIds as $id) {
            $this->calculateCustomerMetrics($id);
            $count++;
        }

        return response()->json([
            'message' => 'Metrics recalculated',
            'customers_processed' => $count
        ]);
    }

    // ==========================================
    // EMAIL TEMPLATES
    // ==========================================

    /**
     * Get email templates
     */
    public function emailTemplates(Request $request)
    {
        $query = DB::table('email_templates');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('category')->orderBy('name')->get();

        return response()->json(['data' => $templates]);
    }

    /**
     * Get single email template
     */
    public function emailTemplate($id)
    {
        $template = DB::table('email_templates')->where('id', $id)->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        return response()->json(['data' => $template]);
    }

    /**
     * Create email template
     */
    public function createEmailTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:order,service,marketing,transactional,personal',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string'
        ]);

        $id = DB::table('email_templates')->insertGetId([
            'name' => $request->name,
            'category' => $request->category,
            'subject' => $request->subject,
            'body_html' => $request->body_html,
            'body_text' => $request->body_text,
            'variables' => $request->variables ? json_encode($request->variables) : null,
            'is_active' => true,
            'created_by' => $request->created_by,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Template created successfully',
            'id' => $id
        ], 201);
    }

    /**
     * Update email template
     */
    public function updateEmailTemplate(Request $request, $id)
    {
        $updateData = ['updated_at' => now()];
        if ($request->filled('name')) $updateData['name'] = $request->name;
        if ($request->filled('category')) $updateData['category'] = $request->category;
        if ($request->filled('subject')) $updateData['subject'] = $request->subject;
        if ($request->filled('body_html')) $updateData['body_html'] = $request->body_html;
        if ($request->has('body_text')) $updateData['body_text'] = $request->body_text;
        if ($request->has('is_active')) $updateData['is_active'] = $request->boolean('is_active');

        DB::table('email_templates')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Template updated successfully']);
    }

    /**
     * Delete email template
     */
    public function deleteEmailTemplate($id)
    {
        DB::table('email_templates')->where('id', $id)->delete();
        return response()->json(['message' => 'Template deleted successfully']);
    }

    // ==========================================
    // CUSTOMER 360 VIEW
    // ==========================================

    /**
     * Get complete customer 360 view data
     */
    public function customer360($customerId)
    {
        // Basic customer info
        $customer = DB::table('users')
            ->where('id', $customerId)
            ->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // Get metrics (calculate if needed)
        $metrics = DB::table('customer_metrics')
            ->where('customer_id', $customerId)
            ->first();

        if (!$metrics) {
            $metrics = $this->calculateCustomerMetrics($customerId);
        }

        // Get tags
        $tags = DB::table('customer_tag_assignments')
            ->join('customer_tags', 'customer_tag_assignments.tag_id', '=', 'customer_tags.id')
            ->where('customer_tag_assignments.customer_id', $customerId)
            ->select('customer_tags.*')
            ->get();

        // Get recent activities (last 10)
        $activities = DB::table('customer_activities')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pinned notes
        $pinnedNotes = DB::table('customer_notes')
            ->where('customer_id', $customerId)
            ->where('is_pinned', true)
            ->where('is_private', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get segments customer belongs to
        $segments = DB::table('customer_segment_members')
            ->join('customer_segments', 'customer_segment_members.segment_id', '=', 'customer_segments.id')
            ->where('customer_segment_members.customer_id', $customerId)
            ->select('customer_segments.id', 'customer_segments.name')
            ->get();

        // Get loyalty info
        $loyalty = DB::table('loyalty_members')
            ->leftJoin('loyalty_tiers', 'loyalty_members.tier_id', '=', 'loyalty_tiers.id')
            ->where('loyalty_members.user_id', $customerId)
            ->select('loyalty_members.*', 'loyalty_tiers.tier_name')
            ->first();

        // Recent orders (last 5)
        $recentOrders = DB::table('orders')
            ->where('user_id', $customerId)
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'customer' => $customer,
            'metrics' => $metrics,
            'tags' => $tags,
            'activities' => $activities,
            'pinned_notes' => $pinnedNotes,
            'segments' => $segments,
            'loyalty' => $loyalty,
            'recent_orders' => $recentOrders
        ]);
    }

    // ==========================================
    // PRESET SEGMENTS INITIALIZATION
    // ==========================================

    /**
     * Initialize preset segments
     */
    public function initPresetSegments()
    {
        $presets = [
            [
                'name' => 'New Customers',
                'description' => 'Customers who made their first order in the last 30 days',
                'rules' => [
                    ['field' => 'created_at', 'operator' => 'within_days', 'value' => 30]
                ]
            ],
            [
                'name' => 'VIP Customers',
                'description' => 'Customers with lifetime value over $1000 or Gold/Platinum tier',
                'rules' => [
                    ['field' => 'total_spent', 'operator' => '>=', 'value' => 1000]
                ]
            ],
            [
                'name' => 'At Risk',
                'description' => 'Previously active customers with no order in 90+ days',
                'rules' => [
                    ['field' => 'order_count', 'operator' => '>=', 'value' => 2],
                    ['field' => 'last_order_days', 'operator' => '>=', 'value' => 90]
                ]
            ],
            [
                'name' => 'Churned',
                'description' => 'No order in 180+ days',
                'rules' => [
                    ['field' => 'last_order_days', 'operator' => '>=', 'value' => 180]
                ]
            ],
            [
                'name' => 'One-Time Buyers',
                'description' => 'Exactly 1 order, placed 60+ days ago',
                'rules' => [
                    ['field' => 'order_count', 'operator' => '=', 'value' => 1],
                    ['field' => 'last_order_days', 'operator' => '>=', 'value' => 60]
                ]
            ],
            [
                'name' => 'Frequent Buyers',
                'description' => '5+ orders in the last 12 months',
                'rules' => [
                    ['field' => 'order_count', 'operator' => '>=', 'value' => 5]
                ]
            ],
            [
                'name' => 'High AOV',
                'description' => 'Average order value over $200',
                'rules' => [
                    ['field' => 'avg_order_value', 'operator' => '>=', 'value' => 200]
                ]
            ]
        ];

        $created = 0;
        foreach ($presets as $preset) {
            $exists = DB::table('customer_segments')
                ->where('name', $preset['name'])
                ->exists();

            if (!$exists) {
                $id = DB::table('customer_segments')->insertGetId([
                    'name' => $preset['name'],
                    'description' => $preset['description'],
                    'rules' => json_encode($preset['rules']),
                    'is_dynamic' => true,
                    'is_preset' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->recalculateSegment($id);
                $created++;
            }
        }

        return response()->json([
            'message' => 'Preset segments initialized',
            'created' => $created
        ]);
    }
}
