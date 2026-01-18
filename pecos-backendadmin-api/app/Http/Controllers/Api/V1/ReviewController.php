<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product.
     *
     * @OA\Get(
     *     path="/products/{productId}/reviews",
     *     summary="Get reviews for a product",
     *     tags={"Reviews"},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string", enum={"newest", "highest", "lowest", "helpful"})),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request, string $productId): JsonResponse
    {
        $query = ProductReview::where('product_id', $productId)
            ->approved()
            ->with('user:id,first_name,last_name');

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = min($request->get('per_page', 10), 50);
        $reviews = $query->paginate($perPage);

        // Get aggregate stats
        $stats = ProductReview::where('product_id', $productId)
            ->approved()
            ->selectRaw('
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_reviews' => (int) $stats->total_reviews,
                'average_rating' => round($stats->average_rating, 1),
                'breakdown' => [
                    5 => (int) $stats->five_star,
                    4 => (int) $stats->four_star,
                    3 => (int) $stats->three_star,
                    2 => (int) $stats->two_star,
                    1 => (int) $stats->one_star,
                ]
            ],
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Submit a review.
     *
     * @OA\Post(
     *     path="/products/{productId}/reviews",
     *     summary="Submit a review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating", "review_text", "reviewer_name", "reviewer_email"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="review_title", type="string"),
     *             @OA\Property(property="review_text", type="string"),
     *             @OA\Property(property="reviewer_name", type="string"),
     *             @OA\Property(property="reviewer_email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Review submitted"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function store(Request $request, string $productId): JsonResponse
    {
        // Check product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:200',
            'review_text' => 'required|string|min:10',
            'reviewer_name' => 'required|string|max:100',
            'reviewer_email' => 'required|email|max:255',
        ]);

        $user = $request->user();

        // Check if user has purchased this product (for verified purchase badge)
        $isVerifiedPurchase = false;
        if ($user) {
            $isVerifiedPurchase = $user->orders()
                ->whereHas('items', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->where('status', 'delivered')
                ->exists();
        }

        $review = ProductReview::create([
            'product_id' => $productId,
            'user_id' => $user->id ?? null,
            'reviewer_name' => $validated['reviewer_name'],
            'reviewer_email' => $validated['reviewer_email'],
            'rating' => $validated['rating'],
            'review_title' => $validated['review_title'] ?? null,
            'review_text' => $validated['review_text'],
            'is_verified_purchase' => $isVerifiedPurchase,
            'status' => 'pending', // Requires moderation
            'helpful_count' => 0,
            'unhelpful_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully and pending approval',
            'data' => $review
        ], 201);
    }

    /**
     * Mark review as helpful.
     *
     * @OA\Post(
     *     path="/reviews/{reviewId}/helpful",
     *     summary="Mark review as helpful",
     *     tags={"Reviews"},
     *     @OA\Parameter(name="reviewId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Marked as helpful"),
     *     @OA\Response(response=404, description="Review not found")
     * )
     */
    public function helpful(int $reviewId): JsonResponse
    {
        $review = ProductReview::find($reviewId);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'message' => 'Marked as helpful',
            'data' => [
                'helpful_count' => $review->helpful_count,
                'unhelpful_count' => $review->unhelpful_count
            ]
        ]);
    }

    /**
     * Mark review as not helpful.
     *
     * @OA\Post(
     *     path="/reviews/{reviewId}/not-helpful",
     *     summary="Mark review as not helpful",
     *     tags={"Reviews"},
     *     @OA\Parameter(name="reviewId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Marked as not helpful"),
     *     @OA\Response(response=404, description="Review not found")
     * )
     */
    public function notHelpful(int $reviewId): JsonResponse
    {
        $review = ProductReview::find($reviewId);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $review->increment('unhelpful_count');

        return response()->json([
            'success' => true,
            'message' => 'Marked as not helpful',
            'data' => [
                'helpful_count' => $review->helpful_count,
                'unhelpful_count' => $review->unhelpful_count
            ]
        ]);
    }

    /**
     * Get user's reviews.
     *
     * @OA\Get(
     *     path="/user/reviews",
     *     summary="Get user's reviews",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function userReviews(Request $request): JsonResponse
    {
        $user = $request->user();

        $reviews = ProductReview::where('user_id', $user->id)
            ->with('product:UPC,ShortDescription,UnitPrice')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Admin: Get all reviews for moderation.
     *
     * @OA\Get(
     *     path="/admin/reviews",
     *     summary="Get all reviews for moderation (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"pending", "approved", "rejected"})),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = ProductReview::with(['product:UPC,ShortDescription,Image', 'user:id,first_name,last_name,email']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('review_title', 'like', "%{$search}%")
                  ->orWhere('review_text', 'like', "%{$search}%")
                  ->orWhere('reviewer_name', 'like', "%{$search}%")
                  ->orWhere('reviewer_email', 'like', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Admin: Update review status.
     *
     * @OA\Patch(
     *     path="/admin/reviews/{reviewId}/status",
     *     summary="Update review status (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="reviewId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "approved", "rejected"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=404, description="Review not found")
     * )
     */
    public function updateStatus(Request $request, int $reviewId): JsonResponse
    {
        $review = ProductReview::find($reviewId);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $review->status = $validated['status'];
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Review status updated',
            'data' => $review
        ]);
    }
}
