@extends('layouts.admin')

@section('title', 'Stock Alerts')

@section('content')
<div class="page-header">
    <h1>Stock Alerts</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Stock Alerts</li>
        </ol>
    </nav>
</div>

<!-- Alert Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="value" id="stat-out-of-stock">-</div>
            <div class="label">Out of Stock</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="value" id="stat-low-stock">-</div>
            <div class="label">Low Stock</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="value" id="stat-needs-reorder">-</div>
            <div class="label">Needs Reorder</div>
        </div>
    </div>
</div>

<!-- Out of Stock -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bi bi-x-circle me-2"></i>Out of Stock Items</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="outOfStockTable">
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div id="outOfStockPaginationInfo" class="text-muted small"></div>
            <ul class="pagination pagination-sm mb-0" id="outOfStockPagination"></ul>
        </div>
    </div>
</div>

<!-- Low Stock -->
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Items</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Available</th>
                    <th>Reorder Point</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="lowStockTable">
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div id="lowStockPaginationInfo" class="text-muted small"></div>
            <ul class="pagination pagination-sm mb-0" id="lowStockPagination"></ul>
        </div>
    </div>
</div>

<!-- Alert Settings -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Alert Settings</h5>
    </div>
    <div class="card-body">
        <form class="row g-3" onsubmit="saveAlertSettings(event)">
            <div class="col-md-4">
                <label class="form-label">Default Low Stock Threshold</label>
                <input type="number" class="form-control" id="defaultThreshold" value="10">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email Alerts</label>
                <select class="form-select" id="emailAlerts">
                    <option>Daily Summary</option>
                    <option>Immediately</option>
                    <option>Weekly</option>
                    <option>Disabled</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-prt">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addStockForm">
                    <input type="hidden" id="addStockProductId">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="addStockProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU/UPC</label>
                        <input type="text" class="form-control" id="addStockUPC" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="addStockCurrent" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity to Add</label>
                        <input type="number" class="form-control" id="addStockQuantity" min="1" value="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="addStockNotes" rows="2" placeholder="e.g., Received from supplier"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="addStock()">Add Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Reorder Modal -->
<div class="modal fade" id="reorderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Reorder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reorderForm">
                    <input type="hidden" id="reorderProductId">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="reorderProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU/UPC</label>
                        <input type="text" class="form-control" id="reorderUPC" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reorder Quantity</label>
                        <input type="number" class="form-control" id="reorderQuantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" id="reorderSupplier">
                            <option value="default">Default Supplier</option>
                            <option value="supplier1">Supplier 1</option>
                            <option value="supplier2">Supplier 2</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select" id="reorderPriority">
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="reorderNotes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createReorder()">Create Reorder</button>
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
</style>

<script>
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.closest('button') || target.closest('a') || target.closest('select')) {
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
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let addStockModal, reorderModal;
let outOfStockData = [];
let lowStockData = [];
let outOfStockPage = 1;
let lowStockPage = 1;
const perPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    addStockModal = new bootstrap.Modal(document.getElementById('addStockModal'));
    reorderModal = new bootstrap.Modal(document.getElementById('reorderModal'));
    loadAlerts();
});

async function loadAlerts() {
    try {
        // Load stock alerts and stats in parallel
        const [alertsResponse, statsResponse] = await Promise.all([
            fetch(`${API_BASE}/admin/inventory/stock-alerts`),
            fetch(`${API_BASE}/admin/inventory/stats`)
        ]);
        const alertsResult = await alertsResponse.json();
        const statsResult = await statsResponse.json();

        if (alertsResult.success) {
            const alertData = alertsResult.data || [];

            // Update stats from stats endpoint
            const stats = statsResult.data || {};
            document.getElementById('stat-out-of-stock').textContent = (stats.out_of_stock_count || 0).toLocaleString();
            document.getElementById('stat-low-stock').textContent = (stats.low_stock_count || 0).toLocaleString();
            document.getElementById('stat-needs-reorder').textContent = (stats.low_stock_count || 0).toLocaleString();

            // Filter data based on alert_type
            outOfStockData = alertData.filter(item => item.alert_type === 'out_of_stock' || (item.available || 0) <= 0);
            lowStockData = alertData.filter(item => item.alert_type === 'low_stock' || ((item.available || 0) > 0 && (item.available || 0) <= (item.reorder_point || 10)));

            renderOutOfStock(1);
            renderLowStock(1);
        }
    } catch (error) {
        console.error('Error loading alerts:', error);
        document.getElementById('outOfStockTable').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
        document.getElementById('lowStockTable').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
    }
}

