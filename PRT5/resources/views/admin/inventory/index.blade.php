@extends('layouts.admin')

@section('title', 'Inventory Dashboard')

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--prt-brown);
    }
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .stock-low { color: #ffc107; }
    .stock-out { color: #dc3545; }
    .stock-ok { color: #28a745; }
    .alert-item {
        padding: 10px;
        border-left: 4px solid #ffc107;
        background: #fff3cd;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    .alert-item.out-of-stock {
        border-left-color: #dc3545;
        background: #f8d7da;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-box-seam"></i> Inventory Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Inventory Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                <div class="stat-label">Total Products</div>
                <small class="text-muted">{{ number_format($stats['tracked_products']) }} tracked</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">${{ number_format($stats['total_inventory_value'], 2) }}</div>
                <div class="stat-label">Inventory Value</div>
                <small class="text-muted">At cost price</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value stock-low">{{ number_format($stats['low_stock_count']) }}</div>
                <div class="stat-label">Low Stock Items</div>
                <small class="text-muted">Below threshold</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value stock-out">{{ number_format($stats['out_of_stock_count']) }}</div>
                <div class="stat-label">Out of Stock</div>
                <small class="text-muted">Need reorder</small>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Main Inventory Table --}}
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Inventory List</h5>
                    <div>
                        <a href="{{ route('admin.inventory.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Export current inventory list to CSV file">
                            <i class="bi bi-download"></i> Export
                        </a>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="card-body border-bottom">
                    <form method="GET" class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by name, UPC, or item #"
                                   value="{{ $filters['search'] }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->CategoryCode }}"
                                            {{ $filters['category'] == $cat->CategoryCode ? 'selected' : '' }}>
                                        {{ $cat->Category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="in_stock" {{ $filters['status'] == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ $filters['status'] == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ $filters['status'] == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Apply search and filter criteria to inventory list">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Products Table --}}
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>UPC</th>
                                <th>Stock</th>
                                <th>Reserved</th>
                                <th>Available</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($products as $product)
                                @php
                                    $available = $product->availableQuantity;
                                    $threshold = $product->low_stock_threshold;
                                    $value = $product->stock_quantity * $product->cost_price;

                                    if (!$product->track_inventory) {
                                        $statusClass = 'text-muted';
                                        $statusIcon = 'bi-slash-circle';
                                        $statusText = 'Not Tracked';
                                    } elseif ($available <= 0) {
                                        $statusClass = 'stock-out';
                                        $statusIcon = 'bi-x-circle-fill';
                                        $statusText = 'Out of Stock';
                                    } elseif ($available <= $threshold) {
                                        $statusClass = 'stock-low';
                                        $statusIcon = 'bi-exclamation-triangle-fill';
                                        $statusText = 'Low Stock';
                                    } else {
                                        $statusClass = 'stock-ok';
                                        $statusIcon = 'bi-check-circle-fill';
                                        $statusText = 'In Stock';
                                    }
                                @endphp
                                <tr data-id="{{ $product->id }}">
                                    <td>
                                        <strong>{{ $product->ShortDescription }}</strong><br>
                                        <small class="text-muted">{{ $product->category->Category ?? '' }}</small>
                                    </td>
                                    <td><small>{{ $product->UPC }}</small></td>
                                    <td>{{ $product->stock_quantity }}</td>
                                    <td>{{ $product->reserved_quantity }}</td>
                                    <td><strong>{{ $available }}</strong></td>
                                    <td>${{ number_format($value, 2) }}</td>
                                    <td>
                                        <i class="bi {{ $statusIcon }} {{ $statusClass }}"></i>
                                        <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.inventory.edit', $product) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Edit inventory for {{ $product->ShortDescription }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted">No products found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    @include('components.grid.pagination', [
                        'paginator' => $products,
                        'perPage' => $filters['per_page'],
                    ])
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-3">
            {{-- Stock Alerts --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bell"></i> Stock Alerts</h6>
                </div>
                <div class="card-body">
                    @if($alerts->count() > 0)
                        @foreach($alerts as $alert)
                            <div class="alert-item {{ $alert->alert_type == 'out_of_stock' ? 'out-of-stock' : '' }}">
                                <strong>{{ $alert->product->ShortDescription ?? 'Unknown' }}</strong><br>
                                <small>
                                    @if($alert->alert_type == 'out_of_stock')
                                        <i class="bi bi-x-circle"></i> Out of stock
                                    @else
                                        <i class="bi bi-exclamation-triangle"></i> Low stock: {{ $alert->current_quantity }} remaining
                                    @endif
                                </small>
                            </div>
                        @endforeach
                        <a href="{{ route('admin.stock-alerts.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2" data-bs-toggle="tooltip" title="View and manage all stock alerts">
                            View All Alerts
                        </a>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                            <p class="mb-0">No active alerts</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.inventory.bulk-update') }}" class="btn btn-outline-primary btn-sm"
                       data-bs-toggle="tooltip" title="Update multiple products at once">
                        <i class="bi bi-box-arrow-in-down"></i> Bulk Stock Update
                    </a>
                    <a href="{{ route('admin.inventory.reports') }}" class="btn btn-outline-primary btn-sm"
                       data-bs-toggle="tooltip" title="View inventory valuation and stock reports">
                        <i class="bi bi-graph-up"></i> View Reports
                    </a>
                    <a href="{{ route('admin.stock-alerts.index') }}" class="btn btn-outline-warning btn-sm"
                       data-bs-toggle="tooltip" title="View and manage low stock alerts">
                        <i class="bi bi-bell"></i> Manage Alerts
                    </a>
                    <a href="{{ route('admin.inventory.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm"
                       data-bs-toggle="tooltip" title="Download inventory data as CSV file">
                        <i class="bi bi-download"></i> Export to CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
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
</script>
@endpush
