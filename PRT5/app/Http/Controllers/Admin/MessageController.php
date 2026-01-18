<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Traits\HasGridFeatures;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use HasGridFeatures;

    public function index(Request $request)
    {
        $query = ContactMessage::with('user');

        // Apply search
        $this->applySearch($query, $request, ['name', 'email', 'subject', 'message']);

        // Apply status filter
        $this->applyStatusFilter($query, $request);

        // Apply date filter
        $this->applyDateFilter($query, $request);

        // Apply sorting
        $this->applySorting($query, $request, 'created_at', 'desc');

        // Get paginated results
        $messages = $this->getPaginated($query, $request);

        // Get stats
        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::unread()->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'archived' => ContactMessage::where('status', 'archived')->count(),
        ];

        $filters = $this->getFilterOptions($request);

        // Handle selected message (prt4 style - master-detail view)
        $selectedMessage = null;
        if ($request->filled('id')) {
            $selectedMessage = ContactMessage::find($request->get('id'));

            // Auto-mark as read when viewing
            if ($selectedMessage && $selectedMessage->status === 'unread') {
                $selectedMessage->update([
                    'status' => 'read',
                    'read_at' => now(),
                    'read_by' => auth()->id(),
                ]);
                $stats['unread']--;
                $stats['read']++;
            }
        }

        return view('admin.messages.index', compact('messages', 'stats', 'filters', 'selectedMessage'));
    }

    public function show(ContactMessage $message)
    {
        // Redirect to index with ?id= parameter (prt4 style)
        return redirect()->route('admin.messages.index', ['id' => $message->id]);
    }

    public function update(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'status' => 'required|in:unread,read,replied,archived',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $message->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message status updated.',
                'stats' => $this->getStats(),
            ]);
        }

        return back()->with('success', 'Message updated.');
    }

    public function destroy(Request $request, ContactMessage $message)
    {
        $message->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message deleted.',
                'stats' => $this->getStats(),
            ]);
        }

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted.');
    }

    public function bulkAction(Request $request)
    {
        $result = $this->handleBulkAction(
            $request,
            ContactMessage::class,
            ['read', 'archive', 'delete']
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'count' => $result['count'],
                'stats' => $this->getStats(),
            ]);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function getStats(): array
    {
        return [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::unread()->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'archived' => ContactMessage::where('status', 'archived')->count(),
        ];
    }
}
