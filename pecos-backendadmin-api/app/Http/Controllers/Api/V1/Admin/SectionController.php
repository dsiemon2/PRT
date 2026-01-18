<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    /**
     * Get all homepage sections (admin)
     */
    public function index()
    {
        $sections = DB::table('homepage_sections')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sections
        ]);
    }

    /**
     * Get a single section
     */
    public function show($id)
    {
        $section = DB::table('homepage_sections')->where('id', $id)->first();

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $section
        ]);
    }

    /**
     * Create a new section
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'admin_label' => 'nullable|string|max:255',
            'content' => 'required|string',
            'background_style' => 'nullable|string|in:white,cream,gradient,dark,custom',
            'background_color' => 'nullable|string|max:20',
            'is_visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get max sort order
        $maxOrder = DB::table('homepage_sections')->max('sort_order') ?? 0;

        $id = DB::table('homepage_sections')->insertGetId([
            'title' => $request->title,
            'admin_label' => $request->admin_label,
            'content' => $request->content,
            'background_style' => $request->background_style ?? 'white',
            'background_color' => $request->background_color,
            'is_visible' => $request->is_visible ?? true,
            'sort_order' => $maxOrder + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $section = DB::table('homepage_sections')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Section created successfully',
            'data' => $section
        ], 201);
    }

    /**
     * Update a section
     */
    public function update(Request $request, $id)
    {
        $section = DB::table('homepage_sections')->where('id', $id)->first();

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'admin_label' => 'nullable|string|max:255',
            'content' => 'sometimes|required|string',
            'background_style' => 'nullable|string|in:white,cream,gradient,dark,custom',
            'background_color' => 'nullable|string|max:20',
            'is_visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['updated_at' => now()];

        if ($request->has('title')) $updateData['title'] = $request->title;
        if ($request->has('admin_label')) $updateData['admin_label'] = $request->admin_label;
        if ($request->has('content')) $updateData['content'] = $request->content;
        if ($request->has('background_style')) $updateData['background_style'] = $request->background_style;
        if ($request->has('background_color')) $updateData['background_color'] = $request->background_color;
        if ($request->has('is_visible')) $updateData['is_visible'] = $request->is_visible;

        DB::table('homepage_sections')->where('id', $id)->update($updateData);

        $section = DB::table('homepage_sections')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully',
            'data' => $section
        ]);
    }

    /**
     * Delete a section
     */
    public function destroy($id)
    {
        $section = DB::table('homepage_sections')->where('id', $id)->first();

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }

        DB::table('homepage_sections')->where('id', $id)->delete();

        // Reorder remaining sections
        $sections = DB::table('homepage_sections')->orderBy('sort_order')->get();
        foreach ($sections as $index => $sec) {
            DB::table('homepage_sections')
                ->where('id', $sec->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully'
        ]);
    }

    /**
     * Toggle section visibility
     */
    public function toggleVisibility($id)
    {
        $section = DB::table('homepage_sections')->where('id', $id)->first();

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }

        $newVisibility = !$section->is_visible;

        DB::table('homepage_sections')
            ->where('id', $id)
            ->update([
                'is_visible' => $newVisibility,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => $newVisibility ? 'Section enabled' : 'Section disabled',
            'data' => ['is_visible' => $newVisibility]
        ]);
    }

    /**
     * Reorder sections
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'integer|exists:homepage_sections,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->order as $index => $id) {
            DB::table('homepage_sections')
                ->where('id', $id)
                ->update([
                    'sort_order' => $index + 1,
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sections reordered successfully'
        ]);
    }

    /**
     * Public: Get visible sections for frontend
     */
    public function getPublicSections()
    {
        $sections = DB::table('homepage_sections')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sections
        ]);
    }
}
