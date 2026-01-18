@extends('layouts.admin')

@section('title', 'Stock Alerts')

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
    .alert-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .alert-item {
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff3cd;
        border-radius: 4px;
    }
    .alert-item.out-of-stock,
    .alert-item.out_of_stock {
        border-left-color: #dc3545;
        background: #f8d7da;
    }
    .alert-item.resolved {
        border-left-color: #28a745;
        background: #d4edda;
        opacity: 0.7;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-bell"></i> Stock Alerts Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Stock Alerts</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Statistics --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="text-muted">Total Alerts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-warning">{{ number_format($stats['active'] ?? 0) }}</div>
                <div class="text-muted">Active Alerts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-danger">{{ number_format($stats['out_of_stock'] ?? 0) }}</div>
                <div class="text-muted">Out of Stock</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-success">{{ number_format($stats['resolved'] ?? 0) }}</div>
                <div class="text-muted">Resolved</div>
            </div>
        </div>
    </div>

    {{-- Filters and Actions --}}
    <div class="alert-card mb-4">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h4 class="mb-0"><i class="bi bi-filter"></i> Filter Alerts</h4>
            </div>
            <div class="col-md-6 text-end">
                @if(($stats['active'] ?? 0) > 0)
                    <form action="{{ route('admin.stock-alerts.bulk-resolve') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="resolve_all" value="1">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Mark all active alerts as resolved?')" data-bs-toggle="tooltip" title="Mark all active alerts as resolved">
                            <i class="bi bi-check-all"></i> Resolve All
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to inventory dashboard">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ ($filters['status'] ?? 'active') == 'active' ? 'selected' : '' }}>Active Alerts</option>
                    <option value="resolved" {{ ($filters['status'] ?? '') == 'resolved' ? 'selected' : '' }}>Resolved Alerts</option>
                    <option value="all" {{ ($filters['status'] ?? '') == 'all' ? 'selected' : '' }}>All Alerts</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Alert Type</label>
                <select name="type" class="form-select">
                    <option value="all" {{ ($filters['type'] ?? 'all') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="low_stock" {{ ($filters['type'] ?? '') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ ($filters['type'] ?? '') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Apply selected filter criteria">
                    <i class="bi bi-search"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Alerts List --}}
    <div class="alert-card">
        <h4 class="mb-4">
            <i class="bi bi-list-ul"></i> Alert Details
            <span class="badge bg-secondary">{{ $alerts->total() }} alerts</span>
        </h4>

        @if($alerts->count() > 0)
            @foreach($alerts as $alert)
                <div class="alert-item {{ $alert->alert_type }} {{ $alert->is_resolved ? 'resolved' : '' }}">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-2">
                                @if($alert->alert_type == 'out_of_stock')
                                    <i class="bi bi-x-circle text-danger"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                @endif
                                {{ $alert->product->ShortDescription ?? 'Unknown Product' }}
                                @if($alert->is_resolved)
                                    <span class="badge bg-success">Resolved</span>
                                @endif
                            </h5>
                            <p class="mb-1">
                                <strong>Item #:</strong> {{ $alert->product->ItemNumber ?? 'N/A' }} |
                                <strong>UPC:</strong> {{ $alert->product->UPC ?? 'N/A' }} |
                                <strong>Category:</strong> {{ $alert->product->category->Category ?? 'N/A' }}
                            </p>
                            <p class="mb-1">
                                <strong>Alert Type:</strong>
                                <span class="badge {{ $alert->alert_type == 'out_of_stock' ? 'bg-danger' : 'bg-warning' }}">
                                    {{ str_replace('_', ' ', ucwords($alert->alert_type)) }}
                                </span>
                            </p>
                            <p class="mb-1">
                                <strong>Current Stock:</strong> {{ $alert->current_quantity ?? $alert->product->availableQuantity ?? 0 }} available
                                ({{ $alert->product->stock_quantity ?? 0 }} total - {{ $alert->product->reserved_quantity ?? 0 }} reserved)
                                | <strong>Threshold:</strong> {{ $alert->threshold ?? $alert->product->low_stock_threshold ?? 5 }}
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-clock"></i> Alert created: {{ $alert->created_at->format('M d, Y g:i A') }}
                                @if($alert->is_resolved && $alert->resolved_at)
                                    | Resolved: {{ $alert->resolved_at->format('M d, Y g:i A') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.inventory.edit', $alert->product_id) }}" class="btn btn-primary btn-sm mb-2" data-bs-toggle="tooltip" title="Edit product stock levels and settings">
                                <i class="bi bi-pencil"></i> Edit Stock
                            </a>
                            @if(!$alert->is_resolved)
                                <form action="{{ route('admin.stock-alerts.update', $alert) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_resolved" value="1">
                                    <button type="submit" class="btn btn-success btn-sm mb-2" data-bs-toggle="tooltip" title="Mark this alert as resolved">
                                        <i class="bi bi-check"></i> Mark Resolved
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Alerts Found</h4>
                <p class="text-muted">There are no stock alerts matching your filters.</p>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Return to inventory dashboard">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        @endif

        {{-- Pagination --}}
        @if($alerts->hasPages())
            <nav class="mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $alerts->firstItem() ?? 0 }} to {{ $alerts->lastItem() ?? 0 }} of {{ $alerts->total() }} alerts
                    </div>
                    <ul class="pagination mb-0">
                        <li class="page-item {{ $alerts->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $alerts->previousPageUrl() }}" data-bs-toggle="tooltip" title="Go to previous page">Previous</a>
                        </li>

                        @php
                            $currentPage = $alerts->currentPage();
                            $lastPage = $alerts->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <li class="page-item"><a class="page-link" href="{{ $alerts->url(1) }}">1</a></li>
                            @if($startPage > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for($i = $startPage; $i <= $endPage; $i++)
                            <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                <a class="page-link" href="{{ $alerts->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $alerts->url($lastPage) }}">{{ $lastPage }}</a></li>
                        @endif

                        <li class="page-item {{ !$alerts->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $alerts->nextPageUrl() }}" data-bs-toggle="tooltip" title="Go to next page">Next</a>
                        </li>
                    </ul>
                </div>
            </nav>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
