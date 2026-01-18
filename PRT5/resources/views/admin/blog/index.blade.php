@extends('layouts.admin')

@section('title', 'Blog Management')

@section('styles')
<style>
    .post-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .post-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .status-draft {
        background-color: #ffc107;
        color: #000;
    }
    .status-published {
        background-color: #198754;
        color: #fff;
    }
    .status-scheduled {
        background-color: #0dcaf0;
        color: #000;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-journal-text"></i> Blog Management</h1>
            <p class="lead text-muted">Create, edit, and manage blog posts</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    <li class="breadcrumb-item active">Blog Management</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add/Edit Blog Post Form -->
    <div class="card mb-4">
        <div class="card-header" style="background-color: var(--prt-brown); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-{{ isset($editPost) ? 'pencil-square' : 'plus-circle' }}"></i>
                {{ isset($editPost) ? 'Edit Blog Post' : 'Add New Blog Post' }}
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ isset($editPost) ? route('admin.blog.update', $editPost) : route('admin.blog.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($editPost))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="{{ old('title', $editPost->title ?? '') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft" {{ (old('status', $editPost->status ?? '') === 'draft') ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ (old('status', $editPost->status ?? '') === 'published') ? 'selected' : '' }}>Published</option>
                            <option value="scheduled" {{ (old('status', $editPost->status ?? '') === 'scheduled') ? 'selected' : '' }}>Scheduled</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (old('category_id', $editPost->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="author_name" class="form-label">Author Name</label>
                        <input type="text" class="form-control" id="author_name" name="author_name"
                               value="{{ old('author_name', $editPost->author_name ?? auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                               placeholder="{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}">
                    </div>

                    <div class="col-12">
                        <label for="featured_image" class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">Accepted formats: JPEG, PNG, GIF, WebP. Max size: 2MB</small>
                        @if(isset($editPost) && $editPost->featured_image)
                            <div class="mt-2">
                                <img src="{{ asset('assets/' . $editPost->featured_image) }}"
                                     alt="Current featured image" style="max-height: 100px; border-radius: 4px;">
                                <small class="d-block text-muted">Current image: {{ $editPost->featured_image }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <label for="excerpt" class="form-label">Excerpt (Short Description)</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2"
                                  placeholder="Brief summary of the post...">{{ old('excerpt', $editPost->excerpt ?? '') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="10" required
                                  placeholder="Write your blog post content here... (HTML supported)">{{ old('content', $editPost->content ?? '') }}</textarea>
                        <small class="text-muted">HTML formatting is supported</small>
                    </div>

                    <div class="col-md-6">
                        <label for="meta_title" class="form-label">SEO Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                               value="{{ old('meta_title', $editPost->meta_title ?? '') }}"
                               placeholder="Leave blank to use post title">
                    </div>

                    <div class="col-md-6">
                        <label for="meta_description" class="form-label">SEO Description</label>
                        <input type="text" class="form-control" id="meta_description" name="meta_description"
                               value="{{ old('meta_description', $editPost->meta_description ?? '') }}"
                               placeholder="Leave blank to use excerpt">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ isset($editPost) ? 'Save changes to this blog post' : 'Create new blog post' }}">
                            <i class="bi bi-{{ isset($editPost) ? 'save' : 'plus-circle' }}"></i>
                            {{ isset($editPost) ? 'Update Post' : 'Create Post' }}
                        </button>
                        @if(isset($editPost))
                            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Cancel editing and return to list">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Blog Posts List -->
    <div class="card">
        <div class="card-header" style="background-color: var(--prt-brown); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> All Blog Posts ({{ $posts->total() }})
            </h5>
        </div>
        <div class="card-body">
            @if($posts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    <td>
                                        <strong>{{ $post->title }}</strong>
                                        @if($post->excerpt)
                                            <br><small class="text-muted">{{ Str::limit($post->excerpt, 50) }}...</small>
                                        @endif
                                    </td>
                                    <td>{{ $post->category->name ?? 'Uncategorized' }}</td>
                                    <td>{{ $post->author_name ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="badge status-{{ $post->status }}">
                                            {{ ucfirst($post->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($post->views ?? 0) }}</td>
                                    <td>{{ $post->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.blog.index', ['edit' => $post->id]) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit this blog post">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete this blog post"
                                                    onclick="deletePost({{ $post->id }}, '{{ addslashes($post->title) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <p class="lead mt-3">No blog posts found</p>
                    <p class="text-muted">Create your first blog post using the form above</p>
                </div>
            @endif

            <!-- Pagination -->
            @if($posts->hasPages())
                <nav class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $posts->firstItem() ?? 0 }} to {{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }} posts
                        </div>
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $posts->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $posts->previousPageUrl() }}" data-bs-toggle="tooltip" title="Go to previous page">Previous</a>
                            </li>

                            @php
                                $currentPage = $posts->currentPage();
                                $lastPage = $posts->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp

                            @if($startPage > 1)
                                <li class="page-item"><a class="page-link" href="{{ $posts->url(1) }}">1</a></li>
                                @if($startPage > 2)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif

                            @for($i = $startPage; $i <= $endPage; $i++)
                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $posts->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                                <li class="page-item"><a class="page-link" href="{{ $posts->url($lastPage) }}">{{ $lastPage }}</a></li>
                            @endif

                            <li class="page-item {{ !$posts->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $posts->nextPageUrl() }}" data-bs-toggle="tooltip" title="Go to next page">Next</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function deletePost(postId, postTitle) {
    if (confirm('Are you sure you want to delete "' + postTitle + '"?\n\nThis action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("admin/blog") }}/' + postId;
        form.submit();
    }
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
