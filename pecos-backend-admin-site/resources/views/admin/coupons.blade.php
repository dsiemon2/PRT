@extends('layouts.admin')

@section('title', 'Coupon Management')

@section('content')
<div class="page-header">
    <h1>Coupon Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Coupons</li>
        </ol>
    </nav>
</div>

<!-- Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search coupons...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterCoupons()">Filter</button>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" onclick="openCreateModal()">
                    <i class="bi bi-plus"></i> Create Coupon
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Coupons Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Min Order</th>
                <th>Usage</th>
                <th>Expires</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="couponsTable">
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading coupons...
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

<!-- Add/Edit Coupon Modal -->
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title" id="couponModalTitle">Create New Coupon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="couponForm" class="admin-form">
                    <input type="hidden" id="couponId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" class="form-control" id="couponCode" name="code" placeholder="e.g., SAVE20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" id="couponDescription" name="description" placeholder="e.g., 20% off all items">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type</label>
                        <select class="form-select" id="couponDiscountType" name="discount_type" required>
                            <option value="">Select Type</option>
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Value</label>
                        <input type="number" step="0.01" class="form-control" id="couponDiscountValue" name="discount_value" placeholder="e.g., 25" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Order Amount</label>
                        <input type="number" step="0.01" class="form-control" id="couponMinOrder" name="min_order_amount" placeholder="e.g., 50.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usage Limit</label>
                        <input type="number" class="form-control" id="couponUsageLimit" name="usage_limit" placeholder="Leave empty for unlimited">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiration Date</label>
                        <input type="datetime-local" class="form-control" id="couponExpiration" name="expiration_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="couponStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" id="saveCouponBtn" onclick="saveCoupon()">Save Coupon</button>
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
let currentPage = 1;
let perPage = 20;
let couponModal;

document.addEventListener('DOMContentLoaded', function() {
    couponModal = new bootstrap.Modal(document.getElementById('couponModal'));
    loadCoupons();
});

