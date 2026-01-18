@extends('layouts.admin')

@section('title', 'Drop Ship Orders')

@section('content')
<div class="page-header">
    <h1>Drop Ship Orders</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dropshippers') }}">Drop Shippers</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-box"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="value" id="stat-pending">-</div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-truck"></i>
            </div>
            <div class="value" id="stat-transit">-</div>
            <div class="label">In Transit</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value" id="stat-delivered">-</div>
            <div class="label">Delivered</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" onsubmit="return filterOrders(event)">
            <div class="col-md-2">
                <input type="text" class="form-control" id="searchInput" placeholder="Order #...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="dropshipperFilter">
                    <option value="">All Shippers</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" id="dateFilter">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-prt w-100">Filter</button>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="exportOrders()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Drop Shipper</th>
                <th>External ID</th>
                <th>Items</th>
                <th>Total</th>
                <th>Commission</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ordersTable">
            <tr>
                <td colspan="9" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading orders...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<nav class="mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div id="paginationInfo" class="text-muted small"></div>
        <ul class="pagination mb-0" id="pagination"></ul>
    </div>
</nav>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                Loading...
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="updateOrderId">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="newStatus">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3" id="trackingFields" style="display: none;">
                        <label class="form-label">Carrier</label>
                        <select class="form-select mb-2" id="carrier">
                            <option value="">Select carrier</option>
                            <option value="USPS">USPS</option>
                            <option value="UPS">UPS</option>
                            <option value="FedEx">FedEx</option>
                            <option value="DHL">DHL</option>
                        </select>
                        <label class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" id="trackingNumber">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveOrderStatus()">Save</button>
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
let orders = [];
let dropshippers = [];
let currentPage = 1;
let perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    loadOrders(1);

    // Show/hide tracking fields based on status
    document.getElementById('newStatus').addEventListener('change', function() {
        const trackingFields = document.getElementById('trackingFields');
        trackingFields.style.display = this.value === 'shipped' ? 'block' : 'none';
    });
});

async function loadOrders(page = 1) {
    currentPage = page;
    try {
        const search = document.getElementById('searchInput').value;
        const dropshipperId = document.getElementById('dropshipperFilter').value;
        const status = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;

        let url = `${API_BASE}/admin/dropship/orders?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (dropshipperId) url += `&dropshipper_id=${dropshipperId}`;
        if (status) url += `&status=${status}`;
        if (date) url += `&date=${date}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            orders = data.data;
            dropshippers = data.dropshippers;
            renderOrders();
            updateStats(data.stats);
            populateDropshipperFilter();
            renderPagination(data.meta);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('ordersTable').innerHTML = `
            <tr><td colspan="9" class="text-center text-danger">Error loading orders</td></tr>
        `;
    }
}

function updateStats(stats) {
    document.getElementById('stat-total').textContent = stats.total.toLocaleString();
    document.getElementById('stat-pending').textContent = stats.pending.toLocaleString();
    document.getElementById('stat-transit').textContent = stats.in_transit.toLocaleString();
    document.getElementById('stat-delivered').textContent = stats.delivered.toLocaleString();
}

function populateDropshipperFilter() {
    const select = document.getElementById('dropshipperFilter');
    const currentValue = select.value;

    // Keep the first option
    select.innerHTML = '<option value="">All Shippers</option>';

    dropshippers.forEach(ds => {
        const option = document.createElement('option');
        option.value = ds.id;
        option.textContent = ds.company_name;
        select.appendChild(option);
    });

    // Restore selection
    if (currentValue) {
        select.value = currentValue;
    }
}

