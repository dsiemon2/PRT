@extends('layouts.admin')

@section('title', 'Specialty Products Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Specialty Products Management</h1>
            <p class="text-muted mb-0">Manage specialty categories and their products</p>
        </div>
        <button class="btn btn-primary" onclick="showAddCategoryModal()">
            <i class="bi bi-plus-lg"></i> Add Category
        </button>
    </div>

    <!-- Visibility Toggle -->
    <div class="visibility-toggle d-flex align-items-center gap-3 p-3 bg-light rounded mb-4">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="visibilityToggle" style="width: 3em; height: 1.5em;">
            <label class="form-check-label fw-bold" for="visibilityToggle">Show Specialty Products Section</label>
        </div>
        <span class="text-muted small">When disabled, the Specialty Products section will be hidden on the frontend</span>
    </div>
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading specialty categories...</p>
    </div>

    <!-- Categories Grid -->
    <div id="categoriesGrid" class="row g-4" style="display: none;">
        <!-- Categories will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="bi bi-star display-1 text-muted"></i>
        <h4 class="mt-3">No Specialty Categories</h4>
        <p class="text-muted">Add your first specialty category to get started.</p>
        <button class="btn btn-primary" onclick="showAddCategoryModal()">
            <i class="bi bi-plus-lg"></i> Add Category
        </button>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Add Specialty Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="categoryLabel" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Link to Existing Category (Optional)</label>
                                <select class="form-select" id="categoryLinkId">
                                    <option value="">-- No Link (Custom Category) --</option>
                                </select>
                                <small class="text-muted">Link to pull products from existing category</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div id="categoryImagePreview" class="border rounded p-3 text-center" style="min-height: 150px;">
                                    <i class="bi bi-image display-4 text-muted"></i>
                                    <p class="small text-muted mb-0">No image</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <input type="file" class="form-control" id="categoryImageFile" accept="image/*">
                                <small class="text-muted">Recommended: 400x300px, JPG/PNG</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">
                    <i class="bi bi-check-lg"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Products Modal -->
<div class="modal fade" id="productsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span id="productsModalCategoryName">Category</span> - Products
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add Product Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Add Product</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="productSearchInput" placeholder="Search by UPC, name, or item number...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="searchProducts()">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-primary w-100" onclick="showCustomProductForm()">
                                    <i class="bi bi-plus"></i> Add Custom Product
                                </button>
                            </div>
                        </div>
                        <!-- Search Results -->
                        <div id="productSearchResults" class="mt-3" style="display: none;">
                            <div class="list-group" id="productSearchList"></div>
                        </div>
                    </div>
                </div>

                <!-- Current Products -->
                <h6><i class="bi bi-box-seam"></i> Current Products</h6>
                <div id="currentProductsLoading" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span class="ms-2">Loading products...</span>
                </div>
                <div id="currentProductsGrid" class="row g-3" style="display: none;"></div>
                <div id="noProductsMessage" class="text-center py-4 text-muted" style="display: none;">
                    <i class="bi bi-inbox display-4"></i>
                    <p class="mt-2">No products in this category yet.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="editProductId">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editProductLabel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editProductDescription" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sizes (comma-separated)</label>
                                <input type="text" class="form-control" id="editProductSizes" placeholder="S,M,L,XL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Colors (comma-separated)</label>
                                <input type="text" class="form-control" id="editProductColors" placeholder="Black,Brown,Tan">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Override Price (leave empty to use product price)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="editProductPrice" step="0.01" min="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveProductEdit()">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Custom Product Modal -->
<div class="modal fade" id="customProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Custom Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customProductForm">
                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customProductLabel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="customProductDescription" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="customProductPrice" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sizes (comma-separated)</label>
                                <input type="text" class="form-control" id="customProductSizes" placeholder="S,M,L,XL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Colors (comma-separated)</label>
                                <input type="text" class="form-control" id="customProductColors" placeholder="Black,Brown,Tan">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomProduct()">
                    <i class="bi bi-check-lg"></i> Add Product
                </button>
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
let categories = [];
let currentCategoryId = null;
let categoryModal, productsModal, editProductModal, customProductModal;

document.addEventListener('DOMContentLoaded', function() {
    categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    productsModal = new bootstrap.Modal(document.getElementById('productsModal'));
    editProductModal = new bootstrap.Modal(document.getElementById('editProductModal'));
    customProductModal = new bootstrap.Modal(document.getElementById('customProductModal'));

    loadCategories();
    loadExistingCategories();
    loadVisibilitySetting();

    // Visibility toggle
    document.getElementById('visibilityToggle').addEventListener('change', function() {
        updateVisibility(this.checked);
    });

    // Enter key for product search
    document.getElementById('productSearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchProducts();
        }
    });
});

