<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    /**
     * Get all support tickets with filters.
     */
    public function tickets(Request $request)
    {
        $query = DB::table('support_tickets as t')
            ->leftJoin('customers as c', 't.customer_id', '=', 'c.id')
            ->leftJoin('orders as o', 't.order_id', '=', 'o.id')
            ->select(
                't.*',
                'c.NameFirst as customer_first_name',
                'c.NameLast as customer_last_name',
                'c.Email as customer_email',
                'o.order_number'
            );

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('t.status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('t.priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('t.category', $request->category);
        }

        // Filter by assigned_to
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('t.assigned_to', $request->assigned_to);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('t.ticket_number', 'like', "%{$search}%")
                    ->orWhere('t.subject', 'like', "%{$search}%")
                    ->orWhere('c.NameFirst', 'like', "%{$search}%")
                    ->orWhere('c.NameLast', 'like', "%{$search}%")
                    ->orWhere('c.Email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $tickets = $query->orderBy('t.created_at', 'desc')->paginate($perPage);

        // Get message counts for each ticket
        $ticketIds = collect($tickets->items())->pluck('id');
        $messageCounts = DB::table('ticket_messages')
            ->whereIn('ticket_id', $ticketIds)
            ->groupBy('ticket_id')
            ->select('ticket_id', DB::raw('COUNT(*) as count'))
            ->pluck('count', 'ticket_id');

        $items = collect($tickets->items())->map(function ($ticket) use ($messageCounts) {
            $ticket->message_count = $messageCounts[$ticket->id] ?? 0;
            return $ticket;
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }

    /**
     * Get ticket statistics.
     */
    public function ticketStats()
    {
        $stats = [
            'total' => DB::table('support_tickets')->count(),
            'open' => DB::table('support_tickets')->where('status', 'open')->count(),
            'in_progress' => DB::table('support_tickets')->where('status', 'in_progress')->count(),
            'pending_customer' => DB::table('support_tickets')->where('status', 'pending_customer')->count(),
            'resolved' => DB::table('support_tickets')->where('status', 'resolved')->count(),
            'closed' => DB::table('support_tickets')->where('status', 'closed')->count(),
            'urgent' => DB::table('support_tickets')->where('priority', 'urgent')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'high' => DB::table('support_tickets')->where('priority', 'high')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'unassigned' => DB::table('support_tickets')->whereNull('assigned_to')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'avg_resolution_hours' => $this->calculateAvgResolutionTime(),
            'avg_response_hours' => $this->calculateAvgResponseTime(),
            'satisfaction_avg' => DB::table('support_tickets')->whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
            'by_category' => DB::table('support_tickets')
                ->select('category', DB::raw('COUNT(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category'),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Get single ticket with messages.
     */
    public function show($id)
    {
        $ticket = DB::table('support_tickets as t')
            ->leftJoin('users as u', 't.customer_id', '=', 'u.id')
            ->leftJoin('orders as o', 't.order_id', '=', 'o.id')
            ->where('t.id', $id)
            ->select(
                't.*',
                'u.first_name as customer_first_name',
                'u.last_name as customer_last_name',
                'u.email as customer_email',
                'u.phone as customer_phone',
                'o.order_number',
                'o.total_amount as order_total'
            )
            ->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        // Get messages
        $messages = DB::table('ticket_messages')
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get customer's other tickets
        $otherTickets = DB::table('support_tickets')
            ->where('customer_id', $ticket->customer_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'ticket' => $ticket,
                'messages' => $messages,
                'other_tickets' => $otherTickets,
            ],
        ]);
    }

    /**
     * Create a new ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'subject' => 'required|string|max:255',
            'category' => 'required|in:order,return,product,shipping,billing,other',
            'priority' => 'in:low,medium,high,urgent',
            'message' => 'required|string',
            'order_id' => 'nullable|integer',
        ]);

        $ticketNumber = 'TKT-' . strtoupper(uniqid());

        $ticketId = DB::table('support_tickets')->insertGetId([
            'ticket_number' => $ticketNumber,
            'customer_id' => $request->customer_id,
            'order_id' => $request->order_id,
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
            'assigned_to' => $request->assigned_to,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add initial message
        DB::table('ticket_messages')->insert([
            'ticket_id' => $ticketId,
            'sender_type' => $request->sender_type ?? 'customer',
            'sender_id' => $request->sender_id ?? $request->customer_id,
            'message' => $request->message,
            'is_internal' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log activity
        DB::table('customer_activities')->insert([
            'customer_id' => $request->customer_id,
            'activity_type' => 'support',
            'title' => 'Opened support ticket',
            'description' => "Ticket #{$ticketNumber}: {$request->subject}",
            'metadata' => json_encode(['ticket_id' => $ticketId, 'category' => $request->category]),
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => ['id' => $ticketId, 'ticket_number' => $ticketNumber],
        ], 201);
    }

    /**
     * Update ticket.
     */
    public function update(Request $request, $id)
    {
        $ticket = DB::table('support_tickets')->where('id', $id)->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        $updateData = [];

        if ($request->has('status')) {
            $updateData['status'] = $request->status;

            if ($request->status === 'resolved' && !$ticket->resolved_at) {
                $updateData['resolved_at'] = now();
            }
        }

        if ($request->has('priority')) {
            $updateData['priority'] = $request->priority;
        }

        if ($request->has('category')) {
            $updateData['category'] = $request->category;
        }

        if ($request->has('assigned_to')) {
            $updateData['assigned_to'] = $request->assigned_to;
        }

        if (!empty($updateData)) {
            $updateData['updated_at'] = now();
            DB::table('support_tickets')->where('id', $id)->update($updateData);
        }

        return response()->json(['message' => 'Ticket updated successfully']);
    }

    /**
     * Add message to ticket.
     */
    public function addMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'sender_type' => 'required|in:customer,staff',
            'sender_id' => 'required|integer',
        ]);

        $ticket = DB::table('support_tickets')->where('id', $id)->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        $messageId = DB::table('ticket_messages')->insertGetId([
            'ticket_id' => $id,
            'sender_type' => $request->sender_type,
            'sender_id' => $request->sender_id,
            'message' => $request->message,
            'is_internal' => $request->is_internal ?? false,
            'attachments' => $request->attachments ? json_encode($request->attachments) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update first response time if staff responding for first time
        if ($request->sender_type === 'staff' && !$ticket->first_response_at) {
            DB::table('support_tickets')->where('id', $id)->update([
                'first_response_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update status if staff is responding and ticket is open
        if ($request->sender_type === 'staff' && $ticket->status === 'open') {
            DB::table('support_tickets')->where('id', $id)->update([
                'status' => 'in_progress',
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Message added successfully',
            'data' => ['id' => $messageId],
        ], 201);
    }

    /**
     * Add satisfaction rating.
     */
    public function addRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        DB::table('support_tickets')->where('id', $id)->update([
            'satisfaction_rating' => $request->rating,
            'satisfaction_comment' => $request->comment,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Rating submitted successfully']);
    }

    /**
     * Get canned responses.
     */
    public function cannedResponses(Request $request)
    {
        $query = DB::table('canned_responses')->where('is_active', true);

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $responses = $query->orderBy('title')->get();

        return response()->json(['data' => $responses]);
    }

    /**
     * Store canned response.
     */
    public function storeCannedResponse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'shortcut' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
        ]);

        $id = DB::table('canned_responses')->insertGetId([
            'title' => $request->title,
            'content' => $request->content,
            'shortcut' => $request->shortcut,
            'category' => $request->category,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Canned response created',
            'data' => ['id' => $id],
        ], 201);
    }

    /**
     * Update canned response.
     */
    public function updateCannedResponse(Request $request, $id)
    {
        $updateData = array_filter([
            'title' => $request->title,
            'content' => $request->content,
            'shortcut' => $request->shortcut,
            'category' => $request->category,
            'is_active' => $request->is_active,
        ], fn($v) => $v !== null);

        if (!empty($updateData)) {
            $updateData['updated_at'] = now();
            DB::table('canned_responses')->where('id', $id)->update($updateData);
        }

        return response()->json(['message' => 'Canned response updated']);
    }

    /**
     * Delete canned response.
     */
    public function deleteCannedResponse($id)
    {
        DB::table('canned_responses')->where('id', $id)->delete();

        return response()->json(['message' => 'Canned response deleted']);
    }

    /**
     * Calculate average resolution time in hours.
     */
    private function calculateAvgResolutionTime()
    {
        $result = DB::table('support_tickets')
            ->whereNotNull('resolved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours'))
            ->first();

        return round($result->avg_hours ?? 0, 1);
    }

    /**
     * Calculate average first response time in hours.
     */
    private function calculateAvgResponseTime()
    {
        $result = DB::table('support_tickets')
            ->whereNotNull('first_response_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours'))
            ->first();

        return round($result->avg_hours ?? 0, 1);
    }

    // =============================================
    // CUSTOMER-FACING METHODS
    // =============================================

    /**
     * Get tickets for a specific customer.
     */
    public function customerTickets(Request $request)
    {
        $customerId = $request->query('customer_id');

        if (!$customerId) {
            return response()->json(['error' => 'customer_id is required'], 400);
        }

        $tickets = DB::table('support_tickets')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $tickets]);
    }

    /**
     * Get single ticket detail for customer (with messages).
     */
    public function customerTicketDetail(Request $request, $id)
    {
        $customerId = $request->query('customer_id');

        if (!$customerId) {
            return response()->json(['error' => 'customer_id is required'], 400);
        }

        $ticket = DB::table('support_tickets')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        // Get messages
        $messages = DB::table('ticket_messages')
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'data' => [
                'ticket' => $ticket,
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Customer creates a new ticket.
     */
    public function customerCreateTicket(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'subject' => 'required|string|max:255',
            'category' => 'required|in:order,return,product,shipping,billing,other',
            'message' => 'required|string',
            'order_id' => 'nullable|integer',
        ]);

        $ticketNumber = 'TKT-' . strtoupper(uniqid());

        $ticketId = DB::table('support_tickets')->insertGetId([
            'ticket_number' => $ticketNumber,
            'customer_id' => $request->customer_id,
            'order_id' => $request->order_id,
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => 'medium',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add initial message
        DB::table('ticket_messages')->insert([
            'ticket_id' => $ticketId,
            'sender_type' => 'customer',
            'sender_id' => $request->customer_id,
            'message' => $request->message,
            'is_internal' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log activity
        DB::table('customer_activities')->insert([
            'customer_id' => $request->customer_id,
            'activity_type' => 'support',
            'title' => 'Opened support ticket',
            'description' => "Ticket #{$ticketNumber}: {$request->subject}",
            'metadata' => json_encode(['ticket_id' => $ticketId, 'category' => $request->category]),
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => ['id' => $ticketId, 'ticket_number' => $ticketNumber],
        ], 201);
    }

    /**
     * Customer replies to their ticket.
     */
    public function customerReply(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        // Verify ticket belongs to customer
        $ticket = DB::table('support_tickets')
            ->where('id', $id)
            ->where('customer_id', $request->customer_id)
            ->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        // Add message
        $messageId = DB::table('ticket_messages')->insertGetId([
            'ticket_id' => $id,
            'sender_type' => 'customer',
            'sender_id' => $request->customer_id,
            'message' => $request->message,
            'is_internal' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update ticket status if it was waiting for customer
        if ($ticket->status === 'pending_customer') {
            DB::table('support_tickets')->where('id', $id)->update([
                'status' => 'open',
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Reply added successfully',
            'data' => ['id' => $messageId],
        ], 201);
    }
}
