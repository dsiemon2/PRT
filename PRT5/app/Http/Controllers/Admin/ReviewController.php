<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Traits\HasGridFeatures;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use HasGridFeatures;

    public function index(Request $request)
    {
        $query = ProductReview::with(['user', 'product']);

        // Apply search
        $this->applySearch($query, $request, ['review_text', 'review_title']);

        // Apply status filter
        $this->applyStatusFilter($query, $request);

        // Apply rating filter
        if ($request->filled('rating') && $request->get('rating') !== 'all') {
            $query->where('rating', $request->get('rating'));
        }

        // Apply sorting
        $this->applySorting($query, $request, 'created_at', 'desc');

        // Get paginated results
        $reviews = $this->getPaginated($query, $request);

        // Get stats
        $stats = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::pending()->count(),
            'approved' => ProductReview::approved()->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
            'spam' => ProductReview::where('status', 'spam')->count(),
            'avg_rating' => ProductReview::avg('rating') ?? 0,
        ];

        // Get filter options
        $filters = $this->getFilterOptions($request, [
            'rating' => $request->get('rating', 'all'),
        ]);

        return view('admin.reviews.index', compact('reviews', 'stats', 'filters'));
    }

    public function update(Request $request, ProductReview $review)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,spam',
        ]);

        $review->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Review status updated.',
                'stats' => $this->getStats(),
            ]);
        }

        return back()->with('success', 'Review status updated.');
    }

    public function destroy(Request $request, ProductReview $review)
    {
        $review->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Review deleted.',
                'stats' => $this->getStats(),
            ]);
        }

        return back()->with('success', 'Review deleted.');
    }

    public function bulkAction(Request $request)
    {
        $result = $this->handleBulkAction(
            $request,
            ProductReview::class,
            ['approve', 'reject', 'spam', 'delete']
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
            'total' => ProductReview::count(),
            'pending' => ProductReview::pending()->count(),
            'approved' => ProductReview::approved()->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
            'spam' => ProductReview::where('status', 'spam')->count(),
            'avg_rating' => round(ProductReview::avg('rating') ?? 0, 1),
        ];
    }
}
