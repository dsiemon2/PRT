<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchFacet;
use App\Models\SearchSynonym;
use App\Models\SearchRedirect;
use App\Models\SearchBoost;
use App\Models\SearchQuery;
use App\Models\PopularSearch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    // =====================
    // FACETS
    // =====================

    public function facets(Request $request): JsonResponse
    {
        $query = SearchFacet::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $facets = $query->orderBy('sort_order')->get();

        return response()->json(['data' => $facets]);
    }

    public function showFacet($id): JsonResponse
    {
        $facet = SearchFacet::findOrFail($id);

        return response()->json(['data' => $facet]);
    }

    public function storeFacet(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:search_facets,code',
            'type' => 'required|string',
            'attribute_name' => 'nullable|string',
            'options' => 'nullable|array',
            'is_active' => 'boolean',
            'is_collapsed' => 'boolean',
            'sort_order' => 'integer',
            'max_options' => 'integer|min:1',
            'show_count' => 'boolean',
        ]);

        $facet = SearchFacet::create($validated);

        return response()->json(['data' => $facet, 'message' => 'Facet created'], 201);
    }

    public function updateFacet(Request $request, $id): JsonResponse
    {
        $facet = SearchFacet::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:search_facets,code,' . $id,
            'type' => 'string',
            'attribute_name' => 'nullable|string',
            'options' => 'nullable|array',
            'is_active' => 'boolean',
            'is_collapsed' => 'boolean',
            'sort_order' => 'integer',
            'max_options' => 'integer|min:1',
            'show_count' => 'boolean',
        ]);

        $facet->update($validated);

        return response()->json(['data' => $facet, 'message' => 'Facet updated']);
    }

    public function deleteFacet($id): JsonResponse
    {
        $facet = SearchFacet::findOrFail($id);
        $facet->delete();

        return response()->json(['message' => 'Facet deleted']);
    }

    public function reorderFacets(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facets' => 'required|array',
            'facets.*.id' => 'required|exists:search_facets,id',
            'facets.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['facets'] as $item) {
            SearchFacet::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Facets reordered']);
    }

    public function getFacetTypes(): JsonResponse
    {
        return response()->json(['data' => SearchFacet::getTypes()]);
    }

    // =====================
    // SYNONYMS
    // =====================

    public function synonyms(Request $request): JsonResponse
    {
        $query = SearchSynonym::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('term', 'like', "%{$search}%")
                  ->orWhere('synonyms', 'like', "%{$search}%");
            });
        }

        $synonyms = $query->orderBy('term')->paginate($request->get('per_page', 50));

        return response()->json($synonyms);
    }

    public function storeSynonym(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'term' => 'required|string|max:255',
            'synonyms' => 'required|string',
            'is_bidirectional' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $synonym = SearchSynonym::create($validated);

        return response()->json(['data' => $synonym, 'message' => 'Synonym created'], 201);
    }

    public function updateSynonym(Request $request, $id): JsonResponse
    {
        $synonym = SearchSynonym::findOrFail($id);

        $validated = $request->validate([
            'term' => 'string|max:255',
            'synonyms' => 'string',
            'is_bidirectional' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $synonym->update($validated);

        return response()->json(['data' => $synonym, 'message' => 'Synonym updated']);
    }

    public function deleteSynonym($id): JsonResponse
    {
        $synonym = SearchSynonym::findOrFail($id);
        $synonym->delete();

        return response()->json(['message' => 'Synonym deleted']);
    }

    // =====================
    // REDIRECTS
    // =====================

    public function redirects(Request $request): JsonResponse
    {
        $redirects = SearchRedirect::orderBy('search_term')->paginate($request->get('per_page', 50));

        return response()->json($redirects);
    }

    public function storeRedirect(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_term' => 'required|string|max:255|unique:search_redirects,search_term',
            'redirect_url' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        $redirect = SearchRedirect::create($validated);

        return response()->json(['data' => $redirect, 'message' => 'Redirect created'], 201);
    }

    public function updateRedirect(Request $request, $id): JsonResponse
    {
        $redirect = SearchRedirect::findOrFail($id);

        $validated = $request->validate([
            'search_term' => 'string|max:255|unique:search_redirects,search_term,' . $id,
            'redirect_url' => 'string|max:500',
            'is_active' => 'boolean',
        ]);

        $redirect->update($validated);

        return response()->json(['data' => $redirect, 'message' => 'Redirect updated']);
    }

    public function deleteRedirect($id): JsonResponse
    {
        $redirect = SearchRedirect::findOrFail($id);
        $redirect->delete();

        return response()->json(['message' => 'Redirect deleted']);
    }

    // =====================
    // BOOSTS
    // =====================

    public function boosts(Request $request): JsonResponse
    {
        $query = SearchBoost::with('product');

        if ($request->has('search_term')) {
            $query->where('search_term', 'like', '%' . $request->search_term . '%');
        }

        $boosts = $query->orderBy('search_term')->paginate($request->get('per_page', 50));

        return response()->json($boosts);
    }

    public function storeBoost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_term' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'boost_value' => 'integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $boost = SearchBoost::create($validated);

        return response()->json(['data' => $boost->load('product'), 'message' => 'Boost created'], 201);
    }

    public function updateBoost(Request $request, $id): JsonResponse
    {
        $boost = SearchBoost::findOrFail($id);

        $validated = $request->validate([
            'search_term' => 'string|max:255',
            'product_id' => 'exists:products,id',
            'boost_value' => 'integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $boost->update($validated);

        return response()->json(['data' => $boost->load('product'), 'message' => 'Boost updated']);
    }

    public function deleteBoost($id): JsonResponse
    {
        $boost = SearchBoost::findOrFail($id);
        $boost->delete();

        return response()->json(['message' => 'Boost deleted']);
    }

    // =====================
    // ANALYTICS & QUERIES
    // =====================

    public function searchQueries(Request $request): JsonResponse
    {
        $query = SearchQuery::with('customer');

        if ($request->has('has_results')) {
            $query->where('has_results', $request->boolean('has_results'));
        }

        if ($request->has('search')) {
            $query->where('query', 'like', '%' . $request->search . '%');
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $queries = $query->orderByDesc('created_at')->paginate($request->get('per_page', 50));

        return response()->json($queries);
    }

    public function popularSearches(Request $request): JsonResponse
    {
        $query = PopularSearch::query();

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $searches = $query->orderByDesc('search_count')->paginate($request->get('per_page', 50));

        return response()->json($searches);
    }

    public function toggleFeatured($id): JsonResponse
    {
        $search = PopularSearch::findOrFail($id);
        $search->update(['is_featured' => !$search->is_featured]);

        return response()->json([
            'data' => $search,
            'message' => $search->is_featured ? 'Added to featured' : 'Removed from featured'
        ]);
    }

    public function zeroResultQueries(): JsonResponse
    {
        $queries = SearchQuery::getZeroResultQueries();

        return response()->json(['data' => $queries]);
    }

    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'facets' => [
                'total' => SearchFacet::count(),
                'active' => SearchFacet::where('is_active', true)->count(),
            ],
            'synonyms' => [
                'total' => SearchSynonym::count(),
                'active' => SearchSynonym::where('is_active', true)->count(),
            ],
            'redirects' => [
                'total' => SearchRedirect::count(),
                'active' => SearchRedirect::where('is_active', true)->count(),
            ],
            'boosts' => [
                'total' => SearchBoost::count(),
                'active' => SearchBoost::where('is_active', true)->count(),
            ],
            'searches' => [
                'today' => SearchQuery::where('created_at', '>=', $today)->count(),
                'this_month' => SearchQuery::where('created_at', '>=', $thisMonth)->count(),
                'zero_results_today' => SearchQuery::where('created_at', '>=', $today)
                    ->where('has_results', false)
                    ->count(),
            ],
            'popular' => [
                'total' => PopularSearch::count(),
                'featured' => PopularSearch::where('is_featured', true)->count(),
            ],
            'top_searches' => PopularSearch::orderByDesc('search_count')
                ->limit(10)
                ->pluck('query'),
            'top_zero_result' => SearchQuery::select('query')
                ->selectRaw('COUNT(*) as count')
                ->where('has_results', false)
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
        ];

        return response()->json(['data' => $stats]);
    }
}