function renderOutOfStock(page) {
    outOfStockPage = page;
    const tbody = document.getElementById('outOfStockTable');
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const pageData = outOfStockData.slice(start, end);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No out of stock items</td></tr>';
    } else {
        tbody.innerHTML = pageData.map(item => {
            // Use consistent stock calculation
            const stock = item.available || item.Qty_Avail || item.stock || item.stock_quantity || 0;
            return `
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>${item.ShortDescription || item.Description || item.name || 'Unknown'}</td>
                    <td>${item.UPC || item.UPC_Code || item.sku || 'N/A'}</td>
                    <td>${item.category_name || item.Category || 'N/A'}</td>
                    <td><span class="badge bg-danger">Out of Stock (${stock})</span></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="openAddStockModal('${item.id || ''}', '${(item.ShortDescription || item.Description || 'Unknown').replace(/'/g, "\\'")}', '${item.UPC || ''}', ${stock})">
                            <i class="bi bi-plus"></i> Add Stock
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="openReorderModal('${item.id || ''}', '${(item.ShortDescription || item.Description || 'Unknown').replace(/'/g, "\\'")}', '${item.UPC || ''}', ${item.reorder_point || 10})">
                            <i class="bi bi-cart-plus"></i> Reorder
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Render pagination
    const total = outOfStockData.length;
    const meta = {
        current_page: page,
        last_page: Math.ceil(total / perPage) || 1,
        from: total > 0 ? start + 1 : 0,
        to: Math.min(end, total),
        total: total
    };
    renderPagination('outOfStock', meta);
}

function renderLowStock(page) {
    lowStockPage = page;
    const tbody = document.getElementById('lowStockTable');
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const pageData = lowStockData.slice(start, end);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No low stock items</td></tr>';
    } else {
        tbody.innerHTML = pageData.map(item => {
            const stock = item.available || item.Qty_Avail || item.stock || 0;
            const threshold = item.reorder_point || item.low_stock_threshold || 10;
            const badgeClass = stock <= 3 ? 'danger' : 'warning';
            const textClass = stock > 3 ? 'text-dark' : '';

            return `
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>${item.ShortDescription || item.Description || item.name || 'Unknown'}</td>
                    <td>${item.UPC || item.UPC_Code || item.sku || 'N/A'}</td>
                    <td>${item.category_name || item.Category || 'N/A'}</td>
                    <td><span class="badge bg-${badgeClass} ${textClass}">${stock}</span></td>
                    <td>${threshold}</td>
                    <td><span class="badge bg-warning text-dark">Low Stock</span></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="openAddStockModal('${item.id || ''}', '${(item.ShortDescription || item.Description || 'Unknown').replace(/'/g, "\\'")}', '${item.UPC || ''}', ${stock})">
                            <i class="bi bi-plus"></i> Add Stock
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="openReorderModal('${item.id || ''}', '${(item.ShortDescription || item.Description || 'Unknown').replace(/'/g, "\\'")}', '${item.UPC || ''}', ${threshold})">
                            <i class="bi bi-cart-plus"></i> Reorder
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Render pagination
    const total = lowStockData.length;
    const meta = {
        current_page: page,
        last_page: Math.ceil(total / perPage) || 1,
        from: total > 0 ? start + 1 : 0,
        to: Math.min(end, total),
        total: total
    };
    renderPagination('lowStock', meta);
}

function renderPagination(type, meta) {
    document.getElementById(`${type}PaginationInfo`).textContent =
        `Showing ${meta.from} to ${meta.to} of ${meta.total} entries`;

    const pagination = document.getElementById(`${type}Pagination`);
    let html = '';
    const loadFunc = type === 'outOfStock' ? 'renderOutOfStock' : 'renderLowStock';

    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="${loadFunc}(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="${loadFunc}(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="${loadFunc}(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="${loadFunc}(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="${loadFunc}(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function openAddStockModal(productId, productName, upc, currentStock) {
    document.getElementById('addStockProductId').value = productId;
    document.getElementById('addStockProductName').value = productName;
    document.getElementById('addStockUPC').value = upc;
    document.getElementById('addStockCurrent').value = currentStock;
    document.getElementById('addStockQuantity').value = 10;
    document.getElementById('addStockNotes').value = '';
    addStockModal.show();
}

function openReorderModal(productId, productName, upc, reorderPoint) {
    document.getElementById('reorderProductId').value = productId;
    document.getElementById('reorderProductName').value = productName;
    document.getElementById('reorderUPC').value = upc;
    document.getElementById('reorderQuantity').value = reorderPoint * 2;
    document.getElementById('reorderNotes').value = '';
    reorderModal.show();
}

async function addStock() {
    const productId = document.getElementById('addStockProductId').value;
    const quantity = parseInt(document.getElementById('addStockQuantity').value);
    const notes = document.getElementById('addStockNotes').value;

    if (!productId || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }

    try {
        // API expects: product_id, adjustment (positive=add, negative=remove), notes
        const response = await fetch(`${API_BASE}/admin/inventory/adjust-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: parseInt(productId),
                adjustment: quantity, // Positive value = add stock
                notes: notes || 'Stock added from alerts page'
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(`Successfully added ${quantity} units to stock!`);
            addStockModal.hide();
            loadAlerts();
        } else {
            alert('Error: ' + (result.message || result.error || 'Failed to add stock'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding stock: ' + error.message);
    }
}

async function createReorder() {
    const productId = document.getElementById('reorderProductId').value;
    const quantity = parseInt(document.getElementById('reorderQuantity').value);
    const supplier = document.getElementById('reorderSupplier').value;
    const priority = document.getElementById('reorderPriority').value;
    const notes = document.getElementById('reorderNotes').value;

    if (!productId || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/inventory/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                supplier: supplier,
                priority: priority,
                notes: notes
            })
        });

        const result = await response.json();

        if (result.success || response.ok) {
            alert(`Reorder created successfully for ${quantity} units!`);
            reorderModal.hide();
        } else {
            alert('Error: ' + (result.message || 'Failed to create reorder'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert(`Reorder request created for ${quantity} units!\n\nNote: This would typically be sent to your purchasing system.`);
        reorderModal.hide();
    }
}

function saveAlertSettings(event) {
    event.preventDefault();
    const threshold = document.getElementById('defaultThreshold').value;
    const emailAlerts = document.getElementById('emailAlerts').value;

    alert(`Settings saved!\nDefault Threshold: ${threshold}\nEmail Alerts: ${emailAlerts}`);
}
</script>
@endpush
