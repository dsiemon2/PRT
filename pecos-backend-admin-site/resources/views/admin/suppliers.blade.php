@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<div class="page-header">
    <h1>Supplier Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Suppliers</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-truck"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Suppliers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value" id="stat-active">-</div>
            <div class="label">Active Partners</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="value" id="stat-orders">-</div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value" id="stat-revenue">-</div>
            <div class="label">Total Revenue</div>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" onsubmit="return filterSuppliers(event)">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Search by name or email...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-prt w-100">Filter</button>
            </div>
            <div class="col-md-5 text-end">
                <button type="button" class="btn btn-success" onclick="showAddModal()">
                    <i class="bi bi-plus"></i> Add Supplier
                </button>
                <a href="{{ route('admin.purchase.orders') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-box"></i> View Purchase Orders
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Suppliers Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Company</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Commission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="SuppliersTable">
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading Suppliers...
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="SupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="SupplierForm">
                    <input type="hidden" id="SupplierId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Name *</label>
                            <input type="text" class="form-control" id="companyName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Name</label>
                            <input type="text" class="form-control" id="contactName">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Rate (%)</label>
                            <input type="number" class="form-control" id="commissionRate" step="0.01" min="0" max="100" value="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" id="addressLine1" placeholder="Address Line 1">
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control" id="addressLine2" placeholder="Address Line 2">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="city" placeholder="City">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="state" placeholder="State">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="postalCode" placeholder="Postal Code">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSupplier()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- API Key Modal -->
<div class="modal fade" id="apiKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">API Key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>API Key for <strong id="apiKeyCompany"></strong>:</p>
                <div class="input-group">
                    <input type="text" class="form-control" id="apiKeyValue" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-warning btn-sm" onclick="regenerateApiKey()">
                        <i class="bi bi-arrow-clockwise"></i> Regenerate Key
                    </button>
                </div>
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
let Suppliers = [];
let currentSupplierId = null;
let currentPage = 1;
let perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    loadSuppliers();
});

async function loadSuppliers(page = 1) {
    currentPage = page;
    try {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/suppliers?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            Suppliers = data.data;
            // Set pagination FIRST so it always shows
            renderPagination(data.meta);
            renderSuppliers();
            updateStats(data.stats);
        }
    } catch (error) {
        console.error('Error loading Suppliers:', error);
        document.getElementById('SuppliersTable').innerHTML = `
            <tr><td colspan="7" class="text-center text-danger">Error loading Suppliers</td></tr>
        `;
    }
}

function updateStats(stats) {
    if (!stats) return;
    document.getElementById('stat-total').textContent = (stats.total || 0).toLocaleString();
    document.getElementById('stat-active').textContent = (stats.active || 0).toLocaleString();
    document.getElementById('stat-orders').textContent = (stats.total_orders || 0).toLocaleString();
    document.getElementById('stat-revenue').textContent = '$' + parseFloat(stats.total_revenue || stats.total_amount || 0).toLocaleString();
}

function renderSuppliers() {
    const tbody = document.getElementById('SuppliersTable');

    if (Suppliers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No Suppliers found</td></tr>';
        return;
    }

    tbody.innerHTML = Suppliers.map(ds => `
        <tr onclick="highlightRow(event)" style="cursor: pointer;">
            <td>
                <strong>${ds.company_name || 'Unknown'}</strong><br>
                <small class="text-muted">${ds.contact_name || 'No contact'}</small>
            </td>
            <td>
                ${ds.email || '-'}<br>
                <small class="text-muted">${ds.phone || ''}</small>
            </td>
            <td>
                <span class="status-badge ${(ds.status || 'pending') === 'active' ? 'active' : (ds.status || 'pending') === 'pending' ? 'pending' : 'inactive'}">
                    ${(ds.status || 'pending').charAt(0).toUpperCase() + (ds.status || 'pending').slice(1)}
                </span>
            </td>
            <td>${ds.total_orders || 0}</td>
            <td>$${parseFloat(ds.total_amount || 0).toLocaleString()}</td>
            <td>${ds.payment_terms || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="editSupplier(${ds.id})" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="showApiKey(${ds.id})" title="View API Key">
                    <i class="bi bi-key"></i>
                </button>
                ${ds.status === 'pending' ? `
                    <button class="btn btn-sm btn-outline-success" onclick="approveSupplier(${ds.id})" title="Approve">
                        <i class="bi bi-check-circle"></i>
                    </button>
                ` : ds.status === 'active' ? `
                    <button class="btn btn-sm btn-outline-danger" onclick="toggleSuspend(${ds.id})" title="Suspend">
                        <i class="bi bi-pause-circle"></i>
                    </button>
                ` : `
                    <button class="btn btn-sm btn-outline-success" onclick="toggleSuspend(${ds.id})" title="Reactivate">
                        <i class="bi bi-play-circle"></i>
                    </button>
                `}
                <button class="btn btn-sm btn-outline-danger" onclick="deleteSupplier(${ds.id})" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function filterSuppliers(event) {
    event.preventDefault();
    loadSuppliers(1);
    return false;
}

function renderPagination(meta) {
    if (!meta) {
        document.getElementById('paginationInfo').textContent = '';
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    document.getElementById('paginationInfo').textContent =
        `Showing ${meta.from} to ${meta.to} of ${meta.total} entries`;

    const pagination = document.getElementById('pagination');
    let html = '';

    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadSuppliers(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSuppliers(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadSuppliers(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSuppliers(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadSuppliers(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Supplier';
    document.getElementById('SupplierId').value = '';
    document.getElementById('SupplierForm').reset();
    document.getElementById('commissionRate').value = '5';
    document.getElementById('status').value = 'pending';
    new bootstrap.Modal(document.getElementById('SupplierModal')).show();
}

async function editSupplier(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/suppliers/${id}`);
        const data = await response.json();

        if (data.success) {
            const ds = data.data;
            document.getElementById('modalTitle').textContent = 'Edit Supplier';
            document.getElementById('SupplierId').value = ds.id;
            document.getElementById('companyName').value = ds.company_name;
            document.getElementById('contactName').value = ds.contact_name || '';
            document.getElementById('email').value = ds.email;
            document.getElementById('phone').value = ds.phone || '';
            document.getElementById('commissionRate').value = ds.commission_rate;
            document.getElementById('status').value = ds.status;
            document.getElementById('addressLine1').value = ds.address_line1 || '';
            document.getElementById('addressLine2').value = ds.address_line2 || '';
            document.getElementById('city').value = ds.city || '';
            document.getElementById('state').value = ds.state || '';
            document.getElementById('postalCode').value = ds.postal_code || '';
            document.getElementById('notes').value = ds.notes || '';

            new bootstrap.Modal(document.getElementById('SupplierModal')).show();
        }
    } catch (error) {
        console.error('Error loading Supplier:', error);
        alert('Error loading Supplier details');
    }
}