async function loadVisibilitySetting() {
    try {
        const response = await fetch(`${API_BASE}/admin/settings/features`);
        const data = await response.json();

        if (data.success && data.data) {
            const isVisible = data.data.specialty_products_enabled === true ||
                              data.data.specialty_products_enabled === '1' ||
                              data.data.specialty_products_enabled === 'true' ||
                              data.data.specialty_products_enabled === 1;
            document.getElementById('visibilityToggle').checked = isVisible;
        }
    } catch (error) {
        console.error('Error loading visibility setting:', error);
    }
}

async function updateVisibility(isVisible) {
    try {
        const response = await fetch(`${API_BASE}/admin/settings/features`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ specialty_products_enabled: isVisible })
        });

        const data = await response.json();

        if (data.success) {
            showToast(`Specialty Products section ${isVisible ? 'enabled' : 'disabled'}`, 'success');
        } else {
            showToast('Failed to update visibility', 'danger');
        }
    } catch (error) {
        console.error('Error updating visibility:', error);
        showToast('Error updating visibility', 'danger');
    }
}

async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE}/admin/specialty-categories`);
        const data = await response.json();

        document.getElementById('loadingState').style.display = 'none';

        if (data.success && data.data.length > 0) {
            categories = data.data;
            renderCategories();
            document.getElementById('categoriesGrid').style.display = 'flex';
        } else {
            document.getElementById('emptyState').style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        document.getElementById('loadingState').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> Failed to load categories. Please try again.
            </div>
        `;
    }
}

async function loadExistingCategories() {
    try {
        const response = await fetch(`${API_BASE}/categories`);
        const data = await response.json();

        if (data.success) {
            const select = document.getElementById('categoryLinkId');
            // Filter for special categories (100+) or allow any
            data.data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.CategoryCode;
                option.textContent = `${cat.Category} (ID: ${cat.CategoryCode})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading existing categories:', error);
    }
}

const NO_IMAGE_PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='200' viewBox='0 0 300 200'%3E%3Crect fill='%23f8f9fa' width='300' height='200'/%3E%3Ctext x='150' y='100' font-family='Arial' font-size='14' fill='%236c757d' text-anchor='middle' dominant-baseline='middle'%3ENo Image%3C/text%3E%3C/svg%3E";

function renderCategories() {
    const grid = document.getElementById('categoriesGrid');
    grid.innerHTML = '';

    categories.forEach((cat, index) => {
        const imageSrc = cat.image ? `${STOREFRONT_URL}/${cat.image}` : NO_IMAGE_PLACEHOLDER;

        const card = document.createElement('div');
        card.className = 'col-md-6 col-lg-3';
        card.innerHTML = `
            <div class="card h-100 specialty-category-card" data-id="${cat.id}">
                <div class="position-relative">
                    <img src="${imageSrc}" class="card-img-top" alt="${cat.label}" style="height: 180px; object-fit: cover;" onerror="this.src='${NO_IMAGE_PLACEHOLDER}'">
                    ${!cat.is_visible ? '<span class="badge bg-secondary position-absolute top-0 end-0 m-2">Hidden</span>' : ''}
                </div>
                <div class="card-body">
                    <h5 class="card-title">${cat.label}</h5>
                    <p class="card-text text-muted small" style="min-height: 60px;">${cat.description || 'No description'}</p>
                    <p class="mb-2">
                        <span class="badge bg-primary">${cat.products_count} Products</span>
                        ${cat.category_id ? `<span class="badge bg-info">Linked: ${cat.category_id}</span>` : ''}
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100">
                        <button class="btn btn-sm btn-outline-primary" onclick="showProductsModal(${cat.id}, '${cat.label.replace(/'/g, "\\'")}')">
                            <i class="bi bi-box-seam"></i> Products
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="editCategory(${cat.id})">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${cat.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });

    // Initialize sortable
    new Sortable(grid, {
        animation: 150,
        handle: '.card',
        onEnd: function(evt) {
            reorderCategories();
        }
    });
}

