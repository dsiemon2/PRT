<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatAgent;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ChatDepartment;
use App\Models\ChatCannedResponse;
use App\Models\ChatTrigger;
use App\Models\ChatOfflineMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // ==================
    // AGENTS
    // ==================

    public function agents(): JsonResponse
    {
        $agents = ChatAgent::with(['user', 'departments'])
            ->orderBy('display_name')
            ->get();

        return response()->json(['data' => $agents]);
    }

    public function agent($id): JsonResponse
    {
        $agent = ChatAgent::with(['user', 'departments', 'activeSessions'])->findOrFail($id);
        return response()->json(['data' => $agent]);
    }

    public function storeAgent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:chat_agents,user_id',
            'display_name' => 'required|string|max:255',
            'avatar_url' => 'nullable|string|max:500',
            'max_concurrent_chats' => 'integer|min:1|max:20',
            'skills' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $agent = ChatAgent::create($validated);

        return response()->json(['data' => $agent], 201);
    }

    public function updateAgent(Request $request, $id): JsonResponse
    {
        $agent = ChatAgent::findOrFail($id);

        $validated = $request->validate([
            'display_name' => 'string|max:255',
            'avatar_url' => 'nullable|string|max:500',
            'status' => 'string|in:online,offline,away,busy',
            'max_concurrent_chats' => 'integer|min:1|max:20',
            'skills' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $agent->update($validated);

        return response()->json(['data' => $agent]);
    }

    public function deleteAgent($id): JsonResponse
    {
        $agent = ChatAgent::findOrFail($id);
        $agent->delete();

        return response()->json(['message' => 'Agent deleted']);
    }

    public function agentStatuses(): JsonResponse
    {
        return response()->json(['data' => ChatAgent::getStatuses()]);
    }

    // ==================
    // SESSIONS
    // ==================

    public function sessions(Request $request): JsonResponse
    {
        $query = ChatSession::with(['customer', 'agent']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($sessions);
    }

    public function session($id): JsonResponse
    {
        $session = ChatSession::with(['customer', 'agent', 'messages.agent'])->findOrFail($id);
        return response()->json(['data' => $session]);
    }

    public function assignSession(Request $request, $id): JsonResponse
    {
        $session = ChatSession::findOrFail($id);

        $validated = $request->validate([
            'agent_id' => 'required|exists:chat_agents,id',
        ]);

        $agent = ChatAgent::findOrFail($validated['agent_id']);

        if (!$agent->isAvailable()) {
            return response()->json(['error' => 'Agent is not available'], 400);
        }

        $session->assignAgent($agent);

        return response()->json(['data' => $session->fresh(['agent'])]);
    }

    public function closeSession(Request $request, $id): JsonResponse
    {
        $session = ChatSession::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'nullable|numeric|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $session->close($validated['rating'] ?? null, $validated['feedback'] ?? null);

        return response()->json(['data' => $session->fresh()]);
    }

    public function sessionStatuses(): JsonResponse
    {
        return response()->json(['data' => ChatSession::getStatuses()]);
    }

    // ==================
    // MESSAGES
    // ==================

    public function sessionMessages($sessionId): JsonResponse
    {
        $messages = ChatMessage::where('session_id', $sessionId)
            ->with('agent')
            ->orderBy('created_at')
            ->get();

        return response()->json(['data' => $messages]);
    }

    public function sendMessage(Request $request, $sessionId): JsonResponse
    {
        $session = ChatSession::findOrFail($sessionId);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'message_type' => 'string|in:text,image,file,link,card',
            'attachments' => 'nullable|array',
        ]);

        $validated['session_id'] = $session->id;
        $validated['sender_type'] = 'agent';
        $validated['agent_id'] = auth()->id(); // Or get agent from user

        $message = ChatMessage::create($validated);

        return response()->json(['data' => $message], 201);
    }

    // ==================
    // DEPARTMENTS
    // ==================

    public function departments(): JsonResponse
    {
        $departments = ChatDepartment::with('agents')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $departments]);
    }

    public function department($id): JsonResponse
    {
        $department = ChatDepartment::with('agents')->findOrFail($id);
        return response()->json(['data' => $department]);
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:chat_departments,code',
            'description' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'working_hours' => 'nullable|array',
            'sort_order' => 'integer',
        ]);

        $department = ChatDepartment::create($validated);

        return response()->json(['data' => $department], 201);
    }

    public function updateDepartment(Request $request, $id): JsonResponse
    {
        $department = ChatDepartment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:chat_departments,code,' . $id,
            'description' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'working_hours' => 'nullable|array',
            'sort_order' => 'integer',
        ]);

        $department->update($validated);

        return response()->json(['data' => $department]);
    }

    public function deleteDepartment($id): JsonResponse
    {
        $department = ChatDepartment::findOrFail($id);
        $department->delete();

        return response()->json(['message' => 'Department deleted']);
    }

    // ==================
    // CANNED RESPONSES
    // ==================

    public function cannedResponses(): JsonResponse
    {
        $responses = ChatCannedResponse::orderBy('category')
            ->orderBy('title')
            ->get();

        return response()->json(['data' => $responses]);
    }

    public function storeCannedResponse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'shortcut' => 'required|string|max:50|unique:chat_canned_responses,shortcut',
            'content' => 'required|string|max:5000',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $response = ChatCannedResponse::create($validated);

        return response()->json(['data' => $response], 201);
    }

    public function updateCannedResponse(Request $request, $id): JsonResponse
    {
        $response = ChatCannedResponse::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'shortcut' => 'string|max:50|unique:chat_canned_responses,shortcut,' . $id,
            'content' => 'string|max:5000',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $response->update($validated);

        return response()->json(['data' => $response]);
    }

    public function deleteCannedResponse($id): JsonResponse
    {
        $response = ChatCannedResponse::findOrFail($id);
        $response->delete();

        return response()->json(['message' => 'Canned response deleted']);
    }

    // ==================
    // TRIGGERS
    // ==================

    public function triggers(): JsonResponse
    {
        $triggers = ChatTrigger::orderBy('name')->get();
        return response()->json(['data' => $triggers]);
    }

    public function storeTrigger(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string|in:page_time,scroll_depth,exit_intent,page_url,cart_value,returning_visitor',
            'conditions' => 'required|array',
            'message' => 'required|string|max:1000',
            'department_code' => 'nullable|string|max:50',
            'delay_seconds' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $trigger = ChatTrigger::create($validated);

        return response()->json(['data' => $trigger], 201);
    }

    public function updateTrigger(Request $request, $id): JsonResponse
    {
        $trigger = ChatTrigger::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'trigger_type' => 'string|in:page_time,scroll_depth,exit_intent,page_url,cart_value,returning_visitor',
            'conditions' => 'array',
            'message' => 'string|max:1000',
            'department_code' => 'nullable|string|max:50',
            'delay_seconds' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $trigger->update($validated);

        return response()->json(['data' => $trigger]);
    }

    public function deleteTrigger($id): JsonResponse
    {
        $trigger = ChatTrigger::findOrFail($id);
        $trigger->delete();

        return response()->json(['message' => 'Trigger deleted']);
    }

    public function triggerTypes(): JsonResponse
    {
        return response()->json(['data' => ChatTrigger::getTriggerTypes()]);
    }

    // ==================
    // OFFLINE MESSAGES
    // ==================

    public function offlineMessages(Request $request): JsonResponse
    {
        $query = ChatOfflineMessage::with('assignedAgent');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($messages);
    }

    public function replyOfflineMessage(Request $request, $id): JsonResponse
    {
        $message = ChatOfflineMessage::findOrFail($id);

        $validated = $request->validate([
            'reply' => 'required|string|max:5000',
        ]);

        $message->reply($validated['reply'], auth()->id());

        return response()->json(['data' => $message->fresh()]);
    }

    public function updateOfflineMessageStatus(Request $request, $id): JsonResponse
    {
        $message = ChatOfflineMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:new,read,replied,closed',
        ]);

        $message->update($validated);

        return response()->json(['data' => $message]);
    }

    // ==================
    // STATS
    // ==================

    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();

        return response()->json([
            'data' => [
                'agents' => [
                    'total' => ChatAgent::count(),
                    'online' => ChatAgent::where('status', 'online')->count(),
                    'active' => ChatAgent::where('is_active', true)->count(),
                ],
                'sessions' => [
                    'waiting' => ChatSession::where('status', 'waiting')->count(),
                    'active' => ChatSession::where('status', 'active')->count(),
                    'today' => ChatSession::where('created_at', '>=', $today)->count(),
                    'avg_wait_time' => round(ChatSession::where('created_at', '>=', $today)
                        ->where('status', '!=', 'waiting')
                        ->avg('wait_time_seconds') ?? 0),
                ],
                'offline_messages' => [
                    'new' => ChatOfflineMessage::where('status', 'new')->count(),
                    'total_today' => ChatOfflineMessage::where('created_at', '>=', $today)->count(),
                ],
                'departments' => ChatDepartment::where('is_active', true)->count(),
                'canned_responses' => ChatCannedResponse::where('is_active', true)->count(),
            ]
        ]);
    }
}
