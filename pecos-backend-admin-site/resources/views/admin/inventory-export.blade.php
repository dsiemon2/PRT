@extends('layouts.admin')

@section('title', 'Export Inventory')

@section('content')
<div class="page-header">
    <h1>Export Inventory</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Export</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <!-- Export Options -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Export Options</h5>
            </div>
            <div class="card-body">
                <form id="exportForm" class="admin-form">
                    <!-- Format -->
                    <div class="mb-3">
                        <label class="form-label">Export Format *</label>
                        <select class="form-select" id="exportFormat">
                            <option value="csv" selected>CSV (Comma Separated)</option>
                        </select>
                    </div>

                    <!-- Data to Include -->
                    <div class="mb-3">
                        <label class="form-label">Include Data</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeBasic" checked disabled>
                            <label class="form-check-label" for="includeBasic">Product Name, SKU, UPC (Required)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeStock" checked>
                            <label class="form-check-label" for="includeStock">Stock Levels (Stock, Reserved, Available)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeThreshold" checked>
                            <label class="form-check-label" for="includeThreshold">Low Stock Threshold</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includePrice" checked>
                            <label class="form-check-label" for="includePrice">Retail Price</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeCost">
                            <label class="form-check-label" for="includeCost">Cost Price</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeCategory" checked>
                            <label class="form-check-label" for="includeCategory">Category</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeValue">
                            <label class="form-check-label" for="includeValue">Total Value (Stock x Cost)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeStatus" checked>
                            <label class="form-check-label" for="includeStatus">Stock Status</label>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-3">
                        <label class="form-label">Filter By Category</label>
                        <select class="form-select" id="categoryFilter" multiple size="4">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category['CategoryCode'] ?? '' }}">{{ $category['Category'] ?? '' }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Status</label>
                        <select class="form-select" id="stockStatus">
                            <option value="">All Products</option>
                            <option value="in_stock">In Stock Only</option>
                            <option value="low_stock">Low Stock Only</option>
                            <option value="out_of_stock">Out of Stock Only</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchFilter" placeholder="Filter by product name or SKU...">
                    </div>

                    <button type="submit" class="btn btn-prt w-100" id="generateExportBtn">
                        <i class="bi bi-download"></i> Generate Export
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Export & Info -->
    <div class="col-lg-6">
        <!-- Quick Export -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Export</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Common export templates for quick access.</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary text-start" onclick="quickExport('full')">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                        Full Inventory (CSV)
                        <small class="d-block text-muted">All products with stock levels</small>
                    </button>
                    <button class="btn btn-outline-primary text-start" onclick="quickExport('low_stock')">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Low Stock Report (CSV)
                        <small class="d-block text-muted">Products below threshold</small>
                    </button>
                    <button class="btn btn-outline-primary text-start" onclick="quickExport('out_of_stock')">
                        <i class="bi bi-x-circle me-2"></i>
                        Out of Stock Report (CSV)
                        <small class="d-block text-muted">Products with zero stock</small>
                    </button>
                    <button class="btn btn-outline-primary text-start" onclick="quickExport('valuation')">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Inventory Valuation (CSV)
                        <small class="d-block text-muted">All products with cost and value</small>
                    </button>
                </div>
            </div>
        </div>

        <!-- Export Info -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Export Information</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>CSV Format</strong><br>
                    Exports are generated as CSV files that can be opened in Excel, Google Sheets, or any spreadsheet application.
                </div>
                <p class="mb-2"><strong>Available Columns:</strong></p>
                <ul class="small text-muted">
                    <li>ShortDescription - Product name</li>
                    <li>ItemNumber - SKU/Item number</li>
                    <li>UPC - Universal product code</li>
                    <li>Category - Product category</li>
                    <li>stock_quantity - Total in stock</li>
                    <li>reserved_quantity - Reserved for orders</li>
                    <li>available - Available to sell</li>
                    <li>low_stock_threshold - Alert threshold</li>
                    <li>cost_price - Cost per unit</li>
                    <li>UnitPrice - Retail price</li>
                    <li>total_value - Stock value</li>
                    <li>status - In Stock/Low Stock/Out of Stock</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p class="mb-0">Generating export...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';

// Main export form
document.getElementById('exportForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const params = new URLSearchParams();

    // Get filters
    const status = document.getElementById('stockStatus').value;
    const search = document.getElementById('searchFilter').value;
    const categories = Array.from(document.getElementById('categoryFilter').selectedOptions)
        .map(o => o.value)
        .filter(v => v);

    if (status) params.append('status', status);
    if (search) params.append('search', search);
    if (categories.length) params.append('category', categories[0]); // API supports single category

    await generateExport(params, getSelectedColumns(), 'custom_inventory_export');
});

function getSelectedColumns() {
    const columns = ['ShortDescription', 'ItemNumber', 'UPC']; // Always included

    if (document.getElementById('includeStock').checked) {
        columns.push('stock_quantity', 'reserved_quantity', 'available');
    }
    if (document.getElementById('includeThreshold').checked) {
        columns.push('low_stock_threshold');
    }
    if (document.getElementById('includePrice').checked) {
        columns.push('UnitPrice');
    }
    if (document.getElementById('includeCost').checked) {
        columns.push('cost_price');
    }
    if (document.getElementById('includeCategory').checked) {
        columns.push('Category');
    }
    if (document.getElementById('includeValue').checked) {
        columns.push('total_value');
    }
    if (document.getElementById('includeStatus').checked) {
        columns.push('status');
    }

    return columns;
}

// Quick export presets
function quickExport(type) {
    const params = new URLSearchParams();
    let columns = ['ShortDescription', 'ItemNumber', 'UPC', 'Category', 'stock_quantity', 'available', 'low_stock_threshold', 'UnitPrice', 'status'];
    let filename = 'inventory_export';

    switch(type) {
        case 'full':
            filename = 'full_inventory';
            break;
        case 'low_stock':
            params.append('status', 'low_stock');
            filename = 'low_stock_report';
            break;
        case 'out_of_stock':
            params.append('status', 'out_of_stock');
            filename = 'out_of_stock_report';
            break;
        case 'valuation':
            columns = ['ShortDescription', 'ItemNumber', 'UPC', 'Category', 'stock_quantity', 'cost_price', 'UnitPrice', 'total_value'];
            filename = 'inventory_valuation';
            break;
    }

    generateExport(params, columns, filename);
}

async function generateExport(params, columns, filename = 'inventory_export') {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();

    try {
        const response = await fetch(`${API_BASE}/admin/inventory/export-data?${params.toString()}`);
        const result = await response.json();

        if (result.success && result.data) {
            // Generate CSV
            const csv = generateCSV(result.data, columns);
            downloadCSV(csv, `${filename}_${new Date().toISOString().split('T')[0]}.csv`);
        } else {
            alert('Error: ' + (result.message || 'Failed to fetch data'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    // Hide modal after download
    setTimeout(() => loadingModal.hide(), 500);
}

function generateCSV(data, columns) {
    if (!data.length) return '';

    // Header row
    const header = columns.join(',');

    // Data rows
    const rows = data.map(item => {
        return columns.map(col => {
            let value = item[col] ?? '';
            // Escape quotes and wrap in quotes if contains comma
            if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
                value = '"' + value.replace(/"/g, '""') + '"';
            }
            return value;
        }).join(',');
    });

    return header + '\n' + rows.join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
