@extends('layouts.admin')

@section('title', 'Order Management')

@section('content')
<div class="page-header">
    <h1>Order Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-cart-check"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-clock"></i>
            </div>
            <div class="value" id="stat-pending">-</div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value" id="stat-revenue">$0</div>
            <div class="label">Total Revenue</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="value" id="stat-avg">$0</div>
            <div class="label">Avg Order Value</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search order ID...">
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
                <input type="date" class="form-control" id="fromDateFilter">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterOrders()">Filter</button>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ env('API_PUBLIC_URL', 'http://localhost:8300/api/v1') }}/admin/export/orders" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-download"></i> Export</a>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ordersTable">
            <tr>
                <td colspan="7" class="text-center">
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
let currentPage = 1;
let perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadOrders();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/orders/stats`);
        const data = await response.json();

        if (data.success || data.total_orders !== undefined) {
            const stats = data.data || data;
            document.getElementById('stat-total').textContent = stats.total_orders || 0;
            document.getElementById('stat-pending').textContent = stats.pending_orders || 0;
            document.getElementById('stat-revenue').textContent = '$' + Number(stats.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('stat-avg').textContent = '$' + Number(stats.avg_order_value || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadOrders(page = 1) {
    currentPage = page;

    try {
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('searchFilter').value;
        const fromDate = document.getElementById('fromDateFilter').value;

        let url = `${API_BASE}/admin/orders?page=${page}&per_page=${perPage}`;
        if (status) url += `&status=${status}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (fromDate) url += `&from_date=${fromDate}`;

        const response = await fetch(url);
        const data = await response.json();

        // Handle both paginated response formats
        const orders = data.data || [];
        const meta = {
            current_page: data.current_page || 1,
            last_page: data.last_page || 1,
            from: data.from || 1,
            to: data.to || orders.length,
            total: data.total || orders.length,
            per_page: data.per_page || perPage
        };

        renderOrders(orders);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('ordersTable').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger">Error loading orders</td></tr>';
    }
}

function renderOrders(orders) {
    const tbody = document.getElementById('ordersTable');

    if (orders.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="7" class="text-center py-4">
                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2 text-muted">No orders found</p>
            </td>
        </tr>`;
        return;
    }

    let html = '';
    orders.forEach(order => {
        let customerName = ((order.customer_first_name || '') + ' ' + (order.customer_last_name || '')).trim();
        if (!customerName) {
            customerName = ((order.FirstName || '') + ' ' + (order.LastName || '')).trim();
        }
        if (!customerName) {
            customerName = 'Guest';
        }

        const orderDate = order.order_date ? new Date(order.order_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'N/A';
        const total = Number(order.total_amount || order.total || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const status = order.status || 'pending';

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td><strong>#${order.order_number || order.id || 'N/A'}</strong></td>`;
        html += `<td>${customerName}</td>`;
        html += `<td>${order.item_count || '-'} items</td>`;
        html += `<td>$${total}</td>`;
        html += `<td>${orderDate}</td>`;
        html += `<td>
            <select class="form-select form-select-sm status-select" style="width: 130px;" data-order-id="${order.id}" onchange="updateStatus(this)">
                <option value="pending" ${status === 'pending' ? 'selected' : ''}>Pending</option>
                <option value="processing" ${status === 'processing' ? 'selected' : ''}>Processing</option>
                <option value="shipped" ${status === 'shipped' ? 'selected' : ''}>Shipped</option>
                <option value="delivered" ${status === 'delivered' ? 'selected' : ''}>Delivered</option>
                <option value="cancelled" ${status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
            </select>
        </td>`;
        html += `<td>
            <a href="/admin/orders/${order.id}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
            <button class="btn btn-sm btn-outline-secondary" title="Print"><i class="bi bi-printer"></i></button>
        </td>`;
        html += `</tr>`;
    });

    tbody.innerHTML = html;
}

function renderPagination(meta) {
    document.getElementById('paginationInfo').textContent =
        `Showing ${meta.from} to ${meta.to} of ${meta.total} entries`;

    const pagination = document.getElementById('pagination');
    let html = '';

    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadOrders(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadOrders(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadOrders(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadOrders(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadOrders(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterOrders() {
    loadOrders(1);
}

async function updateStatus(selectEl) {
    const orderId = selectEl.getAttribute('data-order-id');
    const newStatus = selectEl.value;

    try {
        const response = await fetch(`${API_BASE}/admin/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        });

        const result = await response.json();

        if (response.ok) {
            selectEl.classList.add('border-success');
            setTimeout(() => {
                selectEl.classList.remove('border-success');
            }, 2000);
        } else {
            alert('Error: ' + (result.message || 'Failed to update status'));
            loadOrders(currentPage);
        }
    } catch (error) {
        alert('Error: ' + error.message);
        loadOrders(currentPage);
    }
}
</script>
@endpush
