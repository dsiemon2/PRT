@extends('layouts.admin')

@section('title', 'Featured Categories')

@push('styles')
<style>
.featured-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: grab;
    transition: all 0.2s;
}
.featured-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.featured-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
}
.featured-card .card-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    background: #f8f9fa;
}
.featured-card .card-content {
    flex: 1;
    padding-left: 15px;
}
.featured-card .card-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}
.drag-handle {
    cursor: grab;
    color: #adb5bd;
    padding: 10px;
}
.drag-handle:hover {
    color: #6c757d;
}
.visibility-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}
.add-card {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #fafafa;
}
.add-card:hover {
    border-color: #8B4513;
    background: #fdf8f4;
}
.add-card i {
    font-size: 2rem;
    color: #8B4513;
}
.category-count {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #6c757d;
}
.image-upload-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
    border-radius: 8px;
    cursor: pointer;
}
.image-container:hover .image-upload-overlay {
    opacity: 1;
}
.image-container {
    position: relative;
    width: 80px;
    height: 80px;
}
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Featured Categories</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Featured Categories</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Visibility Toggle -->
<div class="visibility-toggle">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="visibilityToggle" checked>
        <label class="form-check-label fw-bold" for="visibilityToggle">Show Featured Categories Section</label>
    </div>
    <span class="text-muted small">When disabled, the Featured Categories section will be hidden on the homepage</span>
</div>

<!-- Featured Categories Grid -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Featured Categories</h5>
        <span class="badge bg-secondary" id="categoryCount">0 / 9</span>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Drag and drop to reorder. Maximum 9 categories (3 per row).</p>

        <div id="featuredList">
            <!-- Featured categories will be loaded here -->
        </div>

        <!-- Add New Button -->
        <div class="add-card" id="addNewBtn" onclick="showAddModal()">
            <i class="bi bi-plus-circle"></i>
            <div class="mt-2 text-muted">Add Featured Category</div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Featured Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <input type="hidden" id="editCategoryId">

                <!-- Category Image Preview -->
                <div class="mb-3" id="imagePreviewSection" style="display: none;">
                    <label class="form-label">Category Image</label>
                    <div class="d-flex align-items-start gap-3">
                        <div class="image-container" style="width: 120px; height: 120px;">
                            <img id="modalImagePreview" src="" class="card-image" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                            <div class="image-upload-overlay" onclick="document.getElementById('modalImageFile').click()">
                                <i class="bi bi-camera text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" class="form-control form-control-sm" id="modalImageFile" accept="image/*" onchange="previewModalImage(this)">
                            <small class="text-muted d-block mt-1">Click image or choose file to change. Recommended: 400x300px, JPG or PNG</small>
                            <div id="newImagePreview" class="mt-2" style="display: none;">
                                <span class="badge bg-success"><i class="bi bi-check"></i> New image selected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" id="categorySelect" onchange="onCategorySelect()">
                        <option value="">Select a category...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Label</label>
                    <input type="text" class="form-control" id="labelInput" placeholder="e.g., Men's Boots">
                    <small class="text-muted">Display name shown on the card</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="descriptionInput" rows="3" placeholder="Short description for this category..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveCategory()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Category Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="imageCategoryId">

                <div class="text-center mb-3">
                    <img id="currentImage" src="" class="img-fluid rounded" style="max-height: 200px;">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload New Image</label>
                    <input type="file" class="form-control" id="imageFile" accept="image/*">
                    <small class="text-muted">Recommended: 400x300px, JPG or PNG, max 2MB</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="uploadImage()">Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
const STOREFRONT_URL = '{{ rtrim(env("STOREFRONT_URL", "http://localhost:8300"), "/") }}';
let featuredCategories = [];
let allCategories = [];
let categoryModal, imageModal;

document.addEventListener('DOMContentLoaded', function() {
    categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    imageModal = new bootstrap.Modal(document.getElementById('imageModal'));

    loadFeaturedCategories();
    loadAllCategories();
    initSortable();

    // Visibility toggle
    document.getElementById('visibilityToggle').addEventListener('change', function() {
        updateVisibility(this.checked);
    });
});

function initSortable() {
    new Sortable(document.getElementById('featuredList'), {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'dragging',
        onEnd: function() {
            saveOrder();
        }
    });
}

