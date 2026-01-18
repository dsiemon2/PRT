@extends('layouts.admin')

@section('title', 'Inventory Reports')

@section('content')
<div class="page-header">
    <h1>Inventory Reports</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>
</div>

<!-- Report Selection -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card h-100 report-card" data-report="valuation" style="cursor: pointer;">
            <div class="card-body text-center">
                <i class="bi bi-currency-dollar" style="font-size: 2rem; color: var(--prt-brown);"></i>
                <h5 class="mt-2">Inventory Valuation</h5>
                <p class="small text-muted">Cost and retail value by category</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 report-card" data-report="stock_status" style="cursor: pointer;">
            <div class="card-body text-center">
                <i class="bi bi-box-seam" style="font-size: 2rem; color: var(--prt-brown);"></i>
                <h5 class="mt-2">Stock Status</h5>
                <p class="small text-muted">In stock, low stock, out of stock</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 report-card" data-report="movement" style="cursor: pointer;">
            <div class="card-body text-center">
                <i class="bi bi-arrow-left-right" style="font-size: 2rem; color: var(--prt-brown);"></i>
                <h5 class="mt-2">Stock Movement</h5>
                <p class="small text-muted">Additions and removals over time</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 report-card" data-report="low_stock" style="cursor: pointer;">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: var(--prt-brown);"></i>
                <h5 class="mt-2">Low Stock Report</h5>
                <p class="small text-muted">Items needing reorder</p>
            </div>
        </div>
    </div>
</div>

<!-- Report Display Area -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0" id="reportTitle">Inventory Valuation Report</h5>
        <button class="btn btn-outline-secondary btn-sm" onclick="exportCurrentReport()"><i class="bi bi-download"></i> Export CSV</button>
    </div>
    <div class="card-body p-0">
        <div id="reportContent" class="admin-table">
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading report...
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<nav class="mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div id="paginationInfo" class="text-muted small"></div>
        <ul class="pagination mb-0" id="pagination"></ul>
    </div>
</nav>

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
.report-card.active {
    border-color: #007bff !important;
    border-width: 2px;
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
let currentReportType = 'valuation';
let currentReportData = [];
let currentPage = 1;
let perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    // Set up report card click handlers
    document.querySelectorAll('.report-card').forEach(card => {
        card.addEventListener('click', function() {
            const reportType = this.getAttribute('data-report');
            loadReport(reportType, 1);
        });
    });

    // Load default report
    loadReport('valuation', 1);
});

