@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="page-header">
    <h1>Inventory Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Inventory</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="value">{{ number_format($stats['total_products'] ?? 0) }}</div>
            <div class="label">Total Products</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value">{{ number_format($stats['in_stock_count'] ?? 0) }}</div>
            <div class="label">In Stock</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="value">{{ number_format($stats['low_stock_count'] ?? 0) }}</div>
            <div class="label">Low Stock</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="value">{{ number_format($stats['out_of_stock_count'] ?? 0) }}</div>
            <div class="label">Out of Stock</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search products..." value="{{ $filters['search'] ?? '' }}">
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
                <select class="form-select" name="stock_status">
                    <option value="">All Status</option>
                    <option value="in_stock" {{ ($filters['stock_status'] ?? '') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ ($filters['stock_status'] ?? '') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ ($filters['stock_status'] ?? '') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-prt w-100">Filter</button>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('admin.products') }}" class="btn btn-success"><i class="bi bi-plus"></i> Add Product</a>
                <a href="{{ env('API_PUBLIC_URL', 'http://localhost:8300/api/v1') }}/admin/export/products" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-download"></i> Export</a>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Threshold</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products['products'] ?? $products['data'] ?? $products as $product)
            @php
                $stock = $product['stock_quantity'] ?? $product['available'] ?? $product['Qty_Avail'] ?? 0;
                $threshold = $product['low_stock_threshold'] ?? 10;
                $status = $stock <= 0 ? 'out' : ($stock <= $threshold ? 'low' : 'in');
            @endphp
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        @php
                            $imageUrl = $product['image'] ?? $product['Image'] ?? $product['ImagePath'] ?? null;
                            if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                                $imageUrl = 'http://localhost/PRT2/' . ltrim($imageUrl, '/');
                            }
                        @endphp
                        @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="Product" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                        <div class="bg-light rounded p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-image text-muted"></i></div>
                        @endif
                        <div>{{ $product['ShortDescription'] ?? $product['Description'] ?? $product['name'] ?? 'Unknown' }}</div>
                    </div>
                </td>
                <td>{{ $product['ItemNumber'] ?? $product['UPC'] ?? $product['UPC_Code'] ?? 'N/A' }}</td>
                <td>{{ $product['category_name'] ?? $product['Category'] ?? 'N/A' }}</td>
                <td class="{{ $status == 'out' ? 'text-danger' : ($status == 'low' ? 'text-warning' : '') }}">
                    <strong>{{ $stock }}</strong>
                </td>
                <td>{{ $threshold }}</td>
                <td>${{ number_format($product['UnitPrice'] ?? $product['Unt_Price'] ?? $product['price'] ?? 0, 2) }}</td>
                <td>
                    @if($status == 'out')
                    <span class="status-badge inactive">Out of Stock</span>
                    @elseif($status == 'low')
                    <span class="status-badge pending">Low Stock</span>
                    @else
                    <span class="status-badge active">In Stock</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" title="Adjust Stock" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product['UPC'] ?? $product['ItemNumber'] ?? $product['Item_No'] ?? '' }}" data-product-name="{{ $product['ShortDescription'] ?? $product['Description'] ?? '' }}" data-current-stock="{{ $stock }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-secondary" title="View Product">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2 text-muted">No products found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if(isset($products['last_page']) && $products['last_page'] > 1)
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item {{ $products['current_page'] == 1 ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $products['current_page'] - 1 }}">Previous</a>
        </li>
        @for($i = 1; $i <= min($products['last_page'], 5); $i++)
        <li class="page-item {{ $products['current_page'] == $i ? 'active' : '' }}">
            <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
        </li>
        @endfor
        <li class="page-item {{ $products['current_page'] == $products['last_page'] ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $products['current_page'] + 1 }}">Next</a>
        </li>
    </ul>
</nav>
@endif

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Product: <strong id="adjustProductName"></strong></p>
                <p class="mb-3">Current Stock: <strong id="currentStockDisplay"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Adjustment Type</label>
                    <select class="form-select" id="adjustmentType">
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                        <option value="set">Set Stock</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="adjustmentQuantity" min="0" value="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <input type="text" class="form-control" id="adjustmentReason" placeholder="e.g., Received shipment, Damaged goods">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" id="confirmAdjust">Adjust Stock</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Handle adjust stock modal
    document.getElementById('adjustStockModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var productId = button.getAttribute('data-product-id');
        var productName = button.getAttribute('data-product-name');
        var currentStock = button.getAttribute('data-current-stock');

        document.getElementById('adjustProductName').textContent = productName;
        document.getElementById('currentStockDisplay').textContent = currentStock;
        document.getElementById('confirmAdjust').setAttribute('data-product-id', productId);
    });

    document.getElementById('confirmAdjust').addEventListener('click', async function() {
        var productId = this.getAttribute('data-product-id');
        var type = document.getElementById('adjustmentType').value;
        var quantity = parseInt(document.getElementById('adjustmentQuantity').value);
        var reason = document.getElementById('adjustmentReason').value;
        var currentStock = parseInt(document.getElementById('currentStockDisplay').textContent);

        if (!quantity || quantity <= 0) {
            alert('Please enter a valid quantity');
            return;
        }

        // Calculate adjustment based on type
        var adjustment;
        if (type === 'add') {
            adjustment = quantity;
        } else if (type === 'remove') {
            adjustment = -quantity;
        } else if (type === 'set') {
            adjustment = quantity - currentStock;
        }

        try {
            // First, we need to get the product's database ID from the UPC/ItemNumber
            const searchResponse = await fetch(`{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}/admin/inventory/products?search=${encodeURIComponent(productId)}&per_page=1`);
            const searchResult = await searchResponse.json();

            if (!searchResult.success || !searchResult.data.products || searchResult.data.products.length === 0) {
                alert('Product not found');
                return;
            }

            const productDbId = searchResult.data.products[0].id;

            const response = await fetch('{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}/admin/inventory/adjust-stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productDbId,
                    adjustment: adjustment,
                    notes: reason || `Stock ${type} from inventory page`
                })
            });

            const result = await response.json();

            if (result.success) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('adjustStockModal'));
                modal.hide();
                alert('Stock adjusted successfully!');
                location.reload();
            } else {
                alert('Error: ' + (result.message || result.error || 'Failed to adjust stock'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error adjusting stock: ' + error.message);
        }
    });
</script>
@endsection