async function saveSupplier() {
    const id = document.getElementById('SupplierId').value;
    const data = {
        company_name: document.getElementById('companyName').value,
        contact_name: document.getElementById('contactName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        commission_rate: parseFloat(document.getElementById('commissionRate').value),
        status: document.getElementById('status').value,
        address_line1: document.getElementById('addressLine1').value,
        address_line2: document.getElementById('addressLine2').value,
        city: document.getElementById('city').value,
        state: document.getElementById('state').value,
        postal_code: document.getElementById('postalCode').value,
        notes: document.getElementById('notes').value
    };

    try {
        const url = id ? `${API_BASE}/admin/suppliers/${id}` : `${API_BASE}/admin/suppliers`;
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('SupplierModal')).hide();
            loadSuppliers();
        } else {
            alert(result.message || 'Error saving Supplier');
        }
    } catch (error) {
        console.error('Error saving Supplier:', error);
        alert('Error saving Supplier');
    }
}

async function approveSupplier(id) {
    if (!confirm('Approve this Supplier?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/suppliers/${id}/approve`, {
            method: 'POST'
        });
        const result = await response.json();

        if (result.success) {
            loadSuppliers();
        } else {
            alert(result.message || 'Error approving Supplier');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error approving Supplier');
    }
}

async function toggleSuspend(id) {
    const ds = Suppliers.find(d => d.id === id);
    const action = ds.status === 'suspended' ? 'reactivate' : 'suspend';

    if (!confirm(`Are you sure you want to ${action} this Supplier?`)) return;

    try {
        const response = await fetch(`${API_BASE}/admin/suppliers/${id}/toggle-suspend`, {
            method: 'POST'
        });
        const result = await response.json();

        if (result.success) {
            loadSuppliers();
        } else {
            alert(result.message || `Error ${action}ing Supplier`);
        }
    } catch (error) {
        console.error('Error:', error);
        alert(`Error ${action}ing Supplier`);
    }
}

async function deleteSupplier(id) {
    if (!confirm('Are you sure you want to delete this Supplier? This will also delete all their orders.')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/suppliers/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.success) {
            loadSuppliers();
        } else {
            alert(result.message || 'Error deleting Supplier');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting Supplier');
    }
}

function showApiKey(id) {
    const ds = Suppliers.find(d => d.id === id);
    if (ds) {
        currentSupplierId = id;
        document.getElementById('apiKeyCompany').textContent = ds.company_name;
        document.getElementById('apiKeyValue').value = ds.api_key;
        new bootstrap.Modal(document.getElementById('apiKeyModal')).show();
    }
}

function copyApiKey() {
    const input = document.getElementById('apiKeyValue');
    input.select();
    document.execCommand('copy');
    alert('API key copied to clipboard');
}

async function regenerateApiKey() {
    if (!confirm('Are you sure you want to regenerate the API key? The old key will stop working immediately.')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/suppliers/${currentSupplierId}/regenerate-key`, {
            method: 'POST'
        });
        const result = await response.json();

        if (result.success) {
            document.getElementById('apiKeyValue').value = result.api_key;
            loadSuppliers();
            alert('API key regenerated successfully');
        } else {
            alert(result.message || 'Error regenerating API key');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error regenerating API key');
    }
}
</script>
@endpush
