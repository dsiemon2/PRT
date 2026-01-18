@extends('layouts.admin')

@section('title', 'Bulk Stock Update')

@section('content')
<div class="page-header">
    <h1>Bulk Stock Update</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Bulk Update</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <!-- CSV Upload -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV Upload</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Upload a CSV file to bulk update stock quantities.</p>

                <div class="border rounded p-4 text-center mb-3" style="background: #f8f9fa; border-style: dashed !important;" id="csvDropZone">
                    <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #999;"></i>
                    <p class="mb-2 mt-2">Drag & drop your CSV file here</p>
                    <p class="small text-muted mb-3">or</p>
                    <input type="file" class="form-control" id="csvFileInput" accept=".csv" style="max-width: 300px; margin: 0 auto;">
                </div>

                <div id="csvPreview" class="mb-3" style="display: none;">
                    <h6>Preview:</h6>
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm" id="csvPreviewTable">
                            <thead><tr><th>Item #</th><th>Adjustment</th><th>Notes</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <p class="small text-muted mt-2"><span id="csvRowCount">0</span> rows loaded</p>
                </div>

                <div class="alert alert-info">
                    <strong>CSV Format:</strong><br>
                    <code>item_number, adjustment, notes</code><br>
                    <small>Example: MWS001, +50, "Restocked from supplier"</small>
                </div>

                <a href="#" class="btn btn-outline-secondary btn-sm" id="downloadTemplate">
                    <i class="bi bi-download"></i> Download Template
                </a>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-prt w-100" id="processCsvBtn" disabled>
                    <i class="bi bi-upload"></i> Process CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Manual Bulk Update -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Manual Bulk Update</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Select multiple products and apply the same adjustment.</p>

                <!-- Search/Filter -->
                <div class="row g-2 mb-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control form-control-sm" id="productSearch" placeholder="Search products...">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category['CategoryCode'] ?? $category->CategoryCode ?? '' }}">{{ $category['Category'] ?? $category->Category ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Products <small class="text-muted">({{ count($products) }} available)</small></label>
                    <select class="form-select" id="productSelect" multiple size="6">
                        @foreach($products as $product)
                        <option value="{{ $product['id'] ?? $product->id ?? '' }}" data-stock="{{ $product['stock_quantity'] ?? $product->stock_quantity ?? 0 }}" data-category="{{ $product['Category'] ?? $product->Category ?? '' }}">
                            {{ $product['ItemNumber'] ?? $product->ItemNumber ?? '' }} - {{ $product['ShortDescription'] ?? $product->ShortDescription ?? '' }} (Stock: {{ $product['stock_quantity'] ?? $product->stock_quantity ?? 0 }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple. <span id="selectedCount">0</span> selected.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Adjustment Type</label>
                    <select class="form-select" id="adjustmentType">
                        <option value="add">Add to Stock (+)</option>
                        <option value="subtract">Subtract from Stock (-)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="adjustmentQty" min="1" value="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" id="adjustmentNotes" rows="2" placeholder="Reason for adjustment..."></textarea>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-prt w-100" id="applyManualBtn">
                    <i class="bi bi-check-circle"></i> Apply to Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resultsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let csvData = [];

// Product search/filter
document.getElementById('productSearch').addEventListener('input', filterProducts);
document.getElementById('categoryFilter').addEventListener('change', filterProducts);

function filterProducts() {
    const search = document.getElementById('productSearch').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const select = document.getElementById('productSelect');

    Array.from(select.options).forEach(option => {
        const text = option.text.toLowerCase();
        const optCategory = option.dataset.category || '';
        const matchSearch = !search || text.includes(search);
        const matchCategory = !category || optCategory === category;
        option.style.display = (matchSearch && matchCategory) ? '' : 'none';
    });
}

// Track selected count
document.getElementById('productSelect').addEventListener('change', function() {
    document.getElementById('selectedCount').textContent = this.selectedOptions.length;
});

// CSV file handling
document.getElementById('csvFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) parseCSV(file);
});

// Drag and drop
const dropZone = document.getElementById('csvDropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.background = '#e3f2fd'; });
dropZone.addEventListener('dragleave', e => { dropZone.style.background = '#f8f9fa'; });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.background = '#f8f9fa';
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.csv')) parseCSV(file);
});

