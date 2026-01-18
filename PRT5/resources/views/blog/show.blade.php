@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            @if($post->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('blog.index', ['category' => $post->category_id]) }}">
                        {{ $post->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ Str::limit($post->title, 30) }}</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <article>
                {{-- Header --}}
                <header class="mb-4">
                    @if($post->category)
                        <a href="{{ route('blog.index', ['category' => $post->category_id]) }}"
                           class="badge bg-secondary text-decoration-none mb-2">
                            {{ $post->category->name }}
                        </a>
                    @endif
                    <h1 style="color: var(--prt-brown);">{{ $post->title }}</h1>
                    <div class="text-muted">
                        <i class="bi bi-calendar"></i> {{ $post->published_at->format('F j, Y') }}
                        <span class="mx-2">|</span>
                        <i class="bi bi-eye"></i> {{ number_format($post->view_count) }} views
                        @if($post->author)
                            <span class="mx-2">|</span>
                            <i class="bi bi-person"></i> {{ $post->author->name }}
                        @endif
                    </div>
                </header>

                {{-- Featured Image --}}
                @if($post->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                             alt="{{ $post->title }}"
                             class="img-fluid rounded w-100"
                             style="max-height: 400px; object-fit: cover;">
                    </div>
                @endif

                {{-- Content --}}
                <div class="card">
                    <div class="card-body blog-content">
                        {!! $post->content !!}
                    </div>
                </div>

                {{-- Tags --}}
                @if($post->tags)
                    <div class="mt-4">
                        <i class="bi bi-tags"></i>
                        @foreach(explode(',', $post->tags) as $tag)
                            <span class="badge bg-light text-dark">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Share --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-share"></i> Share this article</h6>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                               target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-twitter"></i> Twitter
                            </a>
                            <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(request()->url()) }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-envelope"></i> Email
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Related Posts --}}
            @if($relatedPosts->count() > 0)
                <div class="mt-5">
                    <h4 style="color: var(--prt-brown);">Related Posts</h4>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($related->featured_image)
                                        <a href="{{ route('blog.show', $related->slug) }}">
                                            <img src="{{ asset('storage/' . $related->featured_image) }}"
                                                 class="card-img-top"
                                                 alt="{{ $related->title }}"
                                                 style="height: 120px; object-fit: cover;">
                                        </a>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('blog.show', $related->slug) }}"
                                               class="text-decoration-none" style="color: var(--prt-brown);">
                                                {{ Str::limit($related->title, 40) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $related->published_at->format('M j, Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Blog
                </a>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Author Card --}}
            @if($post->author)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person"></i> About the Author</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person fs-1 text-muted"></i>
                        </div>
                        <h6>{{ $post->author->name }}</h6>
                    </div>
                </div>
            @endif

            {{-- Categories --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-folder"></i> Categories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('blog.index') }}" class="list-group-item list-group-item-action">
                        All Posts
                    </a>
                    @if($post->category)
                        <a href="{{ route('blog.index', ['category' => $post->category_id]) }}"
                           class="list-group-item list-group-item-action active">
                            {{ $post->category->name }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Newsletter --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-envelope"></i> Stay Updated</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Subscribe to our newsletter for the latest updates and offers.</p>
                    <form>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Enter your email">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.blog-content {
    line-height: 1.8;
}
.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}
.blog-content h2, .blog-content h3, .blog-content h4 {
    margin-top: 1.5rem;
    color: var(--prt-brown);
}
.blog-content p {
    margin-bottom: 1rem;
}
.blog-content ul, .blog-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}
</style>
@endsection