async function loadReport(reportType, page = 1) {
    currentReportType = reportType;
    currentPage = page;

    // Update active card
    document.querySelectorAll('.report-card').forEach(card => {
        card.classList.remove('active');
        if (card.getAttribute('data-report') === reportType) {
            card.classList.add('active');
        }
    });

    // Update title
    const titles = {
        'valuation': 'Inventory Valuation Report',
        'stock_status': 'Stock Status Report',
        'movement': 'Stock Movement Report',
        'low_stock': 'Low Stock Report'
    };
    document.getElementById('reportTitle').textContent = titles[reportType] || 'Report';

    // Show loading
    document.getElementById('reportContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading report...
        </div>
    `;

    try {
        const response = await fetch(`${API_BASE}/admin/inventory/reports?report=${reportType}`);
        const result = await response.json();

        if (result.success && result.data) {
            currentReportData = result.data;

            // Set pagination like purchase-orders does
            const total = currentReportData.length;
            const meta = {
                current_page: 1,
                last_page: 1,
                from: 1,
                to: total,
                total: total
            };
            renderPagination(meta);

            // Then render report
            renderReport(reportType, currentReportData);
        } else {
            document.getElementById('reportContent').innerHTML = `
                <div class="text-center py-4 text-danger">Error loading report</div>
            `;
            document.getElementById('paginationInfo').textContent = '';
            document.getElementById('pagination').innerHTML = '';
        }
    } catch (error) {
        console.error('Error loading report:', error);
        document.getElementById('reportContent').innerHTML = `
            <div class="text-center py-4 text-danger">Error loading report: ${error.message}</div>
        `;
        document.getElementById('paginationInfo').textContent = '';
        document.getElementById('pagination').innerHTML = '';
    }
}

function renderReport(reportType, data) {
    let html = '';

    if (reportType === 'valuation') {
        html = renderValuationReport(data);
    } else if (reportType === 'stock_status') {
        html = renderStockStatusReport(data);
    } else if (reportType === 'movement') {
        html = renderMovementReport(data);
    } else if (reportType === 'low_stock') {
        html = renderLowStockReport(data);
    }

    document.getElementById('reportContent').innerHTML = html;
}

function renderValuationReport(data) {
    if (data.length === 0) {
        return `<div class="text-center py-4 text-muted">No data available</div>`;
    }

    let totalProducts = 0, totalUnits = 0, totalCost = 0, totalRetail = 0, totalProfit = 0;

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Products</th>
                    <th>Total Units</th>
                    <th>Cost Value</th>
                    <th>Retail Value</th>
                    <th>Potential Profit</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(row => {
        const productCount = parseInt(row.product_count) || 0;
        const totalQty = parseInt(row.total_quantity) || 0;
        const costValue = parseFloat(row.total_cost_value) || 0;
        const retailValue = parseFloat(row.total_retail_value) || 0;
        const profit = retailValue - costValue;

        totalProducts += productCount;
        totalUnits += totalQty;
        totalCost += costValue;
        totalRetail += retailValue;
        totalProfit += profit;

        html += `
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td>${row.category_name || row.Category || 'Unknown'}</td>
                <td>${productCount.toLocaleString()}</td>
                <td>${totalQty.toLocaleString()}</td>
                <td>$${costValue.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td>$${retailValue.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="text-success">$${profit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
    });

    html += `
            <tr class="table-secondary fw-bold">
                <td>Total</td>
                <td>${totalProducts.toLocaleString()}</td>
                <td>${totalUnits.toLocaleString()}</td>
                <td>$${totalCost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td>$${totalRetail.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="text-success">$${totalProfit.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        </tbody>
        </table>
    `;

    return html;
}

function renderStockStatusReport(data) {
    if (data.length === 0) {
        return `<div class="text-center py-4 text-muted">No data available</div>`;
    }

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Product Count</th>
                    <th>Total Units</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(row => {
        let statusClass = 'active';
        if (row.status === 'Out of Stock') statusClass = 'inactive';
        else if (row.status === 'Low Stock') statusClass = 'pending';

        html += `
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td><span class="status-badge ${statusClass}">${row.status}</span></td>
                <td>${(row.product_count || 0).toLocaleString()}</td>
                <td>${(row.total_units || 0).toLocaleString()}</td>
                <td>$${(row.total_value || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    return html;
}

function renderMovementReport(data) {
    if (data.length === 0) {
        return `<div class="text-center py-4 text-muted">No movement data available for the last 30 days</div>`;
    }

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Transactions</th>
                    <th>Added</th>
                    <th>Removed</th>
                    <th>Net Change</th>
                    <th>Current Stock</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(row => {
        const netChange = row.net_change || 0;
        const netClass = netChange >= 0 ? 'text-success' : 'text-danger';
        const netPrefix = netChange >= 0 ? '+' : '';

        html += `
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td>${row.ShortDescription || 'Unknown'}</td>
                <td>${row.ItemNumber || 'N/A'}</td>
                <td>${row.Category || 'N/A'}</td>
                <td>${row.transaction_count || 0}</td>
                <td class="text-success">+${(row.total_added || 0).toLocaleString()}</td>
                <td class="text-danger">-${(row.total_removed || 0).toLocaleString()}</td>
                <td class="${netClass}">${netPrefix}${netChange.toLocaleString()}</td>
                <td>${(row.current_stock || 0).toLocaleString()}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    return html;
}

function renderLowStockReport(data) {
    if (data.length === 0) {
        return `
            <div class="text-center py-4">
                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2 text-muted">No items need reordering</p>
            </div>
        `;
    }

    let html = `
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Available</th>
                    <th>Threshold</th>
                    <th>Reorder Point</th>
                    <th>Reorder Qty</th>
                    <th>Reorder Cost</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(row => {
        const available = row.available || 0;
        const availableClass = available <= 0 ? 'text-danger' : 'text-warning';

        html += `
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td>${row.ShortDescription || 'Unknown'}</td>
                <td>${row.ItemNumber || 'N/A'}</td>
                <td>${row.Category || 'N/A'}</td>
                <td class="${availableClass}"><strong>${available}</strong></td>
                <td>${row.low_stock_threshold || 0}</td>
                <td>${row.reorder_point || 0}</td>
                <td>${row.reorder_quantity || 0}</td>
                <td>$${(row.reorder_cost || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    return html;
}

function renderPagination(meta) {
    document.getElementById('paginationInfo').textContent =
        `Showing ${meta.from} to ${meta.to} of ${meta.total} entries`;

    const pagination = document.getElementById('pagination');
    let html = '';

    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadReport('${currentReportType}', ${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadReport('${currentReportType}', 1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadReport('${currentReportType}', ${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadReport('${currentReportType}', ${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadReport('${currentReportType}', ${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

async function exportCurrentReport() {
    if (!currentReportData || currentReportData.length === 0) {
        alert('No data to export');
        return;
    }

    let csv = '';
    let filename = `inventory_${currentReportType}_${new Date().toISOString().split('T')[0]}.csv`;

    if (currentReportType === 'valuation') {
        csv = 'Category,Products,Total Units,Cost Value,Retail Value,Potential Profit\n';
        currentReportData.forEach(row => {
            csv += `"${row.Category || ''}",${row.product_count || 0},${row.total_units || 0},${row.cost_value || 0},${row.retail_value || 0},${row.potential_profit || 0}\n`;
        });
    } else if (currentReportType === 'stock_status') {
        csv = 'Status,Product Count,Total Units,Total Value\n';
        currentReportData.forEach(row => {
            csv += `"${row.status || ''}",${row.product_count || 0},${row.total_units || 0},${row.total_value || 0}\n`;
        });
    } else if (currentReportType === 'movement') {
        csv = 'Product,SKU,Category,Transactions,Added,Removed,Net Change,Current Stock\n';
        currentReportData.forEach(row => {
            csv += `"${row.ShortDescription || ''}","${row.ItemNumber || ''}","${row.Category || ''}",${row.transaction_count || 0},${row.total_added || 0},${row.total_removed || 0},${row.net_change || 0},${row.current_stock || 0}\n`;
        });
    } else if (currentReportType === 'low_stock') {
        csv = 'Product,SKU,Category,Available,Threshold,Reorder Point,Reorder Qty,Reorder Cost\n';
        currentReportData.forEach(row => {
            csv += `"${row.ShortDescription || ''}","${row.ItemNumber || ''}","${row.Category || ''}",${row.available || 0},${row.low_stock_threshold || 0},${row.reorder_point || 0},${row.reorder_quantity || 0},${row.reorder_cost || 0}\n`;
        });
    }

    // Download
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
