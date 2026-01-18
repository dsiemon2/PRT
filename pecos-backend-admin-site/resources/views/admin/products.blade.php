@extends('layouts.admin')

@section('title', 'Products Management')

@section('content')
<div class="page-header">
    <h1>Products Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol>
    </nav>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET">
            <div class="col-md-2">
                <input type="text" class="form-control" name="search" placeholder="Search by name, SKU..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category['CategoryCode'] ?? '' }}" {{ ($filters['category'] ?? '') == ($category['CategoryCode'] ?? '') ? 'selected' : '' }}>
                        {{ $category['Category'] ?? 'Unknown' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="discontinued" {{ ($filters['status'] ?? '') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control" name="min_price" placeholder="Min $" step="0.01" min="0" value="{{ $filters['min_price'] ?? '' }}" title="Minimum price filter">
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control" name="max_price" placeholder="Max $" step="0.01" min="0" value="{{ $filters['max_price'] ?? '' }}" title="Maximum price filter">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="sort" title="Sort products by">
                    <option value="">Sort By...</option>
                    <option value="name_asc" {{ ($filters['sort'] ?? '') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ ($filters['sort'] ?? '') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="price_low" {{ ($filters['sort'] ?? '') == 'price_low' ? 'selected' : '' }}>Price (Low-High)</option>
                    <option value="price_high" {{ ($filters['sort'] ?? '') == 'price_high' ? 'selected' : '' }}>Price (High-Low)</option>
                    <option value="Qty_avail" {{ ($filters['sort'] ?? '') == 'Qty_avail' ? 'selected' : '' }}>Stock Level</option>
                    <option value="newest" {{ ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' }}>Newest First</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-prt px-4">Filter</button>
                <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary px-4">Clear</a>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus"></i> Add Product
                </button>
                <a href="{{ env('API_PUBLIC_URL', 'http://localhost:8300/api/v1') }}/admin/export/products" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-download"></i> Export</a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Image</th>
                <th class="sortable" data-sort="name">Product Name <i class="bi bi-arrow-down-up"></i></th>
                <th class="sortable" data-sort="sku">SKU <i class="bi bi-arrow-down-up"></i></th>
                <th class="sortable" data-sort="category">Category <i class="bi bi-arrow-down-up"></i></th>
                <th class="sortable" data-sort="price">Price <i class="bi bi-arrow-down-up"></i></th>
                <th>Sale Price</th>
                <th class="sortable" data-sort="stock">Stock <i class="bi bi-arrow-down-up"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products['data'] ?? $products as $product)
            @php
                $stock = $product['stock_quantity'] ?? $product['Qty_Avail'] ?? 0;
                $threshold = $product['low_stock_threshold'] ?? 10;
            @endphp
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td><input type="checkbox" class="product-checkbox" value="{{ $product['ItemNumber'] ?? $product['Item_No'] ?? '' }}"></td>
                <td>
                    @php
                        $imageCount = count($product['images'] ?? []);
                        $primaryImage = collect($product['images'] ?? [])->firstWhere('is_primary', true);
                        $firstImage = $primaryImage ?? ($product['images'][0] ?? null);
                        $imagePath = $firstImage['image_path'] ?? $product['Image'] ?? null;
                        $imageUrl = null;
                        if ($imagePath) {
                            $storefrontUrl = rtrim(env('STOREFRONT_URL', 'http://localhost:8300'), '/');
                            if (str_starts_with($imagePath, 'products/')) {
                                $imageUrl = "{$storefrontUrl}/storage/{$imagePath}";
                            } else {
                                $imageUrl = "{$storefrontUrl}/assets/{$imagePath}";
                            }
                        }
                    @endphp
                    <div class="product-image-cell">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $product['ShortDescription'] ?? 'Product' }}"
                                 class="rounded"
                                 style="width: 100px; height: 50px; object-fit: cover;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'bg-light rounded p-2 d-flex align-items-center justify-content-center\' style=\'width:100px;height:50px;\'><i class=\'bi bi-image\'></i></div>';">
                            @if($imageCount > 1)
                                <span class="image-count-badge">+{{ $imageCount - 1 }}</span>
                            @endif
                        @else
                            <div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 50px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </div>
                </td>
                <td>{{ $product['ShortDescription'] ?? $product['Description'] ?? 'Unknown' }}</td>
                <td>{{ $product['UPC'] ?? $product['UPC_Code'] ?? 'N/A' }}</td>
                <td>{{ $product['category']['Category'] ?? $product['category_name'] ?? 'N/A' }}</td>
                <td>${{ number_format($product['UnitPrice'] ?? $product['Unt_Price'] ?? 0, 2) }}</td>
                <td>{{ !empty($product['sale_price']) && floatval($product['sale_price']) > 0 ? '$' . number_format($product['sale_price'], 2) : '-' }}</td>
                <td>
                    @if($stock <= 0)
                    <span class="text-danger">{{ $stock }}</span>
                    @elseif($stock <= $threshold)
                    <span class="text-warning">{{ $stock }}</span>
                    @else
                    {{ $stock }}
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit" data-upc="{{ $product['UPC'] ?? '' }}" data-name="{{ $product['ShortDescription'] ?? '' }}" data-price="{{ $product['UnitPrice'] ?? 0 }}" data-category="{{ $product['CategoryCode'] ?? '' }}" data-stock="{{ $stock }}" data-threshold="{{ $threshold }}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-info btn-view" title="View" data-upc="{{ $product['UPC'] ?? '' }}"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-delete" data-bs-toggle="modal" data-bs-target="#deleteProductModal" data-product-name="{{ $product['ShortDescription'] ?? $product['Description'] ?? '' }}" data-product-upc="{{ $product['UPC'] ?? '' }}" title="Delete"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2 text-muted">No products found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Bulk Actions -->
<div class="mt-3">
    <select class="form-select d-inline-block" style="width: 200px;">
        <option>Bulk Actions</option>
        <option>Delete Selected</option>
        <option>Set Active</option>
        <option>Set Draft</option>
        <option>Update Category</option>
    </select>
    <button class="btn btn-outline-primary">Apply</button>
</div>

<!-- Pagination -->
@php
    $currentPage = $products['current_page'] ?? 1;
    $lastPage = $products['last_page'] ?? 1;
    $total = $products['total'] ?? count($products['data'] ?? []);
    $perPage = $products['per_page'] ?? 15;
    $from = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
    $to = min($currentPage * $perPage, $total);
@endphp
<nav class="mt-4" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing {{ $from }} to {{ $to }} of {{ $total }} entries
        </div>
        @if($lastPage > 1)
        <ul class="pagination mb-0">
            <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="?page={{ $currentPage - 1 }}&{{ http_build_query(request()->except('page')) }}">Previous</a>
            </li>
            @for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                <a class="page-link" href="?page={{ $i }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
            </li>
            @endfor
            <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                <a class="page-link" href="?page={{ $currentPage + 1 }}&{{ http_build_query(request()->except('page')) }}">Next</a>
            </li>
        </ul>
        @endif
    </div>
</nav>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" class="admin-form">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="addName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control tinymce-product" id="addDescription" rows="4"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">UPC/SKU *</label>
                                    <input type="text" class="form-control" id="addUpc" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" id="addCategory">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category['CategoryCode'] ?? '' }}">{{ $category['Category'] ?? 'Unknown' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- SEO Fields -->
                            <div class="card bg-light mb-3">
                                <div class="card-header py-2">
                                    <i class="bi bi-search me-1"></i> SEO Settings
                                </div>
                                <div class="card-body py-2">
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">Meta Title</label>
                                        <input type="text" class="form-control form-control-sm" id="addMetaTitle" maxlength="60" placeholder="SEO title (max 60 chars)">
                                        <small class="text-muted"><span id="addMetaTitleCount">0</span>/60</small>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small mb-1">Meta Description</label>
                                        <textarea class="form-control form-control-sm" id="addMetaDescription" rows="2" maxlength="160" placeholder="SEO description (max 160 chars)"></textarea>
                                        <small class="text-muted"><span id="addMetaDescCount">0</span>/160</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="addPrice" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cost Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="addCost" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" id="addStock" value="0">
                                </div>
                            </div>
                            <!-- Product Attributes -->
                            <div class="card bg-light mb-3">
                                <div class="card-header py-2">
                                    <i class="bi bi-tags me-1"></i> Product Attributes
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small mb-1">Size</label>
                                            <input type="text" class="form-control form-control-sm" id="addSize" placeholder="e.g., Large, 10x12">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small mb-1">Color</label>
                                            <input type="text" class="form-control form-control-sm" id="addColor" placeholder="e.g., Red, Brown">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small mb-1">Material</label>
                                            <input type="text" class="form-control form-control-sm" id="addMaterial" placeholder="e.g., Leather, Cotton">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Product Images</label>
                                <input type="file" class="form-control" id="addImages" accept="image/*" multiple>
                                <small class="text-muted">Max 2MB each. Up to 10 images. JPG, PNG, GIF, or WebP</small>
                                <div id="addImagesPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item Number</label>
                                <input type="text" class="form-control" id="addItemNumber">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Low Stock Threshold</label>
                                <input type="number" class="form-control" id="addThreshold" value="10">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Track Inventory</label>
                                <select class="form-select" id="addTrackInventory">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Preferred Supplier</label>
                                <select class="form-select" id="addPreferredSupplier">
                                    <option value="">Select Supplier...</option>
                                    <optgroup label="Suppliers" id="addSuppliersGroup"></optgroup>
                                    <optgroup label="Drop Shippers" id="addDropshippersGroup"></optgroup>
                                </select>
                                <small class="text-muted">Default supplier for reordering</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" id="createProductBtn">Create Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" class="admin-form">
                    <input type="hidden" id="editUpc">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="editName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control tinymce-product" id="editDescription" rows="4"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">UPC/SKU</label>
                                    <input type="text" class="form-control" id="editUpcDisplay" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" id="editCategory">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category['CategoryCode'] ?? '' }}">{{ $category['Category'] ?? 'Unknown' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- SEO Fields -->
                            <div class="card bg-light mb-3">
                                <div class="card-header py-2">
                                    <i class="bi bi-search me-1"></i> SEO Settings
                                </div>
                                <div class="card-body py-2">
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">Meta Title</label>
                                        <input type="text" class="form-control form-control-sm" id="editMetaTitle" maxlength="60" placeholder="SEO title (max 60 chars)">
                                        <small class="text-muted"><span id="editMetaTitleCount">0</span>/60</small>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small mb-1">Meta Description</label>
                                        <textarea class="form-control form-control-sm" id="editMetaDescription" rows="2" maxlength="160" placeholder="SEO description (max 160 chars)"></textarea>
                                        <small class="text-muted"><span id="editMetaDescCount">0</span>/160</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="editPrice" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cost Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="editCost" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" id="editStock">
                                </div>
                            </div>
                            <!-- Product Attributes -->
                            <div class="card bg-light mb-3">
                                <div class="card-header py-2">
                                    <i class="bi bi-tags me-1"></i> Product Attributes
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small mb-1">Size</label>
                                            <input type="text" class="form-control form-control-sm" id="editSize" placeholder="e.g., Large, 10x12">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small mb-1">Color</label>
                                            <input type="text" class="form-control form-control-sm" id="editColor" placeholder="e.g., Red, Brown">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small mb-1">Material</label>
                                            <input type="text" class="form-control form-control-sm" id="editMaterial" placeholder="e.g., Leather, Cotton">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Product Images</label>
                                <div id="editImagesGallery" class="mb-2 border rounded p-2" style="min-height: 100px; background: #f8f9fa;">
                                    <div class="text-muted text-center py-3">Loading images...</div>
                                </div>
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-grip-vertical"></i> Drag to reorder |
                                    <i class="bi bi-star"></i> Click star to set primary
                                </small>
                                <label class="form-label mt-2">Add More Images</label>
                                <input type="file" class="form-control" id="editImages" accept="image/*" multiple>
                                <small class="text-muted">Max 2MB each. JPG, PNG, GIF, or WebP</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item Number</label>
                                <input type="text" class="form-control" id="editItemNumber">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Low Stock Threshold</label>
                                <input type="number" class="form-control" id="editThreshold">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Track Inventory</label>
                                <select class="form-select" id="editTrackInventory">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Preferred Supplier</label>
                                <select class="form-select" id="editPreferredSupplier">
                                    <option value="">Select Supplier...</option>
                                    <optgroup label="Suppliers" id="editSuppliersGroup"></optgroup>
                                    <optgroup label="Drop Shippers" id="editDropshippersGroup"></optgroup>
                                </select>
                                <small class="text-muted">Default supplier for reordering</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" id="updateProductBtn">Update Product</button>
            </div>
        </div>
    </div>
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-panel" type="button" role="tab">Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-panel" type="button" role="tab">Change History</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Details Tab -->
                    <div class="tab-pane fade show active" id="details-panel" role="tabpanel">
                        <div id="viewProductContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                            </div>
                        </div>
                    </div>
                    <!-- History Tab -->
                    <div class="tab-pane fade" id="history-panel" role="tabpanel">
                        <div id="viewProductHistory">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary spinner-border-sm"></div>
                                <p class="text-muted small mb-0 mt-2">Loading history...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                <div id="activeOrdersWarning" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This product has <span id="activeOrderCount"></span> active order(s).
                    <div class="mt-2">
                        <small>The product will be soft-deleted (hidden from catalog but kept in database for order history).</small>
                    </div>
                </div>
                <p class="text-muted small mb-0">The product will be soft-deleted (hidden from catalog but kept in database for historical orders).</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete" title="Delete this product">Delete Product</button>
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
/* Image Gallery Styles */
.image-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.image-gallery-item {
    position: relative;
    width: 80px;
    height: 80px;
    border: 2px solid #dee2e6;
    border-radius: 4px;
    cursor: grab;
    overflow: hidden;
    background: #fff;
}
.image-gallery-item.is-primary {
    border-color: #ffc107;
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.3);
}
.image-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.image-gallery-item:active {
    cursor: grabbing;
}
.image-gallery-item .image-actions {
    position: absolute;
    top: 2px;
    right: 2px;
    display: flex;
    gap: 2px;
    opacity: 0;
    transition: opacity 0.2s;
}
.image-gallery-item:hover .image-actions {
    opacity: 1;
}
.image-gallery-item .btn-action {
    padding: 2px 4px;
    font-size: 10px;
    border-radius: 3px;
    line-height: 1;
}
.image-gallery-item .primary-badge {
    position: absolute;
    bottom: 2px;
    left: 2px;
    background: #ffc107;
    color: #000;
    font-size: 9px;
    padding: 1px 4px;
    border-radius: 3px;
}
.image-gallery-item.dragging {
    opacity: 0.5;
}
.image-gallery-item.drag-over {
    border-color: #0d6efd;
    border-style: dashed;
}
.add-image-preview {
    position: relative;
    width: 80px;
    height: 80px;
    border: 2px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
}
.add-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.add-image-preview .remove-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    padding: 2px 5px;
    font-size: 10px;
    border-radius: 3px;
}
/* History Timeline Styles */
.history-timeline {
    position: relative;
    padding-left: 20px;
}
.history-timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.history-item {
    position: relative;
    padding-bottom: 15px;
    margin-bottom: 10px;
}
.history-item::before {
    content: '';
    position: absolute;
    left: -17px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #6c757d;
    border: 2px solid #fff;
}
.history-item.action-create::before { background: #28a745; }
.history-item.action-update::before { background: #007bff; }
.history-item.action-delete::before { background: #dc3545; }
.history-item.action-stock_adjustment::before { background: #ffc107; }
.history-item .history-time {
    font-size: 11px;
    color: #6c757d;
}
.history-item .history-action {
    font-weight: 500;
    font-size: 13px;
}
.history-item .history-change {
    font-size: 12px;
    color: #495057;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    margin-top: 4px;
}
.history-item .old-value {
    color: #dc3545;
    text-decoration: line-through;
}
.history-item .new-value {
    color: #28a745;
}
/* View Image Gallery Styles */
.view-image-gallery .main-image {
    text-align: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
}
.view-image-gallery .main-image img {
    max-height: 250px;
    object-fit: contain;
}
.view-thumbnail {
    width: 50px;
    height: 50px;
    border: 2px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.2s;
    position: relative;
}
.view-thumbnail:hover {
    border-color: #0d6efd;
}
.view-thumbnail.is-primary {
    border-color: #ffc107;
}
.view-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.view-thumbnail .primary-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 8px;
    height: 8px;
    background: #ffc107;
    border-radius: 50%;
    border: 1px solid #fff;
}
/* Product list image count badge */
.image-count-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    font-size: 10px;
    padding: 1px 4px;
    border-radius: 3px;
}
.product-image-cell {
    position: relative;
    display: inline-block;
}
</style>

<script>
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');

    if (!row) return;

    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.tagName === 'INPUT' || target.closest('button') || target.closest('a') || target.closest('select')) {
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
let allSuppliers = [];

// TinyMCE initialization for product descriptions
function initProductTinyMCE(selector) {
    tinymce.init({
        selector: selector,
        height: 250,
        menubar: false,
        plugins: 'link lists table',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link table | removeformat',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
        branding: false,
        promotion: false,
        setup: function(editor) {
            editor.on('change', function() { editor.save(); });
        }
    });
}

function destroyProductTinyMCE(selector) {
    var editor = tinymce.get(selector.replace('#', ''));
    if (editor) {
        editor.destroy();
    }
}

// Initialize TinyMCE for Add Product modal
document.getElementById('addProductModal').addEventListener('shown.bs.modal', function() {
    setTimeout(function() {
        initProductTinyMCE('#addDescription');
    }, 100);
});

document.getElementById('addProductModal').addEventListener('hidden.bs.modal', function() {
    destroyProductTinyMCE('#addDescription');
});

// Initialize TinyMCE for Edit Product modal
document.getElementById('editProductModal').addEventListener('shown.bs.modal', function() {
    setTimeout(function() {
        initProductTinyMCE('#editDescription');
    }, 100);
});

document.getElementById('editProductModal').addEventListener('hidden.bs.modal', function() {
    destroyProductTinyMCE('#editDescription');
});

// Load suppliers and dropshippers
async function loadSuppliers() {
    try {
        // Load suppliers
        const suppliersResponse = await fetch(`${API_BASE}/admin/suppliers`);
        const suppliersData = await suppliersResponse.json();

        // Load dropshippers
        const dropshippersResponse = await fetch(`${API_BASE}/admin/dropshippers`);
        const dropshippersData = await dropshippersResponse.json();

        allSuppliers = [];

        // Populate both Add and Edit modals
        const addSuppliersGroup = document.getElementById('addSuppliersGroup');
        const addDropshippersGroup = document.getElementById('addDropshippersGroup');
        const editSuppliersGroup = document.getElementById('editSuppliersGroup');
        const editDropshippersGroup = document.getElementById('editDropshippersGroup');

        // Clear existing options
        addSuppliersGroup.innerHTML = '';
        addDropshippersGroup.innerHTML = '';
        editSuppliersGroup.innerHTML = '';
        editDropshippersGroup.innerHTML = '';

        // Add suppliers
        if (suppliersData.success && suppliersData.data) {
            suppliersData.data.forEach(supplier => {
                if (supplier.status === 'active') {
                    allSuppliers.push({
                        type: 'supplier',
                        id: supplier.id,
                        name: supplier.company_name
                    });

                    const option1 = document.createElement('option');
                    option1.value = `supplier_${supplier.id}`;
                    option1.textContent = supplier.company_name;
                    addSuppliersGroup.appendChild(option1);

                    const option2 = document.createElement('option');
                    option2.value = `supplier_${supplier.id}`;
                    option2.textContent = supplier.company_name;
                    editSuppliersGroup.appendChild(option2);
                }
            });
        }

        // Add dropshippers
        if (dropshippersData.success && dropshippersData.data) {
            dropshippersData.data.forEach(dropshipper => {
                if (dropshipper.status === 'active') {
                    allSuppliers.push({
                        type: 'dropshipper',
                        id: dropshipper.id,
                        name: dropshipper.company_name
                    });

                    const option1 = document.createElement('option');
                    option1.value = `dropshipper_${dropshipper.id}`;
                    option1.textContent = dropshipper.company_name;
                    addDropshippersGroup.appendChild(option1);

                    const option2 = document.createElement('option');
                    option2.value = `dropshipper_${dropshipper.id}`;
                    option2.textContent = dropshipper.company_name;
                    editDropshippersGroup.appendChild(option2);
                }
            });
        }
    } catch (error) {
        console.error('Error loading suppliers:', error);
    }
}

// Load suppliers on page load
loadSuppliers();

// Handle delete modal - reset state when opening
document.getElementById('deleteProductModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var productName = button.getAttribute('data-product-name');
    var productUpc = button.getAttribute('data-product-upc');

    document.getElementById('deleteProductName').textContent = productName;
    document.getElementById('confirmDelete').setAttribute('data-product-upc', productUpc);
    document.getElementById('confirmDelete').removeAttribute('data-force');
    document.getElementById('activeOrdersWarning').classList.add('d-none');
    document.getElementById('confirmDelete').innerHTML = 'Delete Product';
});

// Confirm delete
document.getElementById('confirmDelete').addEventListener('click', async function() {
    var productUpc = this.getAttribute('data-product-upc');
    var forceDelete = this.hasAttribute('data-force');

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Deleting...';

    try {
        const url = forceDelete
            ? `${API_BASE}/products/${productUpc}?force=true`
            : `${API_BASE}/products/${productUpc}`;

        const response = await fetch(url, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteProductModal')).hide();
            alert('Product deleted successfully');
            location.reload();
        } else if (response.status === 409 && result.active_orders) {
            // Product has active orders - show warning and offer force delete
            document.getElementById('activeOrdersWarning').classList.remove('d-none');
            document.getElementById('activeOrderCount').textContent = result.active_orders;
            this.setAttribute('data-force', 'true');
            this.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Confirm Delete';
            this.disabled = false;
        } else {
            alert('Error: ' + (result.message || 'Failed to delete product'));
            this.disabled = false;
            this.innerHTML = 'Delete Product';
        }
    } catch (error) {
        alert('Error: ' + error.message);
        this.disabled = false;
        this.innerHTML = 'Delete Product';
    }
});

// Edit button click - fetch full product data from API
let currentEditUpc = null;
let editProductImages = [];

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', async function() {
        const upc = this.dataset.upc;
        currentEditUpc = upc;
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();

        try {
            const response = await fetch(`${API_BASE}/products/${upc}`);
            const result = await response.json();

            if (result.success && result.data) {
                const p = result.data;
                document.getElementById('editUpc').value = p.UPC || '';
                document.getElementById('editUpcDisplay').value = p.UPC || '';
                document.getElementById('editName').value = p.ShortDescription || '';
                document.getElementById('editDescription').value = p.LngDescription || '';
                // Set TinyMCE content after modal is shown
                setTimeout(function() {
                    var editor = tinymce.get('editDescription');
                    if (editor) editor.setContent(p.LngDescription || '');
                }, 200);
                document.getElementById('editPrice').value = p.UnitPrice || 0;
                document.getElementById('editCost').value = p.cost_price || 0;
                document.getElementById('editCategory').value = p.CategoryCode || '';
                document.getElementById('editStock').value = p.stock_quantity || 0;
                document.getElementById('editThreshold').value = p.low_stock_threshold || 10;
                document.getElementById('editItemNumber').value = p.ItemNumber || '';
                document.getElementById('editTrackInventory').value = p.track_inventory ? '1' : '0';

                // Load SEO fields
                document.getElementById('editMetaTitle').value = p.meta_title || '';
                document.getElementById('editMetaDescription').value = p.meta_description || '';
                document.getElementById('editMetaTitleCount').textContent = (p.meta_title || '').length;
                document.getElementById('editMetaDescCount').textContent = (p.meta_description || '').length;

                // Load product attributes
                document.getElementById('editSize').value = p.ItemSize || '';
                document.getElementById('editColor').value = p.color || '';
                document.getElementById('editMaterial').value = p.material || '';

                // Set preferred supplier
                if (p.preferred_supplier_id) {
                    document.getElementById('editPreferredSupplier').value = `supplier_${p.preferred_supplier_id}`;
                } else {
                    document.getElementById('editPreferredSupplier').value = '';
                }

                // Load images gallery
                await loadEditImagesGallery(upc);
            }
        } catch (error) {
            alert('Error loading product: ' + error.message);
        }
    });
});

// Load images gallery for edit modal
async function loadEditImagesGallery(upc) {
    const gallery = document.getElementById('editImagesGallery');
    gallery.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';

    try {
        const response = await fetch(`${API_BASE}/products/${upc}/images`);
        const result = await response.json();

        if (result.success) {
            editProductImages = result.data || [];
            renderEditImagesGallery();
        } else {
            gallery.innerHTML = '<div class="text-muted text-center py-3">No images</div>';
        }
    } catch (error) {
        gallery.innerHTML = '<div class="text-danger text-center py-3">Error loading images</div>';
    }
}

// Render the images gallery with drag-and-drop
function renderEditImagesGallery() {
    const gallery = document.getElementById('editImagesGallery');

    if (editProductImages.length === 0) {
        gallery.innerHTML = '<div class="text-muted text-center py-3">No images yet</div>';
        return;
    }

    gallery.innerHTML = '<div class="image-gallery" id="imageGallerySortable"></div>';
    const sortable = document.getElementById('imageGallerySortable');

    editProductImages.forEach((img, index) => {
        const item = document.createElement('div');
        item.className = `image-gallery-item ${img.is_primary ? 'is-primary' : ''}`;
        item.dataset.imageId = img.id;
        item.draggable = true;

        // Use storage path or legacy Image field
        const imgSrc = img.image_path
            ? (img.image_path.startsWith('products/') ? `${STOREFRONT_URL}/storage/${img.image_path}` : `${STOREFRONT_URL}/assets/${img.image_path}`)
            : '';

        item.innerHTML = `
            <img src="${imgSrc}" alt="Product image" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23f0f0f0%22 width=%22100%22 height=%22100%22/><text fill=%22%23999%22 x=%2250%22 y=%2255%22 text-anchor=%22middle%22>?</text></svg>'">
            <div class="image-actions">
                <button type="button" class="btn btn-warning btn-action" onclick="setImagePrimary(${img.id})" title="${img.is_primary ? 'Primary image' : 'Set as primary'}">
                    <i class="bi bi-star${img.is_primary ? '-fill' : ''}"></i>
                </button>
                <button type="button" class="btn btn-danger btn-action" onclick="deleteProductImage(${img.id})" title="Delete image">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            ${img.is_primary ? '<span class="primary-badge">Primary</span>' : ''}
        `;

        // Drag events
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('dragleave', handleDragLeave);
        item.addEventListener('drop', handleDrop);
        item.addEventListener('dragend', handleDragEnd);

        sortable.appendChild(item);
    });
}

// Drag and drop handlers
let draggedItem = null;

function handleDragStart(e) {
    draggedItem = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    this.classList.add('drag-over');
}

function handleDragLeave(e) {
    this.classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    this.classList.remove('drag-over');

    if (draggedItem !== this) {
        const gallery = document.getElementById('imageGallerySortable');
        const items = Array.from(gallery.querySelectorAll('.image-gallery-item'));
        const fromIndex = items.indexOf(draggedItem);
        const toIndex = items.indexOf(this);

        if (fromIndex < toIndex) {
            this.parentNode.insertBefore(draggedItem, this.nextSibling);
        } else {
            this.parentNode.insertBefore(draggedItem, this);
        }

        // Save new order
        saveImageOrder();
    }
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    document.querySelectorAll('.image-gallery-item').forEach(item => {
        item.classList.remove('drag-over');
    });
}

// Save image order to API
async function saveImageOrder() {
    const gallery = document.getElementById('imageGallerySortable');
    const items = gallery.querySelectorAll('.image-gallery-item');
    const order = Array.from(items).map(item => parseInt(item.dataset.imageId));

    try {
        await fetch(`${API_BASE}/products/${currentEditUpc}/images/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });
    } catch (error) {
        console.error('Error saving image order:', error);
    }
}

// Set image as primary
async function setImagePrimary(imageId) {
    try {
        const response = await fetch(`${API_BASE}/products/${currentEditUpc}/images/${imageId}/primary`, {
            method: 'PUT'
        });
        const result = await response.json();

        if (result.success) {
            // Update local state and re-render
            editProductImages.forEach(img => {
                img.is_primary = (img.id === imageId);
            });
            renderEditImagesGallery();
        } else {
            alert('Error: ' + (result.message || 'Failed to set primary image'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Delete product image
async function deleteProductImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/products/${currentEditUpc}/images/${imageId}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.success) {
            editProductImages = result.data || [];
            renderEditImagesGallery();
        } else {
            alert('Error: ' + (result.message || 'Failed to delete image'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Upload new images in edit modal
document.getElementById('editImages').addEventListener('change', async function() {
    const files = Array.from(this.files);
    if (files.length === 0) return;

    // Validate files
    for (const file of files) {
        if (file.size > 2 * 1024 * 1024) {
            alert(`Image "${file.name}" must be less than 2MB`);
            return;
        }
    }

    const formData = new FormData();
    files.forEach(file => {
        formData.append('images[]', file);
    });

    try {
        const response = await fetch(`${API_BASE}/products/${currentEditUpc}/images`, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            // Reload the gallery
            await loadEditImagesGallery(currentEditUpc);
            this.value = ''; // Clear input
        } else {
            alert('Error: ' + (result.message || 'Failed to upload images'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

// Update product
document.getElementById('updateProductBtn').addEventListener('click', async function() {
    const upc = document.getElementById('editUpc').value;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';

    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('ShortDescription', document.getElementById('editName').value);
        // Get TinyMCE content
        var editDescEditor = tinymce.get('editDescription');
        var editDescContent = editDescEditor ? editDescEditor.getContent() : document.getElementById('editDescription').value;
        formData.append('LngDescription', editDescContent);
        formData.append('UnitPrice', parseFloat(document.getElementById('editPrice').value));
        formData.append('cost_price', parseFloat(document.getElementById('editCost').value) || 0);
        formData.append('CategoryCode', document.getElementById('editCategory').value);
        formData.append('stock_quantity', parseInt(document.getElementById('editStock').value) || 0);
        formData.append('low_stock_threshold', parseInt(document.getElementById('editThreshold').value) || 10);
        formData.append('ItemNumber', document.getElementById('editItemNumber').value);
        formData.append('track_inventory', document.getElementById('editTrackInventory').value === '1' ? 1 : 0);

        // Add SEO fields
        formData.append('meta_title', document.getElementById('editMetaTitle').value);
        formData.append('meta_description', document.getElementById('editMetaDescription').value);

        // Add product attributes
        formData.append('ItemSize', document.getElementById('editSize').value);
        formData.append('color', document.getElementById('editColor').value);
        formData.append('material', document.getElementById('editMaterial').value);

        // Add preferred supplier
        const preferredSupplier = document.getElementById('editPreferredSupplier').value;
        if (preferredSupplier && preferredSupplier.startsWith('supplier_')) {
            formData.append('preferred_supplier_id', preferredSupplier.replace('supplier_', ''));
        } else if (preferredSupplier === '') {
            formData.append('preferred_supplier_id', '');
        }

        // Note: Images are now managed separately via the image gallery

        const response = await fetch(`${API_BASE}/products/${upc}`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
            alert('Product updated successfully');
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to update product'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = 'Update Product';
});

// View button click
document.querySelectorAll('.btn-view').forEach(btn => {
    btn.addEventListener('click', async function() {
        const upc = this.dataset.upc;
        const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
        modal.show();

        // Reset to details tab
        document.getElementById('details-tab').click();

        // Load product details
        try {
            const response = await fetch(`${API_BASE}/products/${upc}`);
            const result = await response.json();

            if (result.success && result.data) {
                const p = result.data;
                document.getElementById('viewProductContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-sm">
                                <tr><th width="35%">Name</th><td>${p.ShortDescription || 'N/A'}</td></tr>
                                <tr><th>UPC</th><td>${p.UPC || 'N/A'}</td></tr>
                                <tr><th>Item Number</th><td>${p.ItemNumber || 'N/A'}</td></tr>
                                <tr><th>Category</th><td>${p.category?.Category || p.CategoryCode || 'N/A'}</td></tr>
                                <tr><th>Price</th><td>$${parseFloat(p.UnitPrice || 0).toFixed(2)}</td></tr>
                                <tr><th>Cost</th><td>$${parseFloat(p.cost_price || 0).toFixed(2)}</td></tr>
                                <tr><th>Stock</th><td>${p.stock_quantity || 0}</td></tr>
                                <tr><th>Reserved</th><td>${p.reserved_quantity || 0}</td></tr>
                                <tr><th>Available</th><td>${(p.stock_quantity || 0) - (p.reserved_quantity || 0)}</td></tr>
                                <tr><th>Low Stock Threshold</th><td>${p.low_stock_threshold || 'N/A'}</td></tr>
                                <tr><th>Track Inventory</th><td>${p.track_inventory ? 'Yes' : 'No'}</td></tr>
                                ${p.meta_title ? `<tr><th>Meta Title</th><td>${p.meta_title}</td></tr>` : ''}
                                ${p.meta_description ? `<tr><th>Meta Description</th><td>${p.meta_description}</td></tr>` : ''}
                                ${p.ItemSize ? `<tr><th>Size</th><td>${p.ItemSize}</td></tr>` : ''}
                                ${p.color ? `<tr><th>Color</th><td>${p.color}</td></tr>` : ''}
                                ${p.material ? `<tr><th>Material</th><td>${p.material}</td></tr>` : ''}
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div id="viewProductImages">
                                <div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('viewProductContent').innerHTML = '<p class="text-danger">Product not found</p>';
            }
        } catch (error) {
            document.getElementById('viewProductContent').innerHTML = `<p class="text-danger">Error: ${error.message}</p>`;
        }

        // Load product images gallery
        loadViewProductImages(upc);

        // Load product history
        loadProductHistory(upc);
    });
});

// Load product images for view modal
async function loadViewProductImages(upc) {
    const container = document.getElementById('viewProductImages');
    container.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';

    try {
        const response = await fetch(`${API_BASE}/products/${upc}/images`);
        const result = await response.json();

        if (result.success && result.data && result.data.length > 0) {
            const images = result.data;
            const primaryImage = images.find(img => img.is_primary) || images[0];

            let html = `
                <div class="view-image-gallery">
                    <div class="main-image mb-2">
                        <img id="viewMainImage" src="${getImageUrl(primaryImage)}"
                             class="img-fluid rounded" alt="Product image"
                             onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 200%22><rect fill=%22%23f0f0f0%22 width=%22200%22 height=%22200%22/><text fill=%22%23999%22 x=%22100%22 y=%22105%22 text-anchor=%22middle%22>No Image</text></svg>'">
                    </div>
                    <div class="text-muted small mb-2 text-center">${images.length} image${images.length > 1 ? 's' : ''}</div>
            `;

            if (images.length > 1) {
                html += '<div class="thumbnail-strip d-flex gap-1 flex-wrap justify-content-center">';
                images.forEach((img, index) => {
                    html += `
                        <div class="view-thumbnail ${img.is_primary ? 'is-primary' : ''}"
                             onclick="setViewMainImage('${getImageUrl(img)}')"
                             title="${img.is_primary ? 'Primary image' : 'Click to view'}">
                            <img src="${getImageUrl(img)}" alt="Thumbnail ${index + 1}"
                                 onerror="this.parentElement.style.display='none'">
                            ${img.is_primary ? '<span class="primary-dot"></span>' : ''}
                        </div>
                    `;
                });
                html += '</div>';
            }

            html += '</div>';
            container.innerHTML = html;
        } else {
            // Fall back to legacy single image
            const productResponse = await fetch(`${API_BASE}/products/${upc}`);
            const productResult = await productResponse.json();

            if (productResult.success && productResult.data && productResult.data.Image) {
                container.innerHTML = `
                    <img src="${STOREFRONT_URL}/assets/${productResult.data.Image}"
                         class="img-fluid rounded" alt="Product image"
                         onerror="this.parentElement.innerHTML='<div class=\\'bg-light rounded p-4 text-center text-muted\\'>No image</div>'">
                `;
            } else {
                container.innerHTML = '<div class="bg-light rounded p-4 text-center text-muted"><i class="bi bi-image" style="font-size: 2rem;"></i><p class="mb-0 mt-2">No images</p></div>';
            }
        }
    } catch (error) {
        container.innerHTML = '<div class="bg-light rounded p-4 text-center text-muted">Error loading images</div>';
    }
}

// Helper to get image URL
function getImageUrl(img) {
    if (img.image_path) {
        // Check if it's a storage path or legacy path
        if (img.image_path.startsWith('products/')) {
            return `${STOREFRONT_URL}/storage/${img.image_path}`;
        }
        return `${STOREFRONT_URL}/assets/${img.image_path}`;
    }
    return '';
}

// Set main image in view modal
function setViewMainImage(url) {
    document.getElementById('viewMainImage').src = url;
}

// Load product history
async function loadProductHistory(upc) {
    const historyContainer = document.getElementById('viewProductHistory');
    historyContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div><p class="text-muted small mb-0 mt-2">Loading history...</p></div>';

    try {
        const response = await fetch(`${API_BASE}/products/${upc}/history?per_page=50`);
        const result = await response.json();

        if (result.success && result.data && result.data.length > 0) {
            let html = '<div class="history-timeline">';

            result.data.forEach(item => {
                const date = new Date(item.created_at);
                const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const formattedTime = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                html += `
                    <div class="history-item action-${item.action}">
                        <div class="history-time">${formattedDate} at ${formattedTime} by ${item.user_name || 'System'}</div>
                        <div class="history-action">${item.action_label || item.action}: ${item.field_label || item.field_name}</div>
                        <div class="history-change">
                            ${item.old_value !== null ? `<span class="old-value">${truncateValue(item.old_value)}</span> &rarr; ` : ''}
                            <span class="new-value">${truncateValue(item.new_value)}</span>
                            ${item.notes ? `<br><small class="text-muted">${item.notes}</small>` : ''}
                        </div>
                    </div>
                `;
            });

            html += '</div>';

            if (result.meta.total > result.data.length) {
                html += `<p class="text-muted small text-center mt-3">Showing ${result.data.length} of ${result.meta.total} changes</p>`;
            }

            historyContainer.innerHTML = html;
        } else {
            historyContainer.innerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-clock-history" style="font-size: 2rem;"></i><p class="mb-0 mt-2">No change history available</p></div>';
        }
    } catch (error) {
        historyContainer.innerHTML = `<p class="text-danger">Error loading history: ${error.message}</p>`;
    }
}

// Truncate long values in history
function truncateValue(value) {
    if (value === null || value === undefined) return '(empty)';
    const str = String(value);
    if (str.length > 100) {
        return str.substring(0, 100) + '...';
    }
    return str;
}

// Multiple image preview for add product
let addImageFiles = [];

document.getElementById('addImages').addEventListener('change', function() {
    const files = Array.from(this.files);
    const preview = document.getElementById('addImagesPreview');

    files.forEach((file, index) => {
        if (file.size > 2 * 1024 * 1024) {
            alert(`Image "${file.name}" must be less than 2MB`);
            return;
        }
        if (addImageFiles.length >= 10) {
            alert('Maximum 10 images allowed');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const imgIndex = addImageFiles.length;
            addImageFiles.push(file);

            const div = document.createElement('div');
            div.className = 'add-image-preview';
            div.dataset.index = imgIndex;
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="btn btn-danger remove-btn" onclick="removeAddImage(${imgIndex})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    this.value = ''; // Clear input to allow re-selecting same files
});

function removeAddImage(index) {
    addImageFiles[index] = null; // Don't splice to keep indices consistent
    const preview = document.getElementById('addImagesPreview');
    const item = preview.querySelector(`[data-index="${index}"]`);
    if (item) item.remove();
}

// Create product
document.getElementById('createProductBtn').addEventListener('click', async function() {
    const name = document.getElementById('addName').value;
    const upc = document.getElementById('addUpc').value;
    const price = document.getElementById('addPrice').value;

    if (!name || !upc || !price) {
        alert('Please fill in required fields (Name, UPC, Price)');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating...';

    try {
        const formData = new FormData();
        formData.append('UPC', upc);
        formData.append('ItemNumber', document.getElementById('addItemNumber').value || upc);
        formData.append('ShortDescription', name);
        // Get TinyMCE content
        var addDescEditor = tinymce.get('addDescription');
        var addDescContent = addDescEditor ? addDescEditor.getContent() : document.getElementById('addDescription').value;
        formData.append('LongDescription', addDescContent);
        formData.append('UnitPrice', parseFloat(price));
        formData.append('cost_price', parseFloat(document.getElementById('addCost').value) || 0);
        formData.append('CategoryCode', document.getElementById('addCategory').value);
        formData.append('stock_quantity', parseInt(document.getElementById('addStock').value) || 0);
        formData.append('low_stock_threshold', parseInt(document.getElementById('addThreshold').value) || 10);
        formData.append('track_inventory', document.getElementById('addTrackInventory').value === '1' ? 1 : 0);

        // Add SEO fields
        formData.append('meta_title', document.getElementById('addMetaTitle').value);
        formData.append('meta_description', document.getElementById('addMetaDescription').value);

        // Add product attributes
        formData.append('ItemSize', document.getElementById('addSize').value);
        formData.append('color', document.getElementById('addColor').value);
        formData.append('material', document.getElementById('addMaterial').value);

        // Add preferred supplier
        const preferredSupplier = document.getElementById('addPreferredSupplier').value;
        if (preferredSupplier && preferredSupplier.startsWith('supplier_')) {
            formData.append('preferred_supplier_id', preferredSupplier.replace('supplier_', ''));
        }

        const response = await fetch(`${API_BASE}/products`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Upload images if any selected
            const validImages = addImageFiles.filter(f => f !== null);
            if (validImages.length > 0) {
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading images...';
                const imageFormData = new FormData();
                validImages.forEach(file => {
                    imageFormData.append('images[]', file);
                });

                await fetch(`${API_BASE}/products/${upc}/images`, {
                    method: 'POST',
                    body: imageFormData
                });
            }

            bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
            alert('Product created successfully');
            addImageFiles = []; // Reset
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to create product'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = 'Create Product';
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = this.checked;
    }, this);
});

// Sortable columns
document.querySelectorAll('.sortable').forEach(function(header) {
    header.style.cursor = 'pointer';
    header.addEventListener('click', function() {
        var sortField = this.getAttribute('data-sort');
        const url = new URL(window.location);
        url.searchParams.set('sort', sortField);
        window.location = url;
    });
});

// SEO field character counters
document.getElementById('addMetaTitle').addEventListener('input', function() {
    document.getElementById('addMetaTitleCount').textContent = this.value.length;
});
document.getElementById('addMetaDescription').addEventListener('input', function() {
    document.getElementById('addMetaDescCount').textContent = this.value.length;
});
document.getElementById('editMetaTitle').addEventListener('input', function() {
    document.getElementById('editMetaTitleCount').textContent = this.value.length;
});
document.getElementById('editMetaDescription').addEventListener('input', function() {
    document.getElementById('editMetaDescCount').textContent = this.value.length;
});
</script>
@endpush
