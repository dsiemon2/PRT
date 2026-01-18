<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * Get all published blog posts.
     *
     * @OA\Get(
     *     path="/blog",
     *     summary="Get all published blog posts",
     *     tags={"Blog"},
     *     @OA\Parameter(name="category", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=9)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::published()->with('category');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->get('per_page', 9), 50);
        $posts = $query->orderBy('publish_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]
        ]);
    }

    /**
     * Get a single blog post by slug.
     *
     * @OA\Get(
     *     path="/blog/{slug}",
     *     summary="Get blog post by slug",
     *     tags={"Blog"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Post not found")
     * )
     */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with('category')
            ->first();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        // Increment views
        $post->increment('views');

        // Get related posts
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'publish_date']);

        return response()->json([
            'success' => true,
            'data' => $post,
            'related' => $relatedPosts
        ]);
    }

    /**
     * Get a single blog post by ID (for admin).
     */
    public function showById(int $id): JsonResponse
    {
        $post = BlogPost::with('category')->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }

    /**
     * Get blog categories.
     *
     * @OA\Get(
     *     path="/blog/categories",
     *     summary="Get blog categories",
     *     tags={"Blog"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = BlogCategory::withCount(['posts' => function ($q) {
                $q->published();
            }])
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get recent posts.
     *
     * @OA\Get(
     *     path="/blog/recent",
     *     summary="Get recent blog posts",
     *     tags={"Blog"},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=5)),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 5), 20);

        $posts = BlogPost::published()
            ->orderBy('publish_date', 'desc')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'publish_date']);

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }

    // Admin methods

    /**
     * Admin: Get all posts (including drafts).
     *
     * @OA\Get(
     *     path="/admin/blog",
     *     summary="Get all blog posts (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = BlogPost::with('category');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'total' => $posts->total(),
            ]
        ]);
    }

    /**
     * Admin: Create blog post.
     *
     * @OA\Post(
     *     path="/admin/blog",
     *     summary="Create blog post (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "slug", "content", "status"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="excerpt", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="status", type="string", enum={"draft", "published", "scheduled"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Post created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'author_name' => 'nullable|string|max:100',
            'status' => 'required|in:draft,published,scheduled',
            'publish_date' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $post = BlogPost::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    /**
     * Admin: Update blog post.
     *
     * @OA\Put(
     *     path="/admin/blog/{id}",
     *     summary="Update blog post (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post updated"),
     *     @OA\Response(response=404, description="Post not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = BlogPost::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'author_name' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published,scheduled',
            'publish_date' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post->fresh()
        ]);
    }

    /**
     * Admin: Delete blog post.
     *
     * @OA\Delete(
     *     path="/admin/blog/{id}",
     *     summary="Delete blog post (admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post deleted"),
     *     @OA\Response(response=404, description="Post not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $post = BlogPost::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}
