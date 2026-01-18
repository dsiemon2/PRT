@extends('layouts.admin')

@section('title', 'Featured Products')

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
.product-count {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #6c757d;
}
.price-tag {
    background: #198754;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}
.sale-price {
    background: #dc3545;
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
.section-title-input {
    max-width: 300px;
}
.product-search-results {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    display: none;
}
.product-search-results.show {
    display: block;
}
.product-search-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 10px;
}
.product-search-item:hover {
    background: #f8f9fa;
}
.product-search-item img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}
.product-search-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f0f0f0;
}
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Featured Products</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Featured Products</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Visibility Toggle and Section Title -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="visibility-toggle">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="visibilityToggle">
                <label class="form-check-label fw-bold" for="visibilityToggle">Show Featured Products Section</label>
            </div>
            <span class="text-muted small">When disabled, the Featured Products section will be hidden</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="visibility-toggle">
            <label class="form-label fw-bold mb-0 me-2">Section Title:</label>
            <input type="text" class="form-control section-title-input" id="sectionTitle" value="Featured Products">
            <button class="btn btn-sm btn-outline-primary ms-2" onclick="updateSectionTitle()">
                <i class="bi bi-check"></i>
            </button>
        </div>
    </div>
</div>

<!-- Featured Products Grid -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Featured Products</h5>
        <span class="badge bg-secondary" id="productCount">0 / 9</span>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Drag and drop to reorder. Maximum 9 products (3 per row).</p>

        <div id="featuredList">
            <!-- Featured products will be loaded here -->
        </div>

        <!-- Add New Button -->
        <div class="add-card" id="addNewBtn" onclick="showAddModal()">
            <i class="bi bi-plus-circle"></i>
            <div class="mt-2 text-muted">Add Featured Product</div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Featured Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <input type="hidden" id="editUpc">

                <!-- Product Image Preview -->
                <div class="mb-3" id="imagePreviewSection" style="display: none;">
                    <label class="form-label">Product Image</label>
                    <div class="d-flex align-items-start gap-3">
                        <div class="image-container" style="width: 120px; height: 120px;">
                            <img id="modalImagePreview" src="" class="card-image" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                            <div class="image-upload-overlay" onclick="document.getElementById('modalImageFile').click()">
                                <i class="bi bi-camera text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" class="form-control form-control-sm" id="modalImageFile" accept="image/*" onchange="previewModalImage(this)">
                            <small class="text-muted d-block mt-1">Click image or choose file to change.</small>
                            <div id="newImagePreview" class="mt-2" style="display: none;">
                                <span class="badge bg-success"><i class="bi bi-check"></i> New image selected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Search (for adding new) -->
                <div class="mb-3" id="productSearchSection">
                    <label class="form-label">Search Product</label>
                    <input type="text" class="form-control" id="productSearch" placeholder="Search by UPC, name, or description..." oninput="searchProducts(this.value)">
                    <div id="productSearchResults" class="product-search-results mt-2"></div>
                </div>

                <!-- Selected Product Info -->
                <div class="mb-3" id="selectedProductInfo" style="display: none;">
                    <label class="form-label">Selected Product</label>
                    <div class="alert alert-info d-flex align-items-center gap-3">
                        <img id="selectedProductImage" src="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        <div>
                            <strong id="selectedProductName"></strong>
                            <div class="small text-muted">UPC: <span id="selectedProductUpc"></span></div>
                            <div class="small">Price: $<span id="selectedProductPrice"></span></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="clearSelectedProduct()" id="changeProductBtn">Change</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Label</label>
                    <input type="text" class="form-control" id="labelInput" placeholder="e.g., Best Seller">
                    <small class="text-muted">Display name shown on the card</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="descriptionInput" rows="3" placeholder="Short description for this product..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveProduct()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Product Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="imageProductUpc">

                <div class="text-center mb-3">
                    <img id="currentImage" src="" class="img-fluid rounded" style="max-height: 200px;">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload New Image</label>
                    <input type="file" class="form-control" id="imageFile" accept="image/*">
                    <small class="text-muted">Recommended: 400x400px, JPG or PNG, max 2MB</small>
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
let featuredProducts = [];
let allProducts = [];
let productModal, imageModal;
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    productModal = new bootstrap.Modal(document.getElementById('productModal'));
    imageModal = new bootstrap.Modal(document.getElementById('imageModal'));

    loadFeaturedProducts();
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

async function loadFeaturedProducts() {
    try {
        const response = await fetch(`${API_BASE}/admin/featured-products`);
        const data = await response.json();

        if (data.success) {
            featuredProducts = data.data.featured_products;
            document.getElementById('visibilityToggle').checked = data.data.is_visible;
            document.getElementById('sectionTitle').value = data.data.section_title || 'Featured Products';
            renderProducts();
            updateCount();
        }
    } catch (error) {
        console.error('Error loading featured products:', error);
    }
}

