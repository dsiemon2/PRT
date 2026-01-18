@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="page-header">
    <h1>Categories Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
</div>

<!-- Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" id="searchCategories" placeholder="Search categories...">
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus"></i> Add Category
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="admin-table">
    <table class="table table-hover" id="categoriesTable">
        <thead>
            <tr>
                <th>Order</th>
                <th>Image</th>
                <th>Name</th>
                <th>Code</th>
                <th>Level</th>
                <th>Products</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="categoriesTableBody">
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading categories...
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm" class="admin-form">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="addCatName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Code *</label>
                        <input type="text" class="form-control" id="addCatCode" required placeholder="e.g., MENS-BOOTS">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" id="addCatParent">
                            <option value="">None (Top Level)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <input type="number" class="form-control" id="addCatLevel" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="addCatOrder" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <input type="file" class="form-control" id="addCatImage" accept="image/*">
                        <small class="text-muted">Recommended size: 800x400px</small>
                    </div>
                    <div id="addImagePreview" class="mt-2" style="display: none;">
                        <img id="addPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" id="createCategoryBtn">Create Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm" class="admin-form">
                    <input type="hidden" id="editCatCode">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="editCatName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" id="editCatParent">
                            <option value="">None (Top Level)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <input type="number" class="form-control" id="editCatLevel" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="editCatOrder" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <div id="editCurrentImage" class="mb-2" style="display: none;">
                            <p class="text-muted small mb-1">Current Image:</p>
                            <img id="editCurrentImg" src="" alt="Current" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                        <input type="file" class="form-control" id="editCatImage" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image. Recommended size: 800x400px</small>
                    </div>
                    <div id="editImagePreview" class="mt-2" style="display: none;">
                        <p class="text-muted small mb-1">New Image Preview:</p>
                        <img id="editPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" id="updateCategoryBtn">Update Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCategoryName"></strong>?</p>
                <p class="text-muted small mb-0">This will not delete products in this category, but they will become uncategorized.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCategory">Delete Category</button>
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
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
const STOREFRONT_URL = '{{ rtrim(env("STOREFRONT_URL", "http://localhost:8300"), "/") }}';
let categories = [];
let allParentCategories = [];
let currentPage = 1;
let perPage = 15;
let currentDeleteCode = null;

document.addEventListener('DOMContentLoaded', function() {
    loadCategories(1);

    // Search categories with debounce
    let searchTimeout;
    document.getElementById('searchCategories').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadCategories(1), 300);
    });

    // Image preview for Add modal
    document.getElementById('addCatImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('addPreviewImg').src = e.target.result;
                document.getElementById('addImagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('addImagePreview').style.display = 'none';
        }
    });

    // Image preview for Edit modal
    document.getElementById('editCatImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editPreviewImg').src = e.target.result;
                document.getElementById('editImagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('editImagePreview').style.display = 'none';
        }
    });
});

async function loadCategories(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('categoriesTableBody');
    tbody.innerHTML = `<tr><td colspan="8" class="text-center">
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div> Loading categories...
    </td></tr>`;

    try {
        const search = document.getElementById('searchCategories').value;
        let url = `${API_BASE}/admin/categories?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            categories = data.data;
            renderCategories();
            renderPagination(data.meta);
            loadParentCategories();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error loading categories</td></tr>`;
    }
}

async function loadParentCategories() {
    try {
        const response = await fetch(`${API_BASE}/admin/categories`);
        const data = await response.json();
        if (data.success) {
            allParentCategories = data.data.filter(cat => !cat.IsBottom);
            populateParentDropdowns();
        }
    } catch (error) {
        console.error('Error loading parent categories:', error);
    }
}

function populateParentDropdowns() {
    const addSelect = document.getElementById('addCatParent');
    const editSelect = document.getElementById('editCatParent');

    const options = '<option value="">None (Top Level)</option>' +
        allParentCategories.map(cat =>
            `<option value="${cat.CategoryCode}">${cat.Category}</option>`
        ).join('');

    addSelect.innerHTML = options;
    editSelect.innerHTML = options;
}