function showAddCategoryModal() {
    document.getElementById('categoryModalTitle').textContent = 'Add Specialty Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryImagePreview').innerHTML = `
        <i class="bi bi-image display-4 text-muted"></i>
        <p class="small text-muted mb-0">No image</p>
    `;
    categoryModal.show();
}

function editCategory(id) {
    const cat = categories.find(c => c.id === id);
    if (!cat) return;

    document.getElementById('categoryModalTitle').textContent = 'Edit Specialty Category';
    document.getElementById('categoryId').value = cat.id;
    document.getElementById('categoryLabel').value = cat.label;
    document.getElementById('categoryDescription').value = cat.description || '';
    document.getElementById('categoryLinkId').value = cat.category_id || '';

    if (cat.image) {
        document.getElementById('categoryImagePreview').innerHTML = `
            <img src="${STOREFRONT_URL}/${cat.image}" class="img-fluid rounded" style="max-height: 140px;" onerror="this.parentElement.innerHTML='<i class=\\'bi bi-image display-4 text-muted\\'></i><p class=\\'small text-muted mb-0\\'>No image</p>'">
        `;
    } else {
        document.getElementById('categoryImagePreview').innerHTML = `
            <i class="bi bi-image display-4 text-muted"></i>
            <p class="small text-muted mb-0">No image</p>
        `;
    }

    categoryModal.show();
}

async function saveCategory() {
    const id = document.getElementById('categoryId').value;
    const label = document.getElementById('categoryLabel').value.trim();
    const description = document.getElementById('categoryDescription').value.trim();
    const categoryId = document.getElementById('categoryLinkId').value || null;
    const imageFile = document.getElementById('categoryImageFile').files[0];

    if (!label) {
        alert('Category name is required');
        return;
    }

    try {
        let url = `${API_BASE}/admin/specialty-categories`;
        let method = 'POST';
        let body = { label, description, category_id: categoryId };

        if (id) {
            url += `/${id}`;
            method = 'PUT';
        }

        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            // Upload image if provided
            if (imageFile) {
                const catId = id || data.data.id;
                const formData = new FormData();
                formData.append('image', imageFile);

                await fetch(`${API_BASE}/admin/specialty-categories/${catId}/image`, {
                    method: 'POST',
                    body: formData
                });
            }

            categoryModal.hide();
            loadCategories();
        } else {
            alert('Error: ' + (data.message || 'Failed to save category'));
        }
    } catch (error) {
        console.error('Error saving category:', error);
        alert('Failed to save category');
    }
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category and all its products?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/specialty-categories/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            loadCategories();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete category'));
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        alert('Failed to delete category');
    }
}

