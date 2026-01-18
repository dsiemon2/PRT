<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Traits\HasGridFeatures;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use HasGridFeatures;

    public function index(Request $request)
    {
        $query = Event::query();

        // Apply search
        $this->applySearch($query, $request, ['EventName', 'EnteredBy']);

        // Apply status filter
        $status = $request->get('status', 'all');
        if ($status === 'upcoming') {
            $query->where('StartDate', '>=', now());
        } elseif ($status === 'past') {
            $query->where('EndDate', '<', now());
        }

        // Apply sorting - default to StartDate DESC like prt4
        $this->applySorting($query, $request, 'StartDate', 'desc');

        // Get paginated results (12 per page like prt4)
        $events = $query->paginate(12)->withQueryString();

        // Get stats
        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::where('StartDate', '>=', now())->count(),
            'past' => Event::where('EndDate', '<', now())->count(),
            'active' => Event::where('StartDate', '<=', now())->where('EndDate', '>=', now())->count(),
        ];

        $filters = $this->getFilterOptions($request);

        // Handle edit mode (prt4 style inline form)
        $editEvent = null;
        if ($request->filled('edit')) {
            $editEvent = Event::find($request->get('edit'));
        }

        return view('admin.events.index', compact('events', 'stats', 'filters', 'editEvent'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EventName' => 'required|string|max:255',
            'StartDate' => 'required|date',
            'EndDate' => 'nullable|date|after_or_equal:StartDate',
            'StartTime' => 'nullable|string|max:50',
            'EndTime' => 'nullable|string|max:50',
        ]);

        // Set EnteredBy to current user's name like prt4
        $validated['EnteredBy'] = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event added successfully!');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'EventName' => 'required|string|max:255',
            'StartDate' => 'required|date',
            'EndDate' => 'nullable|date|after_or_equal:StartDate',
            'StartTime' => 'nullable|string|max:50',
            'EndTime' => 'nullable|string|max:50',
        ]);

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
