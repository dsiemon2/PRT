<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    /**
     * Get all FAQs grouped by category.
     *
     * @OA\Get(
     *     path="/faqs",
     *     summary="Get all FAQs",
     *     tags={"FAQs"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Faq::active()->with('category');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $faqs = $query->orderBy('display_order')->get();

        // Group by category
        $grouped = $faqs->groupBy('category_id');

        return response()->json([
            'success' => true,
            'data' => $faqs,
            'grouped' => $grouped
        ]);
    }

    /**
     * Get FAQ categories with counts.
     *
     * @OA\Get(
     *     path="/faqs/categories",
     *     summary="Get FAQ categories",
     *     tags={"FAQs"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = FaqCategory::withCount(['faqs' => function ($q) {
                $q->active();
            }])
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get a single FAQ.
     *
     * @OA\Get(
     *     path="/faqs/{id}",
     *     summary="Get FAQ by ID",
     *     tags={"FAQs"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="FAQ not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $faq = Faq::with('category')->find($id);

        if (!$faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found'
            ], 404);
        }

        // Increment views
        $faq->increment('views');

        return response()->json([
            'success' => true,
            'data' => $faq
        ]);
    }

    /**
     * Mark FAQ as helpful.
     *
     * @OA\Post(
     *     path="/faqs/{id}/helpful",
     *     summary="Mark FAQ as helpful",
     *     tags={"FAQs"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Marked as helpful"),
     *     @OA\Response(response=404, description="FAQ not found")
     * )
     */
    public function helpful(int $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found'
            ], 404);
        }

        $faq->increment('helpful_count');

        return response()->json([
            'success' => true,
            'message' => 'Marked as helpful'
        ]);
    }

    /**
     * Mark FAQ as not helpful.
     *
     * @OA\Post(
     *     path="/faqs/{id}/not-helpful",
     *     summary="Mark FAQ as not helpful",
     *     tags={"FAQs"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Marked as not helpful"),
     *     @OA\Response(response=404, description="FAQ not found")
     * )
     */
    public function notHelpful(int $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found'
            ], 404);
        }

        $faq->increment('not_helpful_count');

        return response()->json([
            'success' => true,
            'message' => 'Marked as not helpful'
        ]);
    }

    /**
     * Get FAQ statistics for admin dashboard.
     */
    public function adminStats(Request $request): JsonResponse
    {
        $sortBy = $request->get('sort', 'helpful_ratio');

        // Build ORDER BY clause
        $orderBy = match($sortBy) {
            'views' => 'f.views DESC',
            'helpful' => 'f.helpful_count DESC',
            'not_helpful' => 'f.not_helpful_count DESC',
            'helpful_ratio' => '(f.helpful_count / NULLIF(f.helpful_count + f.not_helpful_count, 0)) DESC',
            'total_votes' => '(f.helpful_count + f.not_helpful_count) DESC',
            default => 'f.views DESC'
        };

        // Get FAQ statistics
        $faqs = DB::select("SELECT
            f.id,
            f.question,
            f.views,
            f.helpful_count,
            f.not_helpful_count,
            (f.helpful_count + f.not_helpful_count) as total_votes,
            CASE
                WHEN (f.helpful_count + f.not_helpful_count) > 0
                THEN ROUND((f.helpful_count / (f.helpful_count + f.not_helpful_count)) * 100, 1)
                ELSE 0
            END as helpful_percentage,
            fc.name as category_name,
            fc.icon as category_icon
        FROM faqs f
        LEFT JOIN faq_categories fc ON f.category_id = fc.id
        WHERE f.status = 'active'
        ORDER BY {$orderBy}");

        // Get summary statistics
        $stats = DB::table('faqs')
            ->where('status', 'active')
            ->select(
                DB::raw('COUNT(*) as total_faqs'),
                DB::raw('SUM(views) as total_views'),
                DB::raw('SUM(helpful_count) as total_helpful'),
                DB::raw('SUM(not_helpful_count) as total_not_helpful'),
                DB::raw('AVG(views) as avg_views'),
                DB::raw('ROUND(SUM(helpful_count) / NULLIF(SUM(helpful_count) + SUM(not_helpful_count), 0) * 100, 1) as overall_helpful_percentage')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'faqs' => $faqs,
                'stats' => $stats
            ]
        ]);
    }
}