async function loadCoupons(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/coupons?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&active=${status === 'active' ? '1' : '0'}`;

        const response = await fetch(url);
        const data = await response.json();

        const coupons = data.data || [];
        const meta = {
            current_page: data.current_page || data.meta?.current_page || 1,
            last_page: data.last_page || data.meta?.last_page || 1,
            from: data.from || data.meta?.from || 1,
            to: data.to || data.meta?.to || coupons.length,
            total: data.total || data.meta?.total || coupons.length,
            per_page: data.per_page || data.meta?.per_page || perPage
        };

        renderCoupons(coupons);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading coupons:', error);
        document.getElementById('couponsTable').innerHTML =
            '<tr><td colspan="8" class="text-center text-danger">Error loading coupons</td></tr>';
    }
}

function renderCoupons(coupons) {
    const tbody = document.getElementById('couponsTable');

    if (coupons.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="8" class="text-center py-4 text-muted">No coupons found</td>
        </tr>`;
        return;
    }

    let html = '';
    coupons.forEach(coupon => {
        const expirationDate = coupon.expiration_date || coupon.expires_at || null;
        const isExpired = expirationDate && new Date(expirationDate) < new Date();
        const status = coupon.status || 'active';
        const usageCount = coupon.usages_count || coupon.usage_count || 0;
        const usageLimit = coupon.usage_limit || null;

        let statusClass, statusText;
        if (isExpired || status === 'expired') {
            statusClass = 'inactive';
            statusText = 'Expired';
        } else if (status === 'inactive') {
            statusClass = 'inactive';
            statusText = 'Inactive';
        } else if (usageLimit && usageCount >= usageLimit) {
            statusClass = 'inactive';
            statusText = 'Maxed';
        } else {
            statusClass = 'active';
            statusText = 'Active';
        }

        let valueDisplay = '-';
        if (coupon.discount_type === 'percentage') {
            valueDisplay = coupon.discount_value + '%';
        } else if (coupon.discount_type === 'fixed') {
            valueDisplay = '$' + Number(coupon.discount_value || 0).toFixed(2);
        }

        const minOrder = '$' + Number(coupon.min_order_amount || coupon.minimum_purchase || 0).toFixed(2);
        const expiresDisplay = expirationDate
            ? new Date(expirationDate).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})
            : 'Never';

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td><code>${coupon.code || 'N/A'}</code></td>`;
        html += `<td>${(coupon.discount_type || 'Unknown').charAt(0).toUpperCase() + (coupon.discount_type || 'Unknown').slice(1)}</td>`;
        html += `<td>${valueDisplay}</td>`;
        html += `<td>${minOrder}</td>`;
        html += `<td>${usageCount} / ${usageLimit || 'Unlimited'}</td>`;
        html += `<td>${expiresDisplay}</td>`;
        html += `<td><span class="status-badge ${statusClass}">${statusText}</span></td>`;
        html += `<td>
            <button class="btn btn-sm btn-outline-primary" onclick='editCoupon(${JSON.stringify(coupon).replace(/'/g, "&#39;")})' title="Edit"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteCoupon(${coupon.id}, '${(coupon.code || '').replace(/'/g, "\\'")}')" title="Delete"><i class="bi bi-trash"></i></button>
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
    html += `<a class="page-link" href="#" onclick="loadCoupons(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadCoupons(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadCoupons(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadCoupons(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadCoupons(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterCoupons() {
    loadCoupons(1);
}

function openCreateModal() {
    document.getElementById('couponModalTitle').textContent = 'Create New Coupon';
    document.getElementById('couponForm').reset();
    document.getElementById('couponId').value = '';
    couponModal.show();
}

function editCoupon(coupon) {
    document.getElementById('couponModalTitle').textContent = 'Edit Coupon';
    document.getElementById('couponId').value = coupon.id;
    document.getElementById('couponCode').value = coupon.code || '';
    document.getElementById('couponDescription').value = coupon.description || '';
    document.getElementById('couponDiscountType').value = coupon.discount_type || '';
    document.getElementById('couponDiscountValue').value = coupon.discount_value || '';
    document.getElementById('couponMinOrder').value = coupon.min_order_amount || '';
    document.getElementById('couponUsageLimit').value = coupon.usage_limit || '';
    document.getElementById('couponStatus').value = coupon.status || 'active';

    if (coupon.expiration_date) {
        const date = new Date(coupon.expiration_date);
        document.getElementById('couponExpiration').value = date.toISOString().slice(0, 16);
    } else {
        document.getElementById('couponExpiration').value = '';
    }

    couponModal.show();
}

async function saveCoupon() {
    const couponId = document.getElementById('couponId').value;
    const isEdit = !!couponId;

    const data = {
        code: document.getElementById('couponCode').value,
        description: document.getElementById('couponDescription').value,
        discount_type: document.getElementById('couponDiscountType').value,
        discount_value: parseFloat(document.getElementById('couponDiscountValue').value) || 0,
        min_order_amount: parseFloat(document.getElementById('couponMinOrder').value) || 0,
        usage_limit: document.getElementById('couponUsageLimit').value ? parseInt(document.getElementById('couponUsageLimit').value) : null,
        expiration_date: document.getElementById('couponExpiration').value || null,
        status: document.getElementById('couponStatus').value
    };

    try {
        const url = isEdit ? `${API_BASE}/admin/coupons/${couponId}` : `${API_BASE}/admin/coupons`;
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success || response.ok) {
            alert(isEdit ? 'Coupon updated successfully!' : 'Coupon created successfully!');
            couponModal.hide();
            loadCoupons(currentPage);
        } else {
            alert('Error: ' + (result.message || 'Failed to save coupon'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error saving coupon');
    }
}

async function deleteCoupon(couponId, couponCode) {
    if (!confirm(`Are you sure you want to delete coupon "${couponCode}"?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/coupons/${couponId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success || response.ok) {
            alert('Coupon deleted successfully!');
            loadCoupons(currentPage);
        } else {
            alert('Error: ' + (data.message || 'Failed to delete coupon'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting coupon');
    }
}
</script>
@endpush
