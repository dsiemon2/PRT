@extends('layouts.admin')

@section('title', 'Customer Management')

@section('content')
<div class="page-header">
    <h1>Customer Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Customers</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-people"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Customers</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-person-plus"></i>
            </div>
            <div class="value" id="stat-new">-</div>
            <div class="label">New This Month</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-cart-check"></i>
            </div>
            <div class="value" id="stat-orders">-</div>
            <div class="label">With Orders</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value" id="stat-revenue">$0</div>
            <div class="label">Total Revenue</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search customers...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="customer">Customer</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" id="fromDateFilter">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterCustomers()">Filter</button>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ env('API_PUBLIC_URL', 'http://localhost:8300/api/v1') }}/admin/export/customers" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-download"></i> Export</a>
            </div>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Orders</th>
                <th>Total Spent</th>
                <th>Joined</th>
                <th>Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="customersTable">
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading customers...
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
.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: var(--prt-brown, #8B4513);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: bold;
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
    loadCustomers();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/customers/stats`);
        const data = await response.json();

        if (data.success || data.data) {
            const stats = data.data || data;
            document.getElementById('stat-total').textContent = stats.total_customers || 0;
            document.getElementById('stat-new').textContent = stats.new_this_month || 0;
            document.getElementById('stat-orders').textContent = stats.with_orders || 0;
            document.getElementById('stat-revenue').textContent = '$' + Number(stats.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadCustomers(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const role = document.getElementById('roleFilter').value;
        const fromDate = document.getElementById('fromDateFilter').value;

        let url = `${API_BASE}/admin/customers?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (role) url += `&role=${role}`;
        if (fromDate) url += `&from_date=${fromDate}`;

        const response = await fetch(url);
        const data = await response.json();

        const customers = data.data || [];
        const meta = {
            current_page: data.current_page || 1,
            last_page: data.last_page || 1,
            from: data.from || 1,
            to: data.to || customers.length,
            total: data.total || customers.length,
            per_page: data.per_page || perPage
        };

        renderCustomers(customers);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading customers:', error);
        document.getElementById('customersTable').innerHTML =
            '<tr><td colspan="8" class="text-center text-danger">Error loading customers</td></tr>';
    }
}

function renderCustomers(customers) {
    const tbody = document.getElementById('customersTable');

    if (customers.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="8" class="text-center py-4">
                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2 text-muted">No customers found</p>
            </td>
        </tr>`;
        return;
    }

    let html = '';
    customers.forEach(customer => {
        const firstName = customer.first_name || '';
        const lastName = customer.last_name || '';
        const name = (firstName + ' ' + lastName).trim() || customer.UserName || customer.name || 'Unknown';
        const initials = name.split(' ').map(w => w.charAt(0).toUpperCase()).slice(0, 2).join('');
        const customerId = customer.id || customer.user_id || 0;
        const joinDate = customer.created_at ? new Date(customer.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'N/A';
        const totalSpent = Number(customer.total_spent || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        const role = customer.role || 'customer';

        let levelClass = 'active';
        let levelName = 'Customer';
        if (role === 'admin') { levelClass = 'danger'; levelName = 'Admin'; }
        else if (role === 'manager') { levelClass = 'warning'; levelName = 'Manager'; }

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td>
            <div class="d-flex align-items-center">
                <div class="user-avatar me-2">${initials}</div>
                <div>${name}</div>
            </div>
        </td>`;
        html += `<td>${customer.email || customer.Email || 'N/A'}</td>`;
        html += `<td>${customer.phone || customer.Phone || 'N/A'}</td>`;
        html += `<td>${customer.order_count || 0}</td>`;
        html += `<td>$${totalSpent}</td>`;
        html += `<td>${joinDate}</td>`;
        html += `<td><span class="status-badge ${levelClass}">${levelName}</span></td>`;
        html += `<td>
            <a href="/admin/customers/${customerId}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="bi bi-eye"></i></a>
            <a href="mailto:${customer.email || ''}" class="btn btn-sm btn-outline-secondary" title="Send Email"><i class="bi bi-envelope"></i></a>
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
    html += `<a class="page-link" href="#" onclick="loadCustomers(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadCustomers(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadCustomers(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadCustomers(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadCustomers(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterCustomers() {
    loadCustomers(1);
}
</script>
@endpush
