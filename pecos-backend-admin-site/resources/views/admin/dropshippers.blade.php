@extends('layouts.admin')

@section('title', 'Drop Shippers')

@section('content')
<div class="page-header">
    <h1>Drop Shipper Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Drop Shippers</li>
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
            <div class="label">Total Drop Shippers</div>
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
        <form class="row g-3" onsubmit="return filterDropshippers(event)">
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
                    <i class="bi bi-plus"></i> Add Drop Shipper
                </button>
                <a href="{{ route('admin.dropship.orders') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-box"></i> View Orders
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Drop Shippers Table -->
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
        <tbody id="dropshippersTable">
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading dropshippers...
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
<div class="modal fade" id="dropshipperModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Drop Shipper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dropshipperForm">
                    <input type="hidden" id="dropshipperId">
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
                <button type="button" class="btn btn-primary" onclick="saveDropshipper()">Save</button>
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
let dropshippers = [];
let currentDropshipperId = null;
let currentPage = 1;
let perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    loadDropshippers();
});

async function loadDropshippers(page = 1) {
    currentPage = page;
    try {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/dropshippers?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            dropshippers = data.data;
            renderDropshippers();
            updateStats(data.stats);
            renderPagination(data.meta);
        }
    } catch (error) {
        console.error('Error loading dropshippers:', error);
        document.getElementById('dropshippersTable').innerHTML = `
            <tr><td colspan="7" class="text-center text-danger">Error loading dropshippers</td></tr>
        `;
    }
}

function updateStats(stats) {
    document.getElementById('stat-total').textContent = stats.total.toLocaleString();
    document.getElementById('stat-active').textContent = stats.active.toLocaleString();
    document.getElementById('stat-orders').textContent = stats.total_orders.toLocaleString();
    document.getElementById('stat-revenue').textContent = '$' + parseFloat(stats.total_revenue).toLocaleString();
}

function renderDropshippers() {
    const tbody = document.getElementById('dropshippersTable');

    if (dropshippers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No dropshippers found</td></tr>';
        return;
    }

    tbody.innerHTML = dropshippers.map(ds => `
        <tr onclick="highlightRow(event)" style="cursor: pointer;">
            <td>
                <strong>${ds.company_name}</strong><br>
                <small class="text-muted">API Key: ${ds.api_key ? ds.api_key.substring(0, 3) + '****' + ds.api_key.slice(-4) : 'N/A'}</small>
            </td>
            <td>
                ${ds.email || ''}<br>
                <small class="text-muted">${ds.phone || ''}</small>
            </td>
            <td>
                <span class="status-badge ${ds.status === 'active' ? 'active' : ds.status === 'pending' ? 'pending' : 'inactive'}">
                    ${ds.status ? ds.status.charAt(0).toUpperCase() + ds.status.slice(1) : 'Unknown'}
                </span>
            </td>
            <td>${(ds.total_orders || 0).toLocaleString()}</td>
            <td>$${parseFloat(ds.total_revenue || 0).toLocaleString()}</td>
            <td>${ds.commission_rate || 0}%</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="editDropshipper(${ds.id})" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="showApiKey(${ds.id})" title="View API Key">
                    <i class="bi bi-key"></i>
                </button>
                ${ds.status === 'pending' ? `
                    <button class="btn btn-sm btn-outline-success" onclick="approveDropshipper(${ds.id})" title="Approve">
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
                <button class="btn btn-sm btn-outline-danger" onclick="deleteDropshipper(${ds.id})" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function filterDropshippers(event) {
    event.preventDefault();
    loadDropshippers(1);
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
        <a class="page-link" href="#" onclick="loadDropshippers(${meta.current_page - 1}); return false;">Previous</a>
    </li>`;

    // Page numbers
    const totalPages = meta.last_page;
    const current = meta.current_page;

    let startPage = Math.max(1, current - 2);
    let endPage = Math.min(totalPages, current + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadDropshippers(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === current ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadDropshippers(${i}); return false;">${i}</a>
        </li>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadDropshippers(${totalPages}); return false;">${totalPages}</a></li>`;
    }

    // Next button
    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadDropshippers(${meta.current_page + 1}); return false;">Next</a>
    </li>`;

    pagination.innerHTML = html;
}

function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Drop Shipper';
    document.getElementById('dropshipperId').value = '';
    document.getElementById('dropshipperForm').reset();
    document.getElementById('commissionRate').value = '5';
    document.getElementById('status').value = 'pending';
    new bootstrap.Modal(document.getElementById('dropshipperModal')).show();
}

async function editDropshipper(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/dropshippers/${id}`);
        const data = await response.json();

        if (data.success) {
            const ds = data.data;
            document.getElementById('modalTitle').textContent = 'Edit Drop Shipper';
            document.getElementById('dropshipperId').value = ds.id;
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

            new bootstrap.Modal(document.getElementById('dropshipperModal')).show();
        }
    } catch (error) {
        console.error('Error loading dropshipper:', error);
        alert('Error loading dropshipper details');
    }
}

async function saveDropshipper() {
    const id = document.getElementById('dropshipperId').value;
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
        const url = id ? `${API_BASE}/admin/dropshippers/${id}` : `${API_BASE}/admin/dropshippers`;
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('dropshipperModal')).hide();
            loadDropshippers();
        } else {
            alert(result.message || 'Error saving dropshipper');
        }
    } catch (error) {
        console.error('Error saving dropshipper:', error);
        alert('Error saving dropshipper');
    }
}

async function approveDropshipper(id) {
    if (!confirm('Approve this dropshipper?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/dropshippers/${id}/approve`, {
            method: 'POST'
        });
        const result = await response.json();

        if (result.success) {
            loadDropshippers();
        } else {
            alert(result.message || 'Error approving dropshipper');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error approving dropshipper');
    }
}

async function toggleSuspend(id) {
    const ds = dropshippers.find(d => d.id === id);
    const action = ds.status === 'suspended' ? 'reactivate' : 'suspend';

    if (!confirm(`Are you sure you want to ${action} this dropshipper?`)) return;

    try {
        const response = await fetch(`${API_BASE}/admin/dropshippers/${id}/toggle-suspend`, {
            method: 'POST'
        });
        const result = await response.json();

        if (result.success) {
            loadDropshippers();
        } else {
            alert(result.message || `Error ${action}ing dropshipper`);
        }
    } catch (error) {
        console.error('Error:', error);
        alert(`Error ${action}ing dropshipper`);
    }
}

async function deleteDropshipper(id) {
    if (!confirm('Are you sure you want to delete this dropshipper? This will also delete all their orders.')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/dropshippers/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.success) {
            loadDropshippers();
        } else {
            alert(result.message || 'Error deleting dropshipper');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting dropshipper');
    }
}

function showApiKey(id) {
    const ds = dropshippers.find(d => d.id === id);
    if (ds) {
        currentDropshipperId = id;
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
        const response = await fetch(`${API_BASE}/admin/dropshippers/${currentDropshipperId}/regenerate-key`, {
            method: 'POST'
        });
        const result = await response.json();

        if (result.success) {
            document.getElementById('apiKeyValue').value = result.api_key;
            loadDropshippers();
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
