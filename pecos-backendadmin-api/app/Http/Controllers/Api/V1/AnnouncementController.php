<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Get active announcements for frontend (public).
     */
    public function index()
    {
        $settings = DB::table('announcement_settings')->first();

        if (!$settings || !$settings->enabled) {
            return response()->json([
                'enabled' => false,
                'data' => [],
            ]);
        }

        $now = now();
        $announcements = DB::table('announcements')
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'enabled' => true,
            'settings' => [
                'allow_dismiss' => $settings->allow_dismiss,
                'rotation_speed' => $settings->rotation_speed,
                'animation' => $settings->animation,
            ],
            'data' => $announcements,
        ]);
    }

    /**
     * Get all announcements for admin.
     */
    public function adminIndex()
    {
        $announcements = DB::table('announcements')
            ->orderBy('display_order')
            ->orderBy('id', 'desc')
            ->get();

        $settings = DB::table('announcement_settings')->first();

        return response()->json([
            'data' => $announcements,
            'settings' => $settings,
        ]);
    }

    /**
     * Get available icons list.
     */
    public function icons()
    {
        return response()->json([
            'data' => [
                ['value' => 'bi-telephone', 'label' => 'Phone'],
                ['value' => 'bi-truck', 'label' => 'Shipping/Truck'],
                ['value' => 'bi-gift', 'label' => 'Gift'],
                ['value' => 'bi-percent', 'label' => 'Percent/Discount'],
                ['value' => 'bi-tag', 'label' => 'Tag/Sale'],
                ['value' => 'bi-star', 'label' => 'Star'],
                ['value' => 'bi-heart', 'label' => 'Heart'],
                ['value' => 'bi-clock', 'label' => 'Clock/Time'],
                ['value' => 'bi-calendar-event', 'label' => 'Calendar/Event'],
                ['value' => 'bi-megaphone', 'label' => 'Megaphone'],
                ['value' => 'bi-bell', 'label' => 'Bell/Notification'],
                ['value' => 'bi-envelope', 'label' => 'Email'],
                ['value' => 'bi-lightning', 'label' => 'Lightning/Flash'],
                ['value' => 'bi-fire', 'label' => 'Fire/Hot'],
                ['value' => 'bi-award', 'label' => 'Award'],
                ['value' => 'bi-trophy', 'label' => 'Trophy'],
                ['value' => 'bi-credit-card', 'label' => 'Credit Card'],
                ['value' => 'bi-shield-check', 'label' => 'Shield/Secure'],
                ['value' => 'bi-box-seam', 'label' => 'Package/Box'],
                ['value' => 'bi-geo-alt', 'label' => 'Location'],
            ],
        ]);
    }

    /**
     * Create a new announcement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'position' => 'in:left,center,right',
            'bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $id = DB::table('announcements')->insertGetId([
            'text' => $request->text,
            'icon' => $request->icon,
            'link_url' => $request->link_url,
            'link_text' => $request->link_text,
            'position' => $request->position ?? 'center',
            'bg_color' => $request->bg_color ?? '#C41E3A',
            'text_color' => $request->text_color ?? '#FFFFFF',
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Announcement created successfully.',
            'data' => ['id' => $id],
        ], 201);
    }

    /**
     * Update an announcement.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'text' => 'sometimes|required|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'position' => 'in:left,center,right',
            'bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $updateData = array_filter([
            'text' => $request->text,
            'icon' => $request->icon,
            'link_url' => $request->link_url,
            'link_text' => $request->link_text,
            'position' => $request->position,
            'bg_color' => $request->bg_color,
            'text_color' => $request->text_color,
            'display_order' => $request->display_order,
            'is_active' => $request->has('is_active') ? $request->is_active : null,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        DB::table('announcements')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Announcement updated successfully.']);
    }

    /**
     * Delete an announcement.
     */
    public function destroy($id)
    {
        DB::table('announcements')->where('id', $id)->delete();

        return response()->json(['message' => 'Announcement deleted successfully.']);
    }

    /**
     * Update announcement settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|boolean',
            'allow_dismiss' => 'nullable|boolean',
            'rotation_speed' => 'nullable|integer|min:1|max:30',
            'animation' => 'nullable|in:fade,slide,none',
        ]);

        DB::table('announcement_settings')->where('id', 1)->update([
            'enabled' => $request->enabled ?? false,
            'allow_dismiss' => $request->allow_dismiss ?? true,
            'rotation_speed' => $request->rotation_speed ?? 5,
            'animation' => $request->animation ?? 'fade',
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Settings updated successfully.']);
    }

    /**
     * Reorder announcements.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer',
            'order.*.display_order' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            DB::table('announcements')
                ->where('id', $item['id'])
                ->update(['display_order' => $item['display_order']]);
        }

        return response()->json(['message' => 'Order updated successfully.']);
    }
}
