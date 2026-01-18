<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApiLogController extends Controller
{
    /**
     * Get paginated API logs with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $query = DB::table('api_logs')
            ->leftJoin('dropshippers', 'api_logs.dropshipper_id', '=', 'dropshippers.id')
            ->select(
                'api_logs.*',
                'dropshippers.company_name as dropshipper_name'
            );

        // Filter by dropshipper
        if ($request->has('dropshipper_id') && $request->dropshipper_id) {
            $query->where('api_logs.dropshipper_id', $request->dropshipper_id);
        }

        // Filter by endpoint
        if ($request->has('endpoint') && $request->endpoint) {
            $query->where('api_logs.endpoint', 'like', "%{$request->endpoint}%");
        }

        // Filter by status code
        if ($request->has('status_code') && $request->status_code) {
            $query->where('api_logs.status_code', $request->status_code);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('api_logs.created_at', $request->date);
        }

        // Get total count for pagination
        $total = $query->count();

        // Get paginated results
        $logs = $query
            ->orderByDesc('api_logs.created_at')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Calculate pagination meta
        $totalPages = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
            'meta' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ]);
    }

    /**
     * Get API log stats for last 24 hours.
     */
    public function stats(): JsonResponse
    {
        $since = now()->subHours(24);

        $totalRequests = DB::table('api_logs')
            ->where('created_at', '>=', $since)
            ->count();

        $successCount = DB::table('api_logs')
            ->where('created_at', '>=', $since)
            ->whereBetween('status_code', [200, 299])
            ->count();

        $errorCount = DB::table('api_logs')
            ->where('created_at', '>=', $since)
            ->where('status_code', '>=', 400)
            ->count();

        $avgResponseTime = DB::table('api_logs')
            ->where('created_at', '>=', $since)
            ->avg('response_time');

        $successRate = $totalRequests > 0
            ? round(($successCount / $totalRequests) * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_requests' => $totalRequests,
                'success_rate' => $successRate,
                'avg_response_time' => round($avgResponseTime ?? 0),
                'errors' => $errorCount
            ]
        ]);
    }

    /**
     * Get a single log entry detail.
     */
    public function show(int $id): JsonResponse
    {
        $log = DB::table('api_logs')
            ->leftJoin('dropshippers', 'api_logs.dropshipper_id', '=', 'dropshippers.id')
            ->select(
                'api_logs.*',
                'dropshippers.company_name as dropshipper_name',
                'dropshippers.email as dropshipper_email'
            )
            ->where('api_logs.id', $id)
            ->first();

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log entry not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    /**
     * Get dropshippers list for filter dropdown.
     */
    public function dropshippers(): JsonResponse
    {
        $dropshippers = DB::table('dropshippers')
            ->select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dropshippers
        ]);
    }

    /**
     * Get unique endpoints for filter dropdown.
     */
    public function endpoints(): JsonResponse
    {
        $endpoints = DB::table('api_logs')
            ->select('endpoint')
            ->distinct()
            ->orderBy('endpoint')
            ->pluck('endpoint');

        return response()->json([
            'success' => true,
            'data' => $endpoints
        ]);
    }

    /**
     * Clear all API logs.
     */
    public function clearAll(): JsonResponse
    {
        $count = DB::table('api_logs')->count();
        DB::table('api_logs')->truncate();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$count} log entries"
        ]);
    }
}
