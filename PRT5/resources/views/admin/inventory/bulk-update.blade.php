@extends('layouts.admin')

@section('title', 'Bulk Inventory Update')

@section('styles')
<style>
    .bulk-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory.index') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Bulk Update</li>
        </ol>
    </nav>

    <h1><i class="bi bi-box-arrow-in-down"></i> Bulk Inventory Update</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('upload_results'))
        <div class="bulk-card">
            <h4>Upload Results</h4>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Item Number</th>
                        <th>Adjustment</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('upload_results') as $result)
                        <tr class="{{ $result['status'] == 'success' ? 'table-success' : 'table-danger' }}">
                            <td>
                                @if($result['status'] == 'success')
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-danger"></i>
                                @endif
                            </td>
                            <td>{{ $result['item'] }}</td>
                            <td>{{ $result['adjustment'] }}</td>
                            <td>{{ $result['message'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="row">
        <!-- CSV Upload -->
        <div class="col-md-6">
            <div class="bulk-card">
                <h4><i class="bi bi-file-earmark-spreadsheet"></i> Upload CSV File</h4>
                <p class="text-muted">Upload a CSV file to update multiple products at once.</p>

                <div class="alert alert-info">
                    <strong>CSV Format:</strong>
                    <pre class="mb-0">ItemNumber,Adjustment,Notes
ITEM001,+50,Received shipment
ITEM002,-10,Damaged inventory</pre>
                    <small>First row should be headers. Use + for additions, - for removals.</small>
                </div>

                <form action="{{ route('admin.inventory.bulk-update.csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="upload_csv">

                    <div class="mb-3">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>

                    <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Upload CSV file and process bulk inventory updates">
                        <i class="bi bi-upload"></i> Upload and Process
                    </button>
                </form>

                <hr>

                <div>
                    <h6>Download Template</h6>
                    <a href="data:text/csv;charset=utf-8,ItemNumber,Adjustment,Notes%0AITEM001,+50,Received shipment%0AITEM002,-10,Damaged inventory"
                       download="inventory_bulk_template.csv"
                       class="btn btn-sm btn-outline-secondary"
                       data-bs-toggle="tooltip" title="Download sample CSV template for bulk inventory updates">
                        <i class="bi bi-download"></i> Download CSV Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Manual Bulk Selection -->
        <div class="col-md-6">
            <div class="bulk-card">
                <h4><i class="bi bi-check2-square"></i> Select Products Manually</h4>
                <p class="text-muted">Select multiple products and apply the same adjustment.</p>

                <!-- Search/Filter -->
                <form method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Search products..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->CategoryCode }}"
                                            {{ request('category') == $cat->CategoryCode ? 'selected' : '' }}>
                                        {{ $cat->Category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100" data-bs-toggle="tooltip" title="Search and filter products">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin.inventory.bulk-update.manual') }}" method="POST" id="bulkAdjustForm">
                    @csrf
                    <input type="hidden" name="action" value="bulk_adjust">

                    <div class="mb-3" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                        @foreach($products as $product)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="product_ids[]"
                                       value="{{ $product->ID }}"
                                       id="product_{{ $product->ID }}">
                                <label class="form-check-label" for="product_{{ $product->ID }}">
                                    <strong>{{ $product->ItemNumber }}</strong> -
                                    {{ $product->ShortDescription }}
                                    <small class="text-muted">(Current: {{ $product->stock_quantity }})</small>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjustment Amount</label>
                        <input type="number" name="adjustment" class="form-control"
                               placeholder="Enter + or - amount (e.g., +50 or -10)" required>
                        <small class="text-muted">Positive to add, negative to remove</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Reason for adjustment"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success" onclick="return confirmBulkUpdate()" data-bs-toggle="tooltip" title="Apply the adjustment amount to all selected products">
                        <i class="bi bi-check"></i> Apply to Selected Products
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to inventory dashboard">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
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

function confirmBulkUpdate() {
    const checked = document.querySelectorAll('input[name="product_ids[]"]:checked');
    if (checked.length === 0) {
        alert('Please select at least one product');
        return false;
    }
    return confirm(`Apply adjustment to ${checked.length} selected product(s)?`);
}
</script>
@endpush
