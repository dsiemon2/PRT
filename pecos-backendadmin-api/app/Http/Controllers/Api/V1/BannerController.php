<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Get active banners for frontend (public).
     */
    public function index()
    {
        $settings = DB::table('banner_settings')->first();
        $now = now();

        $banners = DB::table('homepage_banners')
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
            'settings' => [
                'carousel_enabled' => $settings->carousel_enabled ?? true,
                'slide_duration' => $settings->slide_duration ?? 5,
                'show_indicators' => $settings->show_indicators ?? true,
                'show_controls' => $settings->show_controls ?? true,
                'transition' => $settings->transition ?? 'slide',
                'banner_height' => $settings->banner_height ?? 400,
                'mobile_banner_height' => $settings->mobile_banner_height ?? 250,
            ],
            'data' => $banners,
        ]);
    }

    /**
     * Get all banners for admin.
     */
    public function adminIndex()
    {
        $banners = DB::table('homepage_banners')
            ->orderBy('display_order')
            ->orderBy('id', 'desc')
            ->get();

        $settings = DB::table('banner_settings')->first();

        return response()->json([
            'data' => $banners,
            'settings' => $settings,
        ]);
    }

    /**
     * Get a single banner.
     */
    public function show($id)
    {
        $banner = DB::table('homepage_banners')->where('id', $id)->first();

        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }

        return response()->json(['data' => $banner]);
    }

    /**
     * Create a new banner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'subtitle' => 'nullable|string|max:500',
            'desktop_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'alt_text' => 'nullable|string|max:200',
            'position' => 'in:full,left,center,right',
            'text_position' => 'in:left,center,right',
            'overlay_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:7',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Handle desktop image upload
        $desktopPath = $this->uploadImage($request->file('desktop_image'), 'banners');

        // Handle mobile image upload (optional)
        $mobilePath = null;
        if ($request->hasFile('mobile_image')) {
            $mobilePath = $this->uploadImage($request->file('mobile_image'), 'banners/mobile');
        }

        $id = DB::table('homepage_banners')->insertGetId([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'desktop_image' => $desktopPath,
            'mobile_image' => $mobilePath,
            'link_url' => $request->link_url,
            'link_text' => $request->link_text ?? 'Shop Now',
            'alt_text' => $request->alt_text ?? $request->title,
            'position' => $request->position ?? 'full',
            'text_position' => $request->text_position ?? 'center',
            'overlay_color' => $request->overlay_color ?? 'rgba(0,0,0,0.3)',
            'text_color' => $request->text_color ?? '#FFFFFF',
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->is_active ?? true,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Banner created successfully.',
            'data' => ['id' => $id, 'desktop_image' => $desktopPath, 'mobile_image' => $mobilePath],
        ], 201);
    }

    /**
     * Update a banner.
     */
    public function update(Request $request, $id)
    {
        $banner = DB::table('homepage_banners')->where('id', $id)->first();

        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:200',
            'subtitle' => 'nullable|string|max:500',
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'alt_text' => 'nullable|string|max:200',
            'position' => 'in:full,left,center,right',
            'text_position' => 'in:left,center,right',
            'overlay_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:7',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $updateData = [
            'title' => $request->title ?? $banner->title,
            'subtitle' => $request->subtitle,
            'link_url' => $request->link_url,
            'link_text' => $request->link_text,
            'alt_text' => $request->alt_text,
            'position' => $request->position ?? $banner->position,
            'text_position' => $request->text_position ?? $banner->text_position,
            'overlay_color' => $request->overlay_color ?? $banner->overlay_color,
            'text_color' => $request->text_color ?? $banner->text_color,
            'display_order' => $request->display_order ?? $banner->display_order,
            'is_active' => $request->has('is_active') ? $request->is_active : $banner->is_active,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'updated_at' => now(),
        ];

        // Handle desktop image update
        if ($request->hasFile('desktop_image')) {
            $updateData['desktop_image'] = $this->uploadImage($request->file('desktop_image'), 'banners');
        }

        // Handle mobile image update
        if ($request->hasFile('mobile_image')) {
            $updateData['mobile_image'] = $this->uploadImage($request->file('mobile_image'), 'banners/mobile');
        }

        DB::table('homepage_banners')->where('id', $id)->update($updateData);

        return response()->json(['message' => 'Banner updated successfully.']);
    }

    /**
     * Delete a banner.
     */
    public function destroy($id)
    {
        DB::table('homepage_banners')->where('id', $id)->delete();

        return response()->json(['message' => 'Banner deleted successfully.']);
    }

    /**
     * Update banner settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'carousel_enabled' => 'nullable|boolean',
            'slide_duration' => 'nullable|integer|min:1|max:30',
            'show_indicators' => 'nullable|boolean',
            'show_controls' => 'nullable|boolean',
            'transition' => 'nullable|in:slide,fade',
            'banner_height' => 'nullable|integer|min:100|max:800',
            'mobile_banner_height' => 'nullable|integer|min:100|max:500',
        ]);

        DB::table('banner_settings')->where('id', 1)->update([
            'carousel_enabled' => $request->carousel_enabled ?? true,
            'slide_duration' => $request->slide_duration ?? 5,
            'show_indicators' => $request->show_indicators ?? true,
            'show_controls' => $request->show_controls ?? true,
            'transition' => $request->transition ?? 'slide',
            'banner_height' => $request->banner_height ?? 400,
            'mobile_banner_height' => $request->mobile_banner_height ?? 250,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Settings updated successfully.']);
    }

    /**
     * Reorder banners.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer',
            'order.*.display_order' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            DB::table('homepage_banners')
                ->where('id', $item['id'])
                ->update(['display_order' => $item['display_order']]);
        }

        return response()->json(['message' => 'Order updated successfully.']);
    }

    /**
     * Upload image helper.
     */
    private function uploadImage($file, $folder)
    {
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');
        return 'storage/' . $path;
    }
}