async function reorderCategories() {
    const cards = document.querySelectorAll('.specialty-category-card');
    const order = Array.from(cards).map(card => parseInt(card.dataset.id));

    try {
        await fetch(`${API_BASE}/admin/specialty-categories/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });
    } catch (error) {
        console.error('Error reordering categories:', error);
    }
}

// ==================== PRODUCTS ====================

async function showProductsModal(categoryId, categoryName) {
    currentCategoryId = categoryId;
    document.getElementById('productsModalCategoryName').textContent = categoryName;
    document.getElementById('productSearchInput').value = '';
    document.getElementById('productSearchResults').style.display = 'none';

    productsModal.show();
    loadCategoryProducts(categoryId);
}

async function loadCategoryProducts(categoryId) {
    document.getElementById('currentProductsLoading').style.display = 'block';
    document.getElementById('currentProductsGrid').style.display = 'none';
    document.getElementById('noProductsMessage').style.display = 'none';

    try {
        const response = await fetch(`${API_BASE}/admin/specialty-categories/${categoryId}/products`);
        const data = await response.json();

        document.getElementById('currentProductsLoading').style.display = 'none';

        if (data.success && data.data.length > 0) {
            renderCategoryProducts(data.data);
            document.getElementById('currentProductsGrid').style.display = 'flex';
        } else {
            document.getElementById('noProductsMessage').style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('currentProductsLoading').innerHTML = '<div class="alert alert-danger">Failed to load products</div>';
    }
}

function renderCategoryProducts(products) {
    const grid = document.getElementById('currentProductsGrid');
    grid.innerHTML = '';

    products.forEach(product => {
        const imageSrc = product.product_image ? `${STOREFRONT_URL}/assets/${product.product_image}` : NO_IMAGE_PLACEHOLDER;
        const price = product.product_price ? `$${parseFloat(product.product_price).toFixed(2)}` : 'N/A';

        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-3';
        col.innerHTML = `
            <div class="card h-100 product-card" data-id="${product.id}">
                <img src="${imageSrc}" class="card-img-top" alt="${product.label}" style="height: 120px; object-fit: contain; background: #f8f9fa;" onerror="this.src='${NO_IMAGE_PLACEHOLDER}'">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1" style="font-size: 0.85rem;">${product.label}</h6>
                    <p class="text-primary fw-bold mb-1">${price}</p>
                    ${product.sizes ? `<small class="text-muted d-block">Sizes: ${product.sizes}</small>` : ''}
                    ${product.colors ? `<small class="text-muted d-block">Colors: ${product.colors}</small>` : ''}
                </div>
                <div class="card-footer bg-transparent p-2">
                    <div class="btn-group btn-group-sm w-100">
                        <button class="btn btn-outline-secondary" onclick="editProduct(${product.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="removeProduct(${product.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.appendChild(col);
    });
}

async function searchProducts() {
    const query = document.getElementById('productSearchInput').value.trim();
    if (query.length < 2) {
        alert('Please enter at least 2 characters to search');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/specialty-products/search?q=${encodeURIComponent(query)}`);
        const data = await response.json();

        const resultsDiv = document.getElementById('productSearchResults');
        const listDiv = document.getElementById('productSearchList');

        if (data.success && data.data.length > 0) {
            listDiv.innerHTML = '';
            data.data.forEach(product => {
                const imageSrc = product.Image ? `${STOREFRONT_URL}/assets/${product.Image}` : '';
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action d-flex align-items-center';
                item.innerHTML = `
                    ${imageSrc ? `<img src="${imageSrc}" class="me-3" style="width: 50px; height: 50px; object-fit: contain;" onerror="this.style.display='none'">` : ''}
                    <div class="flex-grow-1">
                        <strong>${product.ShortDescription || product.ItemNumber}</strong>
                        <br><small class="text-muted">UPC: ${product.UPC} | $${parseFloat(product.UnitPrice || 0).toFixed(2)}</small>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="addProductToCategory('${product.UPC}', '${(product.ShortDescription || '').replace(/'/g, "\\'")}', '${product.LngDescription ? product.LngDescription.substring(0, 200).replace(/'/g, "\\'") : ''}', '${product.ItemSize || ''}')">
                        <i class="bi bi-plus"></i> Add
                    </button>
                `;
                listDiv.appendChild(item);
            });
            resultsDiv.style.display = 'block';
        } else {
            listDiv.innerHTML = '<div class="list-group-item text-muted">No products found</div>';
            resultsDiv.style.display = 'block';
        }
    } catch (error) {
        console.error('Error searching products:', error);
        alert('Failed to search products');
    }
}

async function addProductToCategory(upc, label, description, sizes) {
    try {
        const response = await fetch(`${API_BASE}/admin/specialty-categories/${currentCategoryId}/products`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ upc, label, description, sizes })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('productSearchResults').style.display = 'none';
            document.getElementById('productSearchInput').value = '';
            loadCategoryProducts(currentCategoryId);
            loadCategories(); // Refresh counts
        } else {
            alert('Error: ' + (data.message || 'Failed to add product'));
        }
    } catch (error) {
        console.error('Error adding product:', error);
        alert('Failed to add product');
    }
}

function showCustomProductForm() {
    document.getElementById('customProductForm').reset();
    customProductModal.show();
}

async function saveCustomProduct() {
    const label = document.getElementById('customProductLabel').value.trim();
    const description = document.getElementById('customProductDescription').value.trim();
    const price = document.getElementById('customProductPrice').value;
    const sizes = document.getElementById('customProductSizes').value.trim();
    const colors = document.getElementById('customProductColors').value.trim();

    if (!label || !price) {
        alert('Product name and price are required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/specialty-categories/${currentCategoryId}/products`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ label, description, price: parseFloat(price), sizes, colors })
        });

        const data = await response.json();

        if (data.success) {
            customProductModal.hide();
            loadCategoryProducts(currentCategoryId);
            loadCategories();
        } else {
            alert('Error: ' + (data.message || 'Failed to add product'));
        }
    } catch (error) {
        console.error('Error adding custom product:', error);
        alert('Failed to add product');
    }
}