async function loadFeaturedCategories() {
    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories`);
        const data = await response.json();

        if (data.success) {
            featuredCategories = data.data.featured_categories;
            document.getElementById('visibilityToggle').checked = data.data.is_visible;
            renderCategories();
            updateCount();
        }
    } catch (error) {
        console.error('Error loading featured categories:', error);
    }
}

async function loadAllCategories() {
    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories/categories`);
        const data = await response.json();

        if (data.success) {
            allCategories = data.data;
            populateCategoryDropdown();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function populateCategoryDropdown() {
    const select = document.getElementById('categorySelect');
    select.innerHTML = '<option value="">Select a category...</option>';

    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = `${cat.name} (${cat.products_count} products)`;
        select.appendChild(option);
    });
}

function renderCategories() {
    const container = document.getElementById('featuredList');
    container.innerHTML = '';

    featuredCategories.forEach(fc => {
        const imageSrc = fc.category_image
            ? `${STOREFRONT_URL}/assets/${fc.category_image}`
            : `${STOREFRONT_URL}/assets/images/no-image.svg`;

        const card = document.createElement('div');
        card.className = 'featured-card d-flex align-items-center';
        card.dataset.id = fc.id;
        card.innerHTML = `
            <div class="drag-handle">
                <i class="bi bi-grip-vertical fs-4"></i>
            </div>
            <div class="image-container">
                <img src="${imageSrc}" class="card-image" alt="${fc.label}">
                <div class="image-upload-overlay" onclick="showImageModal(${fc.category_id}, '${imageSrc}')">
                    <i class="bi bi-camera text-white fs-4"></i>
                </div>
            </div>
            <div class="card-content">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong>${fc.label}</strong>
                    <span class="category-count">${fc.products_count} Products</span>
                </div>
                <div class="text-muted small">${fc.description || 'No description'}</div>
            </div>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editCategory(${fc.id})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${fc.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(card);
    });

    // Hide add button if max reached
    document.getElementById('addNewBtn').style.display = featuredCategories.length >= 9 ? 'none' : 'block';
}

function updateCount() {
    document.getElementById('categoryCount').textContent = `${featuredCategories.length} / 9`;
}

function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Featured Category';
    document.getElementById('editId').value = '';
    document.getElementById('editCategoryId').value = '';
    document.getElementById('categorySelect').value = '';
    document.getElementById('labelInput').value = '';
    document.getElementById('descriptionInput').value = '';
    document.getElementById('categorySelect').disabled = false;
    document.getElementById('imagePreviewSection').style.display = 'none';
    document.getElementById('modalImageFile').value = '';
    document.getElementById('newImagePreview').style.display = 'none';

    // Filter out already featured categories
    filterCategoryDropdown();

    categoryModal.show();
}

function onCategorySelect() {
    const select = document.getElementById('categorySelect');
    const categoryId = select.value;

    if (categoryId) {
        const category = allCategories.find(c => c.id == categoryId);
        if (category) {
            // Show image preview section
            document.getElementById('imagePreviewSection').style.display = 'block';
            document.getElementById('editCategoryId').value = categoryId;

            const imageSrc = category.image
                ? `${STOREFRONT_URL}/assets/${category.image}`
                : `${STOREFRONT_URL}/assets/images/no-image.svg`;
            document.getElementById('modalImagePreview').src = imageSrc;

            // Auto-fill label with category name if empty
            if (!document.getElementById('labelInput').value) {
                document.getElementById('labelInput').value = category.name;
            }
        }
    } else {
        document.getElementById('imagePreviewSection').style.display = 'none';
    }
}

function previewModalImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('modalImagePreview').src = e.target.result;
            document.getElementById('newImagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function filterCategoryDropdown() {
    const select = document.getElementById('categorySelect');
    const featuredIds = featuredCategories.map(fc => fc.category_id);

    Array.from(select.options).forEach(option => {
        if (option.value && featuredIds.includes(parseInt(option.value))) {
            option.disabled = true;
            option.textContent += ' (already featured)';
        } else {
            option.disabled = false;
        }
    });
}

function editCategory(id) {
    const fc = featuredCategories.find(c => c.id === id);
    if (!fc) return;

    document.getElementById('modalTitle').textContent = 'Edit Featured Category';
    document.getElementById('editId').value = id;
    document.getElementById('editCategoryId').value = fc.category_id;
    document.getElementById('categorySelect').value = fc.category_id;
    document.getElementById('categorySelect').disabled = true; // Can't change category when editing
    document.getElementById('labelInput').value = fc.label;
    document.getElementById('descriptionInput').value = fc.description || '';

    // Show image preview
    document.getElementById('imagePreviewSection').style.display = 'block';
    const imageSrc = fc.category_image
        ? `${STOREFRONT_URL}/assets/${fc.category_image}`
        : `${STOREFRONT_URL}/assets/images/no-image.svg`;
    document.getElementById('modalImagePreview').src = imageSrc;
    document.getElementById('modalImageFile').value = '';
    document.getElementById('newImagePreview').style.display = 'none';

    categoryModal.show();
}

async function saveCategory() {
    const editId = document.getElementById('editId').value;
    const categoryId = document.getElementById('categorySelect').value;
    const editCategoryId = document.getElementById('editCategoryId').value;
    const label = document.getElementById('labelInput').value.trim();
    const description = document.getElementById('descriptionInput').value.trim();
    const imageFile = document.getElementById('modalImageFile').files[0];

    if (!editId && !categoryId) {
        showToast('Please select a category', 'error');
        return;
    }

    if (!label) {
        showToast('Please enter a label', 'error');
        return;
    }

    try {
        const url = editId
            ? `${API_BASE}/admin/featured-categories/${editId}`
            : `${API_BASE}/admin/featured-categories`;

        const method = editId ? 'PUT' : 'POST';

        const body = editId
            ? { label, description }
            : { category_id: parseInt(categoryId), label, description };

        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            // If a new image was selected, upload it
            if (imageFile) {
                const targetCategoryId = editCategoryId || categoryId;
                await uploadCategoryImage(targetCategoryId, imageFile);
            }

            showToast(data.message, 'success');
            categoryModal.hide();
            loadFeaturedCategories();
        } else {
            showToast(data.message || 'Error saving category', 'error');
        }
    } catch (error) {
        console.error('Error saving category:', error);
        showToast('Error saving category', 'error');
    }
}

async function uploadCategoryImage(categoryId, file) {
    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories/upload-image/${categoryId}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('Image uploaded successfully', 'success');
        } else {
            showToast(data.message || 'Error uploading image', 'error');
        }
    } catch (error) {
        console.error('Error uploading image:', error);
        showToast('Error uploading image', 'error');
    }
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to remove this featured category?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            showToast('Featured category removed', 'success');
            loadFeaturedCategories();
        } else {
            showToast(data.message || 'Error removing category', 'error');
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        showToast('Error removing category', 'error');
    }
}

async function saveOrder() {
    const cards = document.querySelectorAll('#featuredList .featured-card');
    const order = Array.from(cards).map(card => parseInt(card.dataset.id));

    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Order updated', 'success');
        }
    } catch (error) {
        console.error('Error saving order:', error);
    }
}

async function updateVisibility(isVisible) {
    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories/visibility`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_visible: isVisible })
        });

        const data = await response.json();

        if (data.success) {
            showToast(`Featured Categories section ${isVisible ? 'enabled' : 'disabled'}`, 'success');
        }
    } catch (error) {
        console.error('Error updating visibility:', error);
    }
}

function showImageModal(categoryId, currentSrc) {
    document.getElementById('imageCategoryId').value = categoryId;
    document.getElementById('currentImage').src = currentSrc;
    document.getElementById('imageFile').value = '';
    imageModal.show();
}

async function uploadImage() {
    const categoryId = document.getElementById('imageCategoryId').value;
    const fileInput = document.getElementById('imageFile');

    if (!fileInput.files.length) {
        showToast('Please select an image', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);

    try {
        const response = await fetch(`${API_BASE}/admin/featured-categories/upload-image/${categoryId}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('Image uploaded successfully', 'success');
            imageModal.hide();
            loadFeaturedCategories();
        } else {
            showToast(data.message || 'Error uploading image', 'error');
        }
    } catch (error) {
        console.error('Error uploading image:', error);
        showToast('Error uploading image', 'error');
    }
}

function showToast(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
