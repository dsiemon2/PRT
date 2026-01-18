<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Get all events.
     *
     * @OA\Get(
     *     path="/events",
     *     summary="Get all events",
     *     tags={"Events"},
     *     @OA\Parameter(name="upcoming", in="query", @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::query();

        if ($request->get('upcoming', true)) {
            $query->upcoming();
        } else {
            $query->orderBy('StartDate', 'desc');
        }

        $events = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Get upcoming events.
     *
     * @OA\Get(
     *     path="/events/upcoming",
     *     summary="Get upcoming events",
     *     tags={"Events"},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function upcoming(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);

        $events = Event::upcoming()->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Get a single event.
     *
     * @OA\Get(
     *     path="/events/{id}",
     *     summary="Get event by ID",
     *     tags={"Events"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    // Admin methods

    /**
     * Admin: Create event.
     *
     * @OA\Post(
     *     path="/admin/events",
     *     summary="Create event (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"EventName", "StartDate", "EndDate"},
     *             @OA\Property(property="EventName", type="string"),
     *             @OA\Property(property="StartDate", type="string", format="date"),
     *             @OA\Property(property="EndDate", type="string", format="date"),
     *             @OA\Property(property="StartTime", type="string"),
     *             @OA\Property(property="EndTime", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Event created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'EventName' => 'required|string|max:255',
            'StartDate' => 'required|date',
            'EndDate' => 'required|date|after_or_equal:StartDate',
            'StartTime' => 'nullable|string',
            'EndTime' => 'nullable|string',
            'EnteredBy' => 'nullable|string',
        ]);

        $validated['EnteredBy'] = $request->input('EnteredBy', 'Admin');

        $event = Event::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * Admin: Update event.
     *
     * @OA\Put(
     *     path="/admin/events/{id}",
     *     summary="Update event (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Event updated"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }

        $validated = $request->validate([
            'EventName' => 'nullable|string|max:255',
            'StartDate' => 'nullable|date',
            'EndDate' => 'nullable|date',
            'StartTime' => 'nullable|string',
            'EndTime' => 'nullable|string',
            'EnteredBy' => 'nullable|string',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event->fresh()
        ]);
    }

    /**
     * Admin: Delete event.
     *
     * @OA\Delete(
     *     path="/admin/events/{id}",
     *     summary="Delete event (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Event deleted"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Admin: Get all events (including past).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Event::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('EventName', 'like', "%{$search}%")
                  ->orWhere('EnteredBy', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'upcoming') {
                $query->where('StartDate', '>=', now());
            } elseif ($request->status === 'past') {
                $query->where('EndDate', '<', now());
            }
        }

        $events = $query->orderBy('StartDate', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'total' => $events->total(),
            ]
        ]);
    }
}
