<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FooterController extends Controller
{
    /**
     * Get full footer configuration including columns and links.
     * Returns data in format expected by admin UI.
     */
    public function index(): JsonResponse
    {
        $columns = DB::table('footer_columns')
            ->orderBy('position')
            ->get();

        $links = DB::table('footer_links')
            ->orderBy('column_id')
            ->orderBy('sort_order')
            ->get();

        // Map position to section keys (for admin UI compatibility)
        $sectionKeys = [
            1 => 'shop',
            2 => 'resources',
            3 => 'customer_service',
            4 => 'connect'
        ];

        // Group links by section key
        $linksBySection = [
            'shop' => [],
            'resources' => [],
            'customer_service' => [],
            'connect' => []
        ];

        // Create column position to id mapping
        $columnPositionToId = [];
        $columnIdToPosition = [];
        foreach ($columns as $column) {
            $columnPositionToId[$column->position] = $column->id;
            $columnIdToPosition[$column->id] = $column->position;
        }

        // Group links by section
        foreach ($links as $link) {
            $position = $columnIdToPosition[$link->column_id] ?? null;
            if ($position && isset($sectionKeys[$position])) {
                $sectionKey = $sectionKeys[$position];
                $linkData = (array) $link;
                // Add link_type field for admin UI (map from is_core)
                $linkData['link_type'] = $link->is_core ? 'core' : ($link->link_type ?? 'custom');
                $linkData['is_active'] = (bool) $link->is_visible;
                $linksBySection[$sectionKey][] = $linkData;
            }
        }

        // Build column settings
        $settings = [];
        foreach ($columns as $column) {
            $settings['column' . $column->position] = $column->title;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'links' => $linksBySection,
                'settings' => $settings,
                'columns' => $columns
            ]
        ]);
    }

    /**
     * Get footer configuration for frontend (public endpoint).
     */
    public function getPublic(): JsonResponse
    {
        $columns = DB::table('footer_columns')
            ->where('is_visible', true)
            ->orderBy('position')
            ->get();

        $links = DB::table('footer_links')
            ->where('is_visible', true)
            ->orderBy('column_id')
            ->orderBy('sort_order')
            ->get();

        // Group links by column
        $linksByColumn = [];
        foreach ($links as $link) {
            if (!isset($linksByColumn[$link->column_id])) {
                $linksByColumn[$link->column_id] = [];
            }
            $linksByColumn[$link->column_id][] = $link;
        }

        // Combine columns with their links
        $result = [];
        foreach ($columns as $column) {
            $columnData = (array) $column;
            $columnData['links'] = $linksByColumn[$column->id] ?? [];
            $result[] = $columnData;
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Update a column's title and visibility.
     */
    public function updateColumn(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:100',
            'is_visible' => 'sometimes|boolean',
        ]);

        $column = DB::table('footer_columns')->where('id', $id)->first();
        if (!$column) {
            return response()->json([
                'success' => false,
                'message' => 'Column not found'
            ], 404);
        }

        DB::table('footer_columns')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json([
            'success' => true,
            'message' => 'Column updated successfully'
        ]);
    }

    /**
     * Update a link's properties.
     */
    public function updateLink(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'sometimes|string|max:100',
            'url' => 'sometimes|string|max:255',
            'icon' => 'sometimes|string|max:50',
            'sort_order' => 'sometimes|integer',
            'is_visible' => 'sometimes|boolean',
        ]);

        $link = DB::table('footer_links')->where('id', $id)->first();
        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link not found'
            ], 404);
        }

        DB::table('footer_links')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json([
            'success' => true,
            'message' => 'Link updated successfully'
        ]);
    }

    /**
     * Batch update link order within a column.
     */
    public function updateLinkOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'links' => 'required|array',
            'links.*.id' => 'required|integer',
            'links.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['links'] as $linkData) {
            DB::table('footer_links')
                ->where('id', $linkData['id'])
                ->update([
                    'sort_order' => $linkData['sort_order'],
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Link order updated successfully'
        ]);
    }

    /**
     * Add a new custom link to a column.
     */
    public function addLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'column_id' => 'required|integer|exists:footer_columns,id',
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'icon' => 'sometimes|string|max:50',
            'link_type' => 'sometimes|in:internal,external,page',
        ]);

        // Get max sort order for this column
        $maxOrder = DB::table('footer_links')
            ->where('column_id', $validated['column_id'])
            ->max('sort_order') ?? 0;

        $id = DB::table('footer_links')->insertGetId([
            'column_id' => $validated['column_id'],
            'label' => $validated['label'],
            'url' => $validated['url'],
            'icon' => $validated['icon'] ?? 'bi-chevron-right',
            'link_type' => $validated['link_type'] ?? 'internal',
            'sort_order' => $maxOrder + 1,
            'is_visible' => true,
            'is_core' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Link added successfully',
            'data' => ['id' => $id]
        ]);
    }

    /**
     * Delete a custom link (core links cannot be deleted).
     */
    public function deleteLink(int $id): JsonResponse
    {
        $link = DB::table('footer_links')->where('id', $id)->first();

        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link not found'
            ], 404);
        }

        if ($link->is_core) {
            return response()->json([
                'success' => false,
                'message' => 'Core links cannot be deleted. You can hide them instead.'
            ], 400);
        }

        DB::table('footer_links')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Link deleted successfully'
        ]);
    }
}
