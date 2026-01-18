@extends('layouts.admin')

@section('title', 'Blog Management')

@section('content')
<div class="page-header">
    <h1>Blog Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Blog</li>
        </ol>
    </nav>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search posts...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterPosts()">Filter</button>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" onclick="newPost()"><i class="bi bi-plus"></i> New Post</button>
            </div>
        </div>
    </div>
</div>

<!-- Blog Posts Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Views</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="postsTable">
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading posts...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<nav class="mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div id="paginationInfo" class="text-muted small"></div>
        <ul class="pagination mb-0" id="pagination"></ul>
    </div>
</nav>

<!-- Blog Post Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title" id="postModalLabel">Add New Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postForm">
                    <input type="hidden" id="postId" name="id">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="postTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="postTitle" name="title" required>
                        </div>

                        <div class="col-md-4">
                            <label for="postStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="postStatus" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="scheduled">Scheduled</option>
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
                            <input type="text" class="form-control" id="postAuthor" name="author_name" placeholder="Pecos River Team">
                        </div>

                        <div class="col-12">
                            <label for="postImageFile" class="form-label">Featured Image</label>
                            <input type="file" class="form-control mb-2" id="postImageFile" name="image_file" accept="image/*" onchange="previewImage(this)">
                            <div id="imagePreview" class="mb-2" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" style="max-height: 150px; border-radius: 4px;">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearImage()"><i class="bi bi-x"></i> Remove</button>
                            </div>
                            <input type="hidden" id="postImage" name="featured_image">
                            <small class="text-muted">Or enter path manually: </small>
                            <input type="text" class="form-control form-control-sm mt-1" id="postImagePath" placeholder="images/blog/your-image.jpg" onchange="document.getElementById('postImage').value = this.value">
                        </div>

                        <div class="col-12">
                            <label for="postExcerpt" class="form-label">Excerpt (Short Description)</label>
                            <textarea class="form-control tinymce-simple" id="postExcerpt" name="excerpt" rows="2" placeholder="Brief summary of the post..."></textarea>
                        </div>

                        <div class="col-12">
                            <label for="postContent" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control tinymce-full" id="postContent" name="content" rows="8" required placeholder="Write your blog post content here..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="postMetaTitle" class="form-label">SEO Title</label>
                            <input type="text" class="form-control" id="postMetaTitle" name="meta_title" placeholder="Leave blank to use post title">
                        </div>

                        <div class="col-md-6">
                            <label for="postMetaDesc" class="form-label">SEO Description</label>
                            <input type="text" class="form-control" id="postMetaDesc" name="meta_description" placeholder="Leave blank to use excerpt">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="savePost()">
                    <i class="bi bi-save"></i> <span id="saveButtonText">Create Post</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
    color: #333 !important;
}
.table tbody tr.row-selected td strong {
    color: #333 !important;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
</style>

<script>
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.closest('button') || target.closest('a') || target.closest('select')) {
        return;
    }
    var selectedRows = document.querySelectorAll('.table tbody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    row.classList.add('row-selected');
}
</script>
@endsection

@push('scripts')
<!-- TinyMCE CDN (self-hosted, no API key required) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
const STOREFRONT_URL = '{{ rtrim(env("STOREFRONT_URL", "http://localhost:8300"), "/") }}';
let currentPage = 1;
let perPage = 20;
let postModal;
let allCategories = [];

document.addEventListener('DOMContentLoaded', function() {
    postModal = new bootstrap.Modal(document.getElementById('postModal'));
    loadCategories();
    loadPosts();

    // Initialize TinyMCE when modal is shown
    document.getElementById('postModal').addEventListener('shown.bs.modal', function() {
        initTinyMCE();
    });

    // Destroy TinyMCE when modal is hidden
    document.getElementById('postModal').addEventListener('hidden.bs.modal', function() {
        destroyTinyMCE();
    });
});

function initTinyMCE() {
    // Full editor for content
    tinymce.init({
        selector: '.tinymce-full',
        height: 400,
        menubar: false,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat code',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
        branding: false,
        promotion: false,
        paste_data_images: true,
        automatic_uploads: true,
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var reader = new FileReader();
                reader.onload = function() { resolve(reader.result); };
                reader.onerror = function() { reject('Failed to read file'); };
                reader.readAsDataURL(blobInfo.blob());
            });
        },
        setup: function(editor) {
            editor.on('change', function() { editor.save(); });
        }
    });

    // Simple editor for excerpt
    tinymce.init({
        selector: '.tinymce-simple',
        height: 150,
        menubar: false,
        plugins: 'link lists',
        toolbar: 'undo redo | bold italic underline | link | removeformat',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
        branding: false,
        promotion: false,
        setup: function(editor) {
            editor.on('change', function() { editor.save(); });
        }
    });
}

function destroyTinyMCE() {
    tinymce.remove('.tinymce-full');
    tinymce.remove('.tinymce-simple');
}

