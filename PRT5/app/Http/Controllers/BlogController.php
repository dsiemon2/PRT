<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::where('status', 'published')
            ->where('publish_date', '<=', now());

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('publish_date', 'desc')
            ->paginate(9)
            ->withQueryString();

        $categories = BlogCategory::withCount(['posts' => function ($q) {
            $q->where('status', 'published')
              ->where('publish_date', '<=', now());
        }])->get();

        $recentPosts = BlogPost::where('status', 'published')
            ->where('publish_date', '<=', now())
            ->orderBy('publish_date', 'desc')
            ->take(5)
            ->get();

        return view('blog.index', compact('posts', 'categories', 'recentPosts'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->where('publish_date', '<=', now())
            ->firstOrFail();

        // Increment view count
        $post->increment('views');

        // Related posts from same category
        $relatedPosts = BlogPost::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->where('publish_date', '<=', now())
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