function renderOrders() {
    const tbody = document.getElementById('ordersTable');

    if (orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No orders found</td></tr>';
        return;
    }

    tbody.innerHTML = orders.map(order => {
        const statusClass = getStatusClass(order.status || 'pending');
        const date = order.created_at ? new Date(order.created_at) : new Date();
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const status = order.status || 'pending';

        return `
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td><a href="#" onclick="viewOrderDetail(${order.id})">${order.order_number || 'N/A'}</a></td>
                <td>${order.dropshipper_name || 'Unknown'}</td>
                <td>${order.external_order_id || '-'}</td>
                <td>${order.items_count || 0}</td>
                <td>$${parseFloat(order.total_amount || 0).toFixed(2)}</td>
                <td>$${parseFloat(order.commission_amount || 0).toFixed(2)}</td>
                <td><span class="status-badge ${statusClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td>
                <td>${formattedDate}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetail(${order.id})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    ${order.status !== 'cancelled' && order.status !== 'delivered' ? `
                        <button class="btn btn-sm btn-outline-warning" onclick="showUpdateStatus(${order.id})" title="Update Status">
                            <i class="bi bi-pencil"></i>
                        </button>
                    ` : ''}
                    ${order.tracking_number ? `
                        <button class="btn btn-sm btn-outline-secondary" onclick="trackOrder('${order.tracking_number}')" title="Track">
                            <i class="bi bi-geo-alt"></i>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusClass(status) {
    switch (status) {
        case 'delivered':
        case 'shipped':
            return 'active';
        case 'processing':
            return 'pending';
        case 'pending':
            return 'pending';
        case 'cancelled':
            return 'inactive';
        default:
            return '';
    }
}

function filterOrders(event) {
    event.preventDefault();
    loadOrders(1);
    return false;
}

function renderPagination(meta) {
    const paginationInfo = document.getElementById('paginationInfo');
    const pagination = document.getElementById('pagination');

    if (!meta || meta.total === 0) {
        paginationInfo.textContent = '';
        pagination.innerHTML = '';
        return;
    }

    paginationInfo.textContent = `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total} entries`;

    let html = '';

    // Previous button
    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadOrders(${meta.current_page - 1}); return false;">Previous</a>
    </li>`;

    // Page numbers
    const totalPages = meta.last_page;
    const current = meta.current_page;

    let startPage = Math.max(1, current - 2);
    let endPage = Math.min(totalPages, current + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadOrders(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === current ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadOrders(${i}); return false;">${i}</a>
        </li>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadOrders(${totalPages}); return false;">${totalPages}</a></li>`;
    }

    // Next button
    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadOrders(${meta.current_page + 1}); return false;">Next</a>
    </li>`;

    pagination.innerHTML = html;
}

async function viewOrderDetail(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/dropship/orders/${id}`);
        const data = await response.json();

        if (data.success) {
            const order = data.data;
            const date = new Date(order.created_at);
            const formattedDate = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });

            document.getElementById('orderDetailContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Order #:</strong></td><td>${order.order_number}</td></tr>
                            <tr><td><strong>External ID:</strong></td><td>${order.external_order_id || '-'}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>${formattedDate}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="status-badge ${getStatusClass(order.status)}">${order.status}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Drop Shipper</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Company:</strong></td><td>${order.dropshipper_name}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${order.dropshipper_email}</td></tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer</h6>
                        <p>${order.customer_name || 'N/A'}<br>
                        ${order.customer_email || ''}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Shipping</h6>
                        ${order.tracking_number ? `
                            <p><strong>Carrier:</strong> ${order.carrier || 'N/A'}<br>
                            <strong>Tracking:</strong> ${order.tracking_number}</p>
                        ` : '<p>No tracking information</p>'}
                    </div>
                </div>
                <hr>
                <h6>Order Summary</h6>
                <table class="table table-sm">
                    <tr><td>Subtotal:</td><td class="text-end">$${parseFloat(order.subtotal).toFixed(2)}</td></tr>
                    <tr><td>Shipping:</td><td class="text-end">$${parseFloat(order.shipping_cost).toFixed(2)}</td></tr>
                    <tr><td>Tax:</td><td class="text-end">$${parseFloat(order.tax_amount).toFixed(2)}</td></tr>
                    <tr><td><strong>Total:</strong></td><td class="text-end"><strong>$${parseFloat(order.total_amount).toFixed(2)}</strong></td></tr>
                    <tr><td>Commission:</td><td class="text-end text-success">$${parseFloat(order.commission_amount).toFixed(2)}</td></tr>
                </table>
            `;

            new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
        }
    } catch (error) {
        console.error('Error loading order details:', error);
        alert('Error loading order details');
    }
}

function showUpdateStatus(id) {
    const order = orders.find(o => o.id === id);
    if (order) {
        document.getElementById('updateOrderId').value = id;
        document.getElementById('newStatus').value = order.status;
        document.getElementById('carrier').value = order.carrier || '';
        document.getElementById('trackingNumber').value = order.tracking_number || '';

        // Show/hide tracking fields
        document.getElementById('trackingFields').style.display =
            order.status === 'shipped' ? 'block' : 'none';

        new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
    }
}

async function saveOrderStatus() {
    const id = document.getElementById('updateOrderId').value;
    const data = {
        status: document.getElementById('newStatus').value
    };

    if (data.status === 'shipped') {
        data.carrier = document.getElementById('carrier').value;
        data.tracking_number = document.getElementById('trackingNumber').value;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/dropship/orders/${id}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
            loadOrders();
        } else {
            alert(result.message || 'Error updating order status');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        alert('Error updating order status');
    }
}

function trackOrder(trackingNumber) {
    // Generic tracking URL - could be enhanced based on carrier
    window.open(`https://www.google.com/search?q=${encodeURIComponent(trackingNumber)}+tracking`, '_blank');
}

function exportOrders() {
    alert('Export functionality coming soon');
}
</script>
@endpush