async function searchProducts(query) {
    if (searchTimeout) clearTimeout(searchTimeout);

    if (query.length < 2) {
        document.getElementById('productSearchResults').classList.remove('show');
        return;
    }

    searchTimeout = setTimeout(async () => {
        try {
            const response = await fetch(`${API_BASE}/admin/featured-products/products?search=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.success) {
                allProducts = data.data;
                renderSearchResults();
            }
        } catch (error) {
            console.error('Error searching products:', error);
        }
    }, 300);
}

function renderSearchResults() {
    const container = document.getElementById('productSearchResults');
    const featuredUpcs = featuredProducts.map(fp => fp.upc);

    if (allProducts.length === 0) {
        container.innerHTML = '<div class="p-3 text-muted text-center">No products found</div>';
        container.classList.add('show');
        return;
    }

    container.innerHTML = allProducts.map(p => {
        const isFeatured = featuredUpcs.includes(p.upc);
        const imageSrc = p.image
            ? `${STOREFRONT_URL}/assets/${p.image}`
            : `${STOREFRONT_URL}/assets/images/no-image.svg`;

        return `
            <div class="product-search-item ${isFeatured ? 'disabled' : ''}" onclick="${isFeatured ? '' : `selectProduct('${p.upc}')`}">
                <img src="${imageSrc}" alt="${p.name}">
                <div class="flex-grow-1">
                    <div class="fw-bold">${p.name}</div>
                    <div class="small text-muted">UPC: ${p.upc} | $${parseFloat(p.price).toFixed(2)}</div>
                </div>
                ${isFeatured ? '<span class="badge bg-secondary">Already Featured</span>' : ''}
            </div>
        `;
    }).join('');

    container.classList.add('show');
}

function selectProduct(upc) {
    const product = allProducts.find(p => p.upc === upc);
    if (!product) return;

    document.getElementById('editUpc').value = upc;
    document.getElementById('selectedProductName').textContent = product.name;
    document.getElementById('selectedProductUpc').textContent = upc;
    document.getElementById('selectedProductPrice').textContent = parseFloat(product.price).toFixed(2);

    const imageSrc = product.image
        ? `${STOREFRONT_URL}/assets/${product.image}`
        : `${STOREFRONT_URL}/assets/images/no-image.svg`;
    document.getElementById('selectedProductImage').src = imageSrc;
    document.getElementById('modalImagePreview').src = imageSrc;

    document.getElementById('productSearchSection').style.display = 'none';
    document.getElementById('selectedProductInfo').style.display = 'block';
    document.getElementById('imagePreviewSection').style.display = 'block';
    document.getElementById('productSearchResults').classList.remove('show');

    // Auto-fill label
    if (!document.getElementById('labelInput').value) {
        document.getElementById('labelInput').value = product.name;
    }
}

function clearSelectedProduct() {
    document.getElementById('editUpc').value = '';
    document.getElementById('productSearch').value = '';
    document.getElementById('productSearchSection').style.display = 'block';
    document.getElementById('selectedProductInfo').style.display = 'none';
    document.getElementById('imagePreviewSection').style.display = 'none';
    document.getElementById('productSearchResults').classList.remove('show');
}

function renderProducts() {
    const container = document.getElementById('featuredList');
    container.innerHTML = '';

    featuredProducts.forEach(fp => {
        const imageSrc = fp.product_image
            ? `${STOREFRONT_URL}/assets/${fp.product_image}`
            : `${STOREFRONT_URL}/assets/images/no-image.svg`;

        const price = parseFloat(fp.price);
        const salePrice = fp.sale_price ? parseFloat(fp.sale_price) : null;
        const displayPrice = salePrice && salePrice < price ? salePrice : price;
        const isOnSale = salePrice && salePrice < price;

        const card = document.createElement('div');
        card.className = 'featured-card d-flex align-items-center';
        card.dataset.id = fp.id;
        card.innerHTML = `
            <div class="drag-handle">
                <i class="bi bi-grip-vertical fs-4"></i>
            </div>
            <div class="image-container">
                <img src="${imageSrc}" class="card-image" alt="${fp.label}">
                <div class="image-upload-overlay" onclick="showImageModal('${fp.upc}', '${imageSrc}')">
                    <i class="bi bi-camera text-white fs-4"></i>
                </div>
            </div>
            <div class="card-content">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong>${fp.label}</strong>
                    <span class="price-tag ${isOnSale ? 'sale-price' : ''}">$${displayPrice.toFixed(2)}</span>
                    <span class="product-count">${fp.quantity} in stock</span>
                </div>
                <div class="text-muted small">${fp.description || 'No description'}</div>
                <div class="text-muted small">UPC: ${fp.upc}</div>
            </div>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editProduct(${fp.id})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${fp.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(card);
    });

    // Hide add button if max reached
    document.getElementById('addNewBtn').style.display = featuredProducts.length >= 9 ? 'none' : 'block';
}

function updateCount() {
    document.getElementById('productCount').textContent = `${featuredProducts.length} / 9`;
}

function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Featured Product';
    document.getElementById('editId').value = '';
    document.getElementById('editUpc').value = '';
    document.getElementById('productSearch').value = '';
    document.getElementById('labelInput').value = '';
    document.getElementById('descriptionInput').value = '';
    document.getElementById('productSearchSection').style.display = 'block';
    document.getElementById('selectedProductInfo').style.display = 'none';
    document.getElementById('imagePreviewSection').style.display = 'none';
    document.getElementById('changeProductBtn').style.display = 'block';
    document.getElementById('modalImageFile').value = '';
    document.getElementById('newImagePreview').style.display = 'none';
    document.getElementById('productSearchResults').classList.remove('show');

    productModal.show();
}

function editProduct(id) {
    const fp = featuredProducts.find(p => p.id === id);
    if (!fp) return;

    document.getElementById('modalTitle').textContent = 'Edit Featured Product';
    document.getElementById('editId').value = id;
    document.getElementById('editUpc').value = fp.upc;
    document.getElementById('labelInput').value = fp.label;
    document.getElementById('descriptionInput').value = fp.description || '';

    // Show selected product info but hide change button
    document.getElementById('selectedProductName').textContent = fp.product_name;
    document.getElementById('selectedProductUpc').textContent = fp.upc;
    document.getElementById('selectedProductPrice').textContent = parseFloat(fp.price).toFixed(2);

    const imageSrc = fp.product_image
        ? `${STOREFRONT_URL}/assets/${fp.product_image}`
        : `${STOREFRONT_URL}/assets/images/no-image.svg`;
    document.getElementById('selectedProductImage').src = imageSrc;
    document.getElementById('modalImagePreview').src = imageSrc;

    document.getElementById('productSearchSection').style.display = 'none';
    document.getElementById('selectedProductInfo').style.display = 'block';
    document.getElementById('imagePreviewSection').style.display = 'block';
    document.getElementById('changeProductBtn').style.display = 'none'; // Can't change product when editing
    document.getElementById('modalImageFile').value = '';
    document.getElementById('newImagePreview').style.display = 'none';

    productModal.show();
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

async function saveProduct() {
    const editId = document.getElementById('editId').value;
    const upc = document.getElementById('editUpc').value;
    const label = document.getElementById('labelInput').value.trim();
    const description = document.getElementById('descriptionInput').value.trim();
    const imageFile = document.getElementById('modalImageFile').files[0];

    if (!editId && !upc) {
        showToast('Please select a product', 'error');
        return;
    }

    if (!label) {
        showToast('Please enter a label', 'error');
        return;
    }

    try {
        const url = editId
            ? `${API_BASE}/admin/featured-products/${editId}`
            : `${API_BASE}/admin/featured-products`;

        const method = editId ? 'PUT' : 'POST';

        const body = editId
            ? { label, description }
            : { upc, label, description };

        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            // If a new image was selected, upload it
            if (imageFile && upc) {
                await uploadProductImage(upc, imageFile);
            }

            showToast(data.message, 'success');
            productModal.hide();
            loadFeaturedProducts();
        } else {
            showToast(data.message || 'Error saving product', 'error');
        }
    } catch (error) {
        console.error('Error saving product:', error);
        showToast('Error saving product', 'error');
    }
}

async function uploadProductImage(upc, file) {
    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch(`${API_BASE}/admin/featured-products/upload-image/${upc}`, {
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

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to remove this featured product?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/featured-products/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            showToast('Featured product removed', 'success');
            loadFeaturedProducts();
        } else {
            showToast(data.message || 'Error removing product', 'error');
        }
    } catch (error) {
        console.error('Error deleting product:', error);
        showToast('Error removing product', 'error');
    }
}

async function saveOrder() {
    const cards = document.querySelectorAll('#featuredList .featured-card');
    const order = Array.from(cards).map(card => parseInt(card.dataset.id));

    try {
        const response = await fetch(`${API_BASE}/admin/featured-products/reorder`, {
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
        const response = await fetch(`${API_BASE}/admin/featured-products/visibility`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_visible: isVisible })
        });

        const data = await response.json();

        if (data.success) {
            showToast(`Featured Products section ${isVisible ? 'enabled' : 'disabled'}`, 'success');
        }
    } catch (error) {
        console.error('Error updating visibility:', error);
    }
}

async function updateSectionTitle() {
    const title = document.getElementById('sectionTitle').value.trim();

    if (!title) {
        showToast('Please enter a section title', 'error');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/featured-products/title`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Section title updated', 'success');
        }
    } catch (error) {
        console.error('Error updating title:', error);
    }
}

function showImageModal(upc, currentSrc) {
    document.getElementById('imageProductUpc').value = upc;
    document.getElementById('currentImage').src = currentSrc;
    document.getElementById('imageFile').value = '';
    imageModal.show();
}

async function uploadImage() {
    const upc = document.getElementById('imageProductUpc').value;
    const fileInput = document.getElementById('imageFile');

    if (!fileInput.files.length) {
        showToast('Please select an image', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);

    try {
        const response = await fetch(`${API_BASE}/admin/featured-products/upload-image/${upc}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('Image uploaded successfully', 'success');
            imageModal.hide();
            loadFeaturedProducts();
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
