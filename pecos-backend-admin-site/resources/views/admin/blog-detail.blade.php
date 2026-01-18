@extends('layouts.admin')

@section('title', 'View Blog Post')

@section('content')
<div class="page-header">
    <h1>{{ $post['title'] ?? 'Blog Post' }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog') }}">Blog</a></li>
            <li class="breadcrumb-item active">View Post</li>
        </ol>
    </nav>
</div>

@if($post)
<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Post Content -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Post Content</h5>
                <span class="status-badge {{ ($post['status'] ?? '') == 'published' ? 'active' : (($post['status'] ?? '') == 'scheduled' ? 'pending' : 'inactive') }}">
                    {{ ucfirst($post['status'] ?? 'Draft') }}
                </span>
            </div>
            <div class="card-body">
                @if($post['featured_image'] ?? null)
                <div class="mb-4 text-center">
                    <img src="{{ config('services.storefront.url') }}/assets/{{ $post['featured_image'] }}"
                         alt="{{ $post['title'] ?? '' }}"
                         class="img-fluid rounded"
                         style="max-height: 300px; object-fit: cover;">
                </div>
                @endif

                @if($post['excerpt'] ?? null)
                <div class="alert alert-light mb-4">
                    <strong>Excerpt:</strong><br>
                    {{ $post['excerpt'] }}
                </div>
                @endif

                <div class="post-content">
                    {!! $post['content'] ?? '<p class="text-muted">No content</p>' !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Post Details -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Post Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Author</td>
                        <td><strong>{{ $post['author_name'] ?? 'Unknown' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Category</td>
                        <td>
                            @if(is_array($post['category'] ?? null))
                                {{ $post['category']['name'] ?? 'Uncategorized' }}
                            @else
                                {{ $post['category'] ?? 'Uncategorized' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Views</td>
                        <td>{{ number_format($post['views'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Published</td>
                        <td>{{ isset($post['publish_date']) ? date('M d, Y', strtotime($post['publish_date'])) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created</td>
                        <td>{{ isset($post['created_at']) ? date('M d, Y H:i', strtotime($post['created_at'])) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Updated</td>
                        <td>{{ isset($post['updated_at']) ? date('M d, Y H:i', strtotime($post['updated_at'])) : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SEO Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">SEO Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Slug:</strong><br>
                    <code>{{ $post['slug'] ?? 'N/A' }}</code>
                </p>
                <p class="mb-2"><strong>Meta Title:</strong><br>
                    {{ $post['meta_title'] ?? $post['title'] ?? 'N/A' }}
                </p>
                <p class="mb-2"><strong>Meta Description:</strong><br>
                    {{ $post['meta_description'] ?? $post['excerpt'] ?? 'N/A' }}
                </p>
                @if($post['meta_keywords'] ?? null)
                <p class="mb-0"><strong>Keywords:</strong><br>
                    {{ $post['meta_keywords'] }}
                </p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.blog') }}" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Blog List
                </a>
                <button class="btn btn-prt w-100" onclick="editPost()">
                    <i class="bi bi-pencil"></i> Edit Post
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Post Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title" id="postModalLabel">Edit Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postForm">
                    <input type="hidden" id="postId" name="id" value="{{ $post['id'] }}">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="postTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="postTitle" name="title" required value="{{ $post['title'] ?? '' }}">
                        </div>

                        <div class="col-md-4">
                            <label for="postStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="postStatus" name="status" required>
                                <option value="draft" {{ ($post['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ ($post['status'] ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="scheduled" {{ ($post['status'] ?? '') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="postCategory" class="form-label">Category</label>
                            <select class="form-select" id="postCategory" name="category_id">
                                <option value="">-- Select Category --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="postAuthor" class="form-label">Author Name</label>
                            <input type="text" class="form-control" id="postAuthor" name="author_name" value="{{ $post['author_name'] ?? '' }}">
                        </div>

                        <div class="col-12">
                            <label for="postImagePath" class="form-label">Featured Image Path</label>
                            <input type="text" class="form-control" id="postImagePath" name="featured_image" value="{{ $post['featured_image'] ?? '' }}" placeholder="images/blog/your-image.jpg">
                        </div>

                        <div class="col-12">
                            <label for="postExcerpt" class="form-label">Excerpt</label>
                            <textarea class="form-control" id="postExcerpt" name="excerpt" rows="2">{{ $post['excerpt'] ?? '' }}</textarea>
                        </div>

                        <div class="col-12">
                            <label for="postContent" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="postContent" name="content" rows="8" required>{{ $post['content'] ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="postMetaTitle" class="form-label">SEO Title</label>
                            <input type="text" class="form-control" id="postMetaTitle" name="meta_title" value="{{ $post['meta_title'] ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="postMetaDesc" class="form-label">SEO Description</label>
                            <input type="text" class="form-control" id="postMetaDesc" name="meta_description" value="{{ $post['meta_description'] ?? '' }}">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="savePost()">
                    <i class="bi bi-save"></i> Update Post
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let postModal;

document.addEventListener('DOMContentLoaded', function() {
    postModal = new bootstrap.Modal(document.getElementById('postModal'));
});

function editPost() {
    postModal.show();
}

async function savePost() {
    const postId = document.getElementById('postId').value;

    const formData = {
        title: document.getElementById('postTitle').value,
        status: document.getElementById('postStatus').value,
        category_id: document.getElementById('postCategory').value || null,
        author_name: document.getElementById('postAuthor').value || 'Pecos River Team',
        featured_image: document.getElementById('postImagePath').value || null,
        excerpt: document.getElementById('postExcerpt').value,
        content: document.getElementById('postContent').value,
        meta_title: document.getElementById('postMetaTitle').value,
        meta_description: document.getElementById('postMetaDesc').value
    };

    try {
        const response = await fetch(`${API_BASE}/admin/blog/${postId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.ok) {
            alert('Post updated successfully');
            postModal.hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save post'));
        }
    } catch (error) {
        console.error('Save error:', error);
        alert('Error saving post');
    }
}
</script>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> Blog post not found.
</div>
@endif
@endsection
