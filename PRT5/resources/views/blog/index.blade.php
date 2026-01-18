@extends('layouts.app')

@section('title', 'Blog')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Blog</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-newspaper"></i> Blog</h1>

    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Search --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search articles..." value="{{ request('search') }}">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Search blog articles">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($posts->count() > 0)
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                @if($post->featured_image)
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                                             class="card-img-top"
                                             alt="{{ $post->title }}"
                                             style="height: 200px; object-fit: cover;">
                                    </a>
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                         style="height: 200px;">
                                        <i class="bi bi-newspaper text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    @if($post->category)
                                        <a href="{{ route('blog.index', ['category' => $post->category_id]) }}"
                                           class="badge bg-secondary text-decoration-none mb-2">
                                            {{ $post->category->name }}
                                        </a>
                                    @endif
                                    <h5 class="card-title">
                                        <a href="{{ route('blog.show', $post->slug) }}"
                                           class="text-decoration-none" style="color: var(--prt-brown);">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        {{ Str::limit($post->excerpt ?? strip_tags($post->content), 120) }}
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i>
                                        {{ $post->published_at->format('M j, Y') }}
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-eye"></i>
                                        {{ number_format($post->view_count) }} views
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-newspaper text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">No posts found</h3>
                        <p class="text-muted">Check back soon for new content!</p>
                        @if(request('search') || request('category'))
                            <a href="{{ route('blog.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Clear filters and view all blog posts">View All Posts</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Categories --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-folder"></i> Categories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('blog.index') }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request('category') ? 'active' : '' }}" data-bs-toggle="tooltip" title="View all blog posts">
                        All Posts
                        <span class="badge bg-primary rounded-pill">{{ $categories->sum('posts_count') }}</span>
                    </a>
                    @foreach($categories as $category)
                        @if($category->posts_count > 0)
                            <a href="{{ route('blog.index', ['category' => $category->id]) }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('category') == $category->id ? 'active' : '' }}" data-bs-toggle="tooltip" title="Filter posts by {{ $category->name }}">
                                {{ $category->name }}
                                <span class="badge bg-primary rounded-pill">{{ $category->posts_count }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Recent Posts --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Posts</h5>
                </div>
                <div class="card-body">
                    @foreach($recentPosts as $recent)
                        <div class="d-flex mb-3">
                            @if($recent->featured_image)
                                <img src="{{ asset('storage/' . $recent->featured_image) }}"
                                     alt="{{ $recent->title }}"
                                     style="width: 60px; height: 60px; object-fit: cover;"
                                     class="me-3 rounded">
                            @else
                                <div class="bg-light me-3 rounded d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px; min-width: 60px;">
                                    <i class="bi bi-newspaper text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('blog.show', $recent->slug) }}"
                                   class="text-decoration-none" style="color: var(--prt-brown);">
                                    <strong>{{ Str::limit($recent->title, 40) }}</strong>
                                </a>
                                <br>
                                <small class="text-muted">{{ $recent->published_at->format('M j, Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