async function editProduct(productId) {
    try {
        // Find product in current loaded data
        const response = await fetch(`${API_BASE}/admin/specialty-categories/${currentCategoryId}/products`);
        const data = await response.json();

        if (data.success) {
            const product = data.data.find(p => p.id === productId);
            if (product) {
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editProductLabel').value = product.label;
                document.getElementById('editProductDescription').value = product.description || '';
                document.getElementById('editProductSizes').value = product.sizes || '';
                document.getElementById('editProductColors').value = product.colors || '';
                document.getElementById('editProductPrice').value = product.price || '';

                editProductModal.show();
            }
        }
    } catch (error) {
        console.error('Error loading product:', error);
        alert('Failed to load product details');
    }
}

async function saveProductEdit() {
    const id = document.getElementById('editProductId').value;
    const label = document.getElementById('editProductLabel').value.trim();
    const description = document.getElementById('editProductDescription').value.trim();
    const sizes = document.getElementById('editProductSizes').value.trim();
    const colors = document.getElementById('editProductColors').value.trim();
    const price = document.getElementById('editProductPrice').value;

    if (!label) {
        alert('Product name is required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/specialty-products/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                label,
                description,
                sizes,
                colors,
                price: price ? parseFloat(price) : null
            })
        });

        const data = await response.json();

        if (data.success) {
            editProductModal.hide();
            loadCategoryProducts(currentCategoryId);
        } else {
            alert('Error: ' + (data.message || 'Failed to update product'));
        }
    } catch (error) {
        console.error('Error updating product:', error);
        alert('Failed to update product');
    }
}

async function removeProduct(productId) {
    if (!confirm('Remove this product from the category?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/specialty-products/${productId}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            loadCategoryProducts(currentCategoryId);
            loadCategories();
        } else {
            alert('Error: ' + (data.message || 'Failed to remove product'));
        }
    } catch (error) {
        console.error('Error removing product:', error);
        alert('Failed to remove product');
    }
}

function showToast(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' || type === 'danger' ? 'alert-danger' : 'alert-info';
    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<style>
.specialty-category-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: move;
}
.specialty-category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.product-card {
    transition: transform 0.2s;
}
.product-card:hover {
    transform: translateY(-3px);
}
</style>
@endpush