async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE}/blog/categories`);
        const data = await response.json();
        allCategories = data.data || [];

        const select = document.getElementById('postCategory');
        select.innerHTML = '<option value="">-- Select Category --</option>';
        allCategories.forEach(cat => {
            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        });
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

async function loadPosts(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/blog?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;

        const response = await fetch(url);
        const data = await response.json();

        const posts = data.data || [];
        const meta = {
            current_page: data.current_page || data.meta?.current_page || 1,
            last_page: data.last_page || data.meta?.last_page || 1,
            from: data.from || data.meta?.from || 1,
            to: data.to || data.meta?.to || posts.length,
            total: data.total || data.meta?.total || posts.length,
            per_page: data.per_page || data.meta?.per_page || perPage
        };

        renderPosts(posts);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading posts:', error);
        document.getElementById('postsTable').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger">Error loading posts</td></tr>';
    }
}

function renderPosts(posts) {
    const tbody = document.getElementById('postsTable');

    if (posts.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="7" class="text-center py-4 text-muted">No blog posts found</td>
        </tr>`;
        return;
    }

    let html = '';
    posts.forEach(post => {
        const postDate = post.created_at ? new Date(post.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'N/A';
        const category = typeof post.category === 'object' ? (post.category?.name || 'Uncategorized') : (post.category || 'Uncategorized');
        const status = post.status || 'draft';

        let statusClass = 'inactive';
        if (status === 'published') statusClass = 'active';
        else if (status === 'scheduled') statusClass = 'pending';

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td>${post.title || 'Untitled'}</td>`;
        html += `<td>${post.author || post.author_name || 'Admin'}</td>`;
        html += `<td>${category}</td>`;
        html += `<td>${(post.views || 0).toLocaleString()}</td>`;
        html += `<td>${postDate}</td>`;
        html += `<td><span class="status-badge ${statusClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td>`;
        html += `<td>
            <a href="/admin/blog/${post.id}" class="btn btn-sm btn-outline-info" title="View Blog"><i class="bi bi-eye"></i></a>
            <button class="btn btn-sm btn-outline-primary" onclick="editPost(${post.id})" title="Edit"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="deletePost(${post.id}, '${(post.title || 'Untitled').replace(/'/g, "\\'")}')" title="Delete"><i class="bi bi-trash"></i></button>
        </td>`;
        html += `</tr>`;
    });

    tbody.innerHTML = html;
}

function renderPagination(meta) {
    document.getElementById('paginationInfo').textContent =
        `Showing ${meta.from} to ${meta.to} of ${meta.total} entries`;

    const pagination = document.getElementById('pagination');
    let html = '';

    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadPosts(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPosts(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadPosts(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPosts(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadPosts(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterPosts() {
    loadPosts(1);
}

function newPost() {
    document.getElementById('postForm').reset();
    document.getElementById('postId').value = '';
    document.getElementById('postImage').value = '';
    document.getElementById('postImagePath').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('postModalLabel').textContent = 'Add New Post';
    document.getElementById('saveButtonText').textContent = 'Create Post';
    postModal.show();
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage() {
    document.getElementById('postImageFile').value = '';
    document.getElementById('postImage').value = '';
    document.getElementById('postImagePath').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function showExistingImage(imagePath) {
    if (imagePath) {
        document.getElementById('previewImg').src = STOREFRONT_URL + '/assets/' + imagePath;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('postImage').value = imagePath;
        document.getElementById('postImagePath').value = imagePath;
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
}

async function editPost(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/blog/${id}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch post');
        }

        const result = await response.json();
        const post = result.data || result;

        document.getElementById('postId').value = post.id;
        document.getElementById('postTitle').value = post.title || '';
        document.getElementById('postStatus').value = post.status || 'draft';
        document.getElementById('postCategory').value = post.category_id || '';
        document.getElementById('postAuthor').value = post.author_name || '';
        showExistingImage(post.featured_image);
        document.getElementById('postExcerpt').value = post.excerpt || '';
        document.getElementById('postContent').value = post.content || '';
        document.getElementById('postMetaTitle').value = post.meta_title || '';
        document.getElementById('postMetaDesc').value = post.meta_description || '';

        document.getElementById('postModalLabel').textContent = 'Edit Post';
        document.getElementById('saveButtonText').textContent = 'Update Post';
        postModal.show();

        // Set TinyMCE content after modal is shown
        setTimeout(function() {
            var excerptEditor = tinymce.get('postExcerpt');
            var contentEditor = tinymce.get('postContent');
            if (excerptEditor) excerptEditor.setContent(post.excerpt || '');
            if (contentEditor) contentEditor.setContent(post.content || '');
        }, 300);

    } catch (error) {
        console.error('Edit error:', error);
        alert('Error loading post data');
    }
}

async function savePost() {
    const postId = document.getElementById('postId').value;
    const isEdit = !!postId;

    // Get TinyMCE content
    var excerptEditor = tinymce.get('postExcerpt');
    var contentEditor = tinymce.get('postContent');
    var excerptContent = excerptEditor ? excerptEditor.getContent() : document.getElementById('postExcerpt').value;
    var postContent = contentEditor ? contentEditor.getContent() : document.getElementById('postContent').value;

    const formData = {
        title: document.getElementById('postTitle').value,
        status: document.getElementById('postStatus').value,
        category_id: document.getElementById('postCategory').value || null,
        author_name: document.getElementById('postAuthor').value || 'Pecos River Team',
        featured_image: document.getElementById('postImage').value || null,
        excerpt: excerptContent,
        content: postContent,
        meta_title: document.getElementById('postMetaTitle').value,
        meta_description: document.getElementById('postMetaDesc').value
    };

    if (!isEdit) {
        formData.slug = formData.title.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    try {
        const url = isEdit ? `${API_BASE}/admin/blog/${postId}` : `${API_BASE}/admin/blog`;
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.ok) {
            alert(isEdit ? 'Post updated successfully' : 'Post created successfully');
            postModal.hide();
            loadPosts(currentPage);
        } else {
            alert('Error: ' + (data.message || 'Failed to save post'));
        }
    } catch (error) {
        console.error('Save error:', error);
        alert('Error saving post');
    }
}

async function deletePost(id, title) {
    if (!confirm('Are you sure you want to delete "' + title + '"?\n\nThis action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/blog/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            alert('Post deleted successfully');
            loadPosts(currentPage);
        } else {
            alert('Error: ' + (data.message || 'Failed to delete post'));
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('Error deleting post');
    }
}
</script>
@endpush
