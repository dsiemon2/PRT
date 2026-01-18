@extends('layouts.admin')

@section('title', 'Edit Inventory - ' . $product->ShortDescription)

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-pencil"></i> Edit Inventory</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($product->ShortDescription, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $product->ShortDescription }}</h5>
                    <small class="text-muted">UPC: {{ $product->UPC }} | Item #: {{ $product->ItemNumber }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control"
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reserved Quantity</label>
                                <input type="number" name="reserved_quantity" class="form-control"
                                       value="{{ old('reserved_quantity', $product->reserved_quantity) }}" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Low Stock Threshold</label>
                                <input type="number" name="low_stock_threshold" class="form-control"
                                       value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reorder Point</label>
                                <input type="number" name="reorder_point" class="form-control"
                                       value="{{ old('reorder_point', $product->reorder_point) }}" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Price ($)</label>
                                <input type="number" name="cost_price" class="form-control" step="0.01"
                                       value="{{ old('cost_price', $product->cost_price) }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Track Inventory</label>
                                <select name="track_inventory" class="form-select">
                                    <option value="1" {{ $product->track_inventory ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !$product->track_inventory ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adjustment Reason (optional)</label>
                            <textarea name="adjustment_reason" class="form-control" rows="2"
                                      placeholder="Reason for this adjustment..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Save all inventory changes">
                                <i class="bi bi-check"></i> Save Changes
                            </button>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Cancel changes and return to inventory list">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Current Status --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Current Status</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Available:</td>
                            <td><strong>{{ $product->availableQuantity }}</strong></td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td>
                                @php $status = $product->stockStatus; @endphp
                                <span class="badge bg-{{ $status == 'in_stock' ? 'success' : ($status == 'low_stock' ? 'warning' : 'danger') }}">
                                    {{ str_replace('_', ' ', ucwords($status)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Last Restock:</td>
                            <td>{{ $product->last_restock_date ? \Carbon\Carbon::parse($product->last_restock_date)->format('M j, Y') : 'Never' }}</td>
                        </tr>
                        <tr>
                            <td>Unit Price:</td>
                            <td>${{ number_format($product->UnitPrice, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Transactions</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @forelse($transactions as $tx)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong class="{{ $tx->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->quantity_change > 0 ? '+' : '' }}{{ $tx->quantity_change }}
                                </strong>
                                <small class="text-muted">{{ $tx->created_at->format('M j, Y') }}</small>
                            </div>
                            <small class="text-muted">{{ $tx->notes ?? $tx->transaction_type }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No transactions yet</p>
                    @endforelse
                </div>
            </div>
        </div>
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