function renderCategories() {
    const tbody = document.getElementById('categoriesTableBody');

    if (categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No categories found</td></tr>';
        return;
    }

    tbody.innerHTML = categories.map((cat, index) => {
        const level = cat.Level || 0;
        const indent = level > 0 ? 'ps-' + Math.min(level * 3, 5) : '';
        const isParent = !cat.IsBottom;

        return `
            <tr data-code="${cat.CategoryCode}" onclick="highlightRow(event)" style="cursor: pointer;">
                <td><i class="bi bi-grip-vertical text-muted" style="cursor: grab;"></i> ${cat.sOrder || index + 1}</td>
                <td>
                    ${cat.image ?
                        `<img src="${STOREFRONT_URL}/assets/${cat.image}"
                             alt="${cat.Category}"
                             class="rounded"
                             style="width: 10%; height: 10%; object-fit: cover;"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'bg-light rounded p-2 d-flex align-items-center justify-content-center\\' style=\\'width:100px;height:50px;\\'><i class=\\'bi bi-image\\'></i></div>';">` :
                        `<div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 50px;">
                            <i class="bi bi-image"></i>
                        </div>`
                    }
                </td>
                <td class="${indent}">
                    ${level > 0 ? '— ' : ''}
                    ${isParent ? '<strong>' : ''}${cat.Category}${isParent ? '</strong>' : ''}
                </td>
                <td>${cat.CategoryCode}</td>
                <td>${level}</td>
                <td>${cat.products_count || 0}</td>
                <td><span class="status-badge active">Active</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editCategory('${cat.CategoryCode}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="showDeleteModal('${cat.CategoryCode}', '${cat.Category.replace(/'/g, "\\'")}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function renderPagination(meta) {
    const paginationInfo = document.getElementById('paginationInfo');
    const pagination = document.getElementById('pagination');

    if (!meta || meta.total === 0) {
        paginationInfo.textContent = '';
        pagination.innerHTML = '';
        return;
    }

    paginationInfo.textContent = `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total} entries`;

    let html = '';

    // Previous button
    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCategories(${meta.current_page - 1}); return false;">Previous</a>
    </li>`;

    // Page numbers
    const totalPages = meta.last_page;
    const current = meta.current_page;

    let startPage = Math.max(1, current - 2);
    let endPage = Math.min(totalPages, current + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadCategories(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === current ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadCategories(${i}); return false;">${i}</a>
        </li>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadCategories(${totalPages}); return false;">${totalPages}</a></li>`;
    }

    // Next button
    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadCategories(${meta.current_page + 1}); return false;">Next</a>
    </li>`;

    pagination.innerHTML = html;
}

function editCategory(code) {
    const cat = categories.find(c => c.CategoryCode == code);
    if (cat) {
        document.getElementById('editCatCode').value = cat.CategoryCode;
        document.getElementById('editCatName').value = cat.Category;
        document.getElementById('editCatParent').value = cat.ParentCode || '';
        document.getElementById('editCatLevel').value = cat.Level || 0;
        document.getElementById('editCatOrder').value = cat.sOrder || 0;

        // Show current image if exists
        if (cat.image) {
            document.getElementById('editCurrentImg').src = `${STOREFRONT_URL}/assets/${cat.image}`;
            document.getElementById('editCurrentImage').style.display = 'block';
        } else {
            document.getElementById('editCurrentImage').style.display = 'none';
        }

        // Reset file input and preview
        document.getElementById('editCatImage').value = '';
        document.getElementById('editImagePreview').style.display = 'none';

        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    }
}

function showDeleteModal(code, name) {
    currentDeleteCode = code;
    document.getElementById('deleteCategoryName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}

// Create category
document.getElementById('createCategoryBtn').addEventListener('click', async function() {
    const name = document.getElementById('addCatName').value;
    const code = document.getElementById('addCatCode').value;

    if (!name || !code) {
        alert('Please fill in required fields (Name, Code)');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating...';

    try {
        const formData = new FormData();
        formData.append('CategoryCode', code);
        formData.append('Category', name);
        formData.append('ParentCode', document.getElementById('addCatParent').value || '');
        formData.append('Level', parseInt(document.getElementById('addCatLevel').value) || 0);
        formData.append('sOrder', parseInt(document.getElementById('addCatOrder').value) || 0);

        // Add image if selected
        const imageFile = document.getElementById('addCatImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        const response = await fetch(`${API_BASE}/admin/categories`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
            document.getElementById('addCategoryForm').reset();
            document.getElementById('addImagePreview').style.display = 'none';
            loadCategories(1);
        } else {
            alert('Error: ' + (result.message || 'Failed to create category'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = 'Create Category';
});

// Update category
document.getElementById('updateCategoryBtn').addEventListener('click', async function() {
    const code = document.getElementById('editCatCode').value;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';

    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('Category', document.getElementById('editCatName').value);
        formData.append('ParentCode', document.getElementById('editCatParent').value || '');
        formData.append('Level', parseInt(document.getElementById('editCatLevel').value) || 0);
        formData.append('sOrder', parseInt(document.getElementById('editCatOrder').value) || 0);

        // Add image if a new one is selected
        const imageFile = document.getElementById('editCatImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        const response = await fetch(`${API_BASE}/admin/categories/${code}`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
            loadCategories(currentPage);
        } else {
            alert('Error: ' + (result.message || 'Failed to update category'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = 'Update Category';
});

// Confirm delete
document.getElementById('confirmDeleteCategory').addEventListener('click', async function() {
    if (!currentDeleteCode) return;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        const response = await fetch(`${API_BASE}/admin/categories/${currentDeleteCode}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal')).hide();
            loadCategories(currentPage);
        } else {
            alert('Error: ' + (result.message || 'Failed to delete category'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = 'Delete Category';
    currentDeleteCode = null;
});
</script>
@endpush