function parseCSV(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        const lines = text.split('\n').filter(l => l.trim());
        csvData = [];
        const tbody = document.querySelector('#csvPreviewTable tbody');
        tbody.innerHTML = '';

        // Skip header if present
        const startIdx = lines[0].toLowerCase().includes('item') ? 1 : 0;

        for (let i = startIdx; i < lines.length; i++) {
            const parts = lines[i].split(',').map(p => p.trim().replace(/^["']|["']$/g, ''));
            if (parts.length >= 2) {
                const item = {
                    item_number: parts[0],
                    adjustment: parseInt(parts[1]) || 0,
                    notes: parts[2] || ''
                };
                csvData.push(item);

                const row = tbody.insertRow();
                row.insertCell().textContent = item.item_number;
                row.insertCell().textContent = (item.adjustment > 0 ? '+' : '') + item.adjustment;
                row.insertCell().textContent = item.notes;
            }
        }

        document.getElementById('csvPreview').style.display = 'block';
        document.getElementById('csvRowCount').textContent = csvData.length;
        document.getElementById('processCsvBtn').disabled = csvData.length === 0;
    };
    reader.readAsText(file);
}

// Download template
document.getElementById('downloadTemplate').addEventListener('click', function(e) {
    e.preventDefault();
    const csv = 'item_number,adjustment,notes\nMWS001,50,Restocked from supplier\nMWS002,-5,Damaged items removed\nMWS003,100,New shipment arrived';
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'bulk_update_template.csv';
    a.click();
});

// Process CSV
document.getElementById('processCsvBtn').addEventListener('click', async function() {
    if (csvData.length === 0) return;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

    try {
        const response = await fetch(`${API_BASE}/admin/inventory/bulk-adjust-csv`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: csvData })
        });

        const result = await response.json();
        showResults(result);
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = '<i class="bi bi-upload"></i> Process CSV';
});

// Manual bulk update
document.getElementById('applyManualBtn').addEventListener('click', async function() {
    const select = document.getElementById('productSelect');
    const selectedIds = Array.from(select.selectedOptions).map(o => parseInt(o.value));

    if (selectedIds.length === 0) {
        alert('Please select at least one product');
        return;
    }

    const type = document.getElementById('adjustmentType').value;
    let qty = parseInt(document.getElementById('adjustmentQty').value) || 0;
    const notes = document.getElementById('adjustmentNotes').value;

    if (type === 'subtract') qty = -qty;

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Applying...';

    try {
        const response = await fetch(`${API_BASE}/admin/inventory/bulk-adjust-manual`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_ids: selectedIds,
                adjustment: qty,
                notes: notes
            })
        });

        const result = await response.json();
        showResults(result);

        // Reload page to refresh product list
        if (result.success) {
            setTimeout(() => location.reload(), 2000);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }

    this.disabled = false;
    this.innerHTML = '<i class="bi bi-check-circle"></i> Apply to Selected';
});

function showResults(result) {
    let html = '';

    if (result.success) {
        html += `<div class="alert alert-success"><i class="bi bi-check-circle"></i> ${result.message}</div>`;

        if (result.data?.success_count !== undefined) {
            html += `<p><strong>Successful:</strong> ${result.data.success_count} products</p>`;
            html += `<p><strong>Errors:</strong> ${result.data.error_count} products</p>`;
        }

        if (result.data?.results) {
            html += '<table class="table table-sm"><thead><tr><th>Item</th><th>Adjustment</th><th>Status</th><th>Message</th></tr></thead><tbody>';
            result.data.results.forEach(r => {
                const statusClass = r.status === 'success' ? 'text-success' : 'text-danger';
                html += `<tr><td>${r.item}</td><td>${r.adjustment > 0 ? '+' : ''}${r.adjustment}</td><td class="${statusClass}">${r.status}</td><td>${r.message}</td></tr>`;
            });
            html += '</tbody></table>';
        }
    } else {
        html += `<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ${result.message || 'An error occurred'}</div>`;
    }

    document.getElementById('resultsContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('resultsModal')).show();
}
</script>
@endpush
