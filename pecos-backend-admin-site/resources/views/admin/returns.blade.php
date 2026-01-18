@extends('layouts.admin')

@section('title', 'Returns / RMA')

@push('styles')
<style>
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}
.status-pending { background-color: #fff3cd; color: #856404; }
.status-approved { background-color: #d4edda; color: #155724; }
.status-rejected { background-color: #f8d7da; color: #721c24; }
.status-received { background-color: #cce5ff; color: #004085; }
.status-inspecting { background-color: #e2e3e5; color: #383d41; }
.status-processed { background-color: #d1ecf1; color: #0c5460; }
.status-refunded { background-color: #d4edda; color: #155724; }
.status-exchanged { background-color: #fff3cd; color: #856404; }
.status-closed { background-color: #e2e3e5; color: #383d41; }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Returns / RMA</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Returns</li>
            </ol>
        </nav>
    </div>
    <div>
        <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#reasonsModal">
            <i class="bi bi-gear me-1"></i> Manage Reasons
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newReturnModal">
            <i class="bi bi-plus-lg me-1"></i> New Return
        </button>
    </div>
</div>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        <h3 class="mb-0" id="statPending">0</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                        <h3 class="mb-0" id="statApproved">0</h3>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="bi bi-box-seam fs-5"></i>
                        </div>
                        <h3 class="mb-0" id="statProcessing">0</h3>
                        <small class="text-muted">Processing</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="bi bi-arrow-repeat fs-5"></i>
                        </div>
                        <h3 class="mb-0" id="statTotal">0</h3>
                        <small class="text-muted">Total Returns</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-danger mb-2">
                            <i class="bi bi-currency-dollar fs-5"></i>
                        </div>
                        <h3 class="mb-0" id="statRefunds">$0</h3>
                        <small class="text-muted">Total Refunds</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2">
                            <i class="bi bi-graph-down fs-5"></i>
                        </div>
                        <h3 class="mb-0" id="statAvgRefund">$0</h3>
                        <small class="text-muted">Avg Refund</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="filterSearch" placeholder="RMA#, Order#, Customer...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="received">Received</option>
                            <option value="inspecting">Inspecting</option>
                            <option value="processed">Processed</option>
                            <option value="refunded">Refunded</option>
                            <option value="exchanged">Exchanged</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select id="filterType" class="form-select">
                            <option value="">All Types</option>
                            <option value="refund">Refund</option>
                            <option value="exchange">Exchange</option>
                            <option value="store_credit">Store Credit</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" id="filterDateFrom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" id="filterDateTo">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Returns Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="returnsTable">
                        <thead>
                            <tr>
                                <th>RMA #</th>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Reason</th>
                                <th>Type</th>
                                <th>Items</th>
                                <th>Refund Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="returnsTableBody">
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    Loading returns...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div id="paginationInfo">Showing 0 returns</div>
                    <nav>
                        <ul class="pagination mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Return Modal -->
<div class="modal fade" id="newReturnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newReturnForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Order ID</label>
                            <input type="number" class="form-control" id="newOrderId" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return Type</label>
                            <select class="form-select" id="newReturnType" required>
                                <option value="refund">Refund</option>
                                <option value="exchange">Exchange</option>
                                <option value="store_credit">Store Credit</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Return Reason</label>
                            <select class="form-select" id="newReturnReason" required>
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Customer Notes</label>
                            <textarea class="form-control" id="newCustomerNotes" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Admin Notes</label>
                            <textarea class="form-control" id="newAdminNotes" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6>Return Items</h6>
                            <div id="returnItemsContainer">
                                <div class="return-item row g-2 mb-2">
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" placeholder="UPC" name="items[0][product_upc]" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" placeholder="Product Name" name="items[0][product_name]" required>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" class="form-control" placeholder="Qty" name="items[0][quantity]" value="1" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.01" class="form-control" placeholder="Price" name="items[0][unit_price]" required>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="items[0][condition]" required>
                                            <option value="unopened">Unopened</option>
                                            <option value="like_new">Like New</option>
                                            <option value="good" selected>Good</option>
                                            <option value="fair">Fair</option>
                                            <option value="damaged">Damaged</option>
                                            <option value="defective">Defective</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                                <i class="bi bi-plus"></i> Add Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveReturnBtn">Create Return</button>
            </div>
        </div>
    </div>
</div>

<!-- Return Detail Modal -->
<div class="modal fade" id="returnDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Details: <span id="detailRmaNumber"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="returnDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Reasons Modal -->
<div class="modal fade" id="reasonsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Return Reasons</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reasonsList">
                    <!-- Populated dynamically -->
                </div>
                <hr>
                <h6>Add New Reason</h6>
                <form id="addReasonForm" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="newReasonName" placeholder="Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="newReasonCode" placeholder="CODE" required>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="newReasonPhoto">
                            <label class="form-check-label" for="newReasonPhoto">Requires Photo</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ config("app.api_url") }}/api/v1';
let currentPage = 1;
let returnReasons = [];

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadReturns();
    loadReasons();

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadReturns();
    });

    document.getElementById('addItemBtn').addEventListener('click', addReturnItem);
    document.getElementById('saveReturnBtn').addEventListener('click', saveReturn);
    document.getElementById('addReasonForm').addEventListener('submit', saveReason);
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/returns/stats`);
        const data = await response.json();

        document.getElementById('statPending').textContent = data.pending_returns || 0;
        document.getElementById('statApproved').textContent = data.approved_returns || 0;
        document.getElementById('statProcessing').textContent = data.processing_returns || 0;
        document.getElementById('statTotal').textContent = data.total_returns || 0;
        document.getElementById('statRefunds').textContent = '$' + (data.total_refunds || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('statAvgRefund').textContent = '$' + (data.avg_refund_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadReturns() {
    const tbody = document.getElementById('returnsTableBody');
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';

    try {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: 20,
            search: document.getElementById('filterSearch').value,
            status: document.getElementById('filterStatus').value,
            type: document.getElementById('filterType').value,
            date_from: document.getElementById('filterDateFrom').value,
            date_to: document.getElementById('filterDateTo').value
        });

        const response = await fetch(`${API_BASE}/admin/returns?${params}`);
        const data = await response.json();

        if (data.data && data.data.length > 0) {
            tbody.innerHTML = data.data.map(ret => `
                <tr>
                    <td><strong>${ret.rma_number}</strong></td>
                    <td>${ret.order_number || '-'}</td>
                    <td>${ret.first_name || ''} ${ret.last_name || ''}<br><small class="text-muted">${ret.customer_email || ''}</small></td>
                    <td>${ret.reason_name || '-'}</td>
                    <td><span class="badge bg-secondary">${ret.type}</span></td>
                    <td>${ret.item_count || 0}</td>
                    <td>$${parseFloat(ret.refund_amount || 0).toFixed(2)}</td>
                    <td><span class="status-badge status-${ret.status}">${ret.status}</span></td>
                    <td>${new Date(ret.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewReturn(${ret.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            document.getElementById('paginationInfo').textContent = `Showing ${data.from}-${data.to} of ${data.total} returns`;
            renderPagination(data);
        } else {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">No returns found</td></tr>';
            document.getElementById('paginationInfo').textContent = 'Showing 0 returns';
        }
    } catch (error) {
        console.error('Error loading returns:', error);
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger">Error loading returns</td></tr>';
    }
}

async function loadReasons() {
    try {
        const response = await fetch(`${API_BASE}/admin/returns/reasons`);
        const data = await response.json();
        returnReasons = data.data || [];

        const select = document.getElementById('newReturnReason');
        select.innerHTML = returnReasons.map(r => `<option value="${r.id}">${r.name}</option>`).join('');

        const list = document.getElementById('reasonsList');
        list.innerHTML = returnReasons.map(r => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                <div>
                    <strong>${r.name}</strong> <code class="ms-2">${r.code}</code>
                    ${r.requires_photo ? '<i class="bi bi-camera ms-2 text-muted" title="Requires photo"></i>' : ''}
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReason(${r.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading reasons:', error);
    }
}

async function viewReturn(id) {
    const modal = new bootstrap.Modal(document.getElementById('returnDetailModal'));
    const content = document.getElementById('returnDetailContent');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
    modal.show();

    try {
        const response = await fetch(`${API_BASE}/admin/returns/${id}`);
        const data = await response.json();
        const ret = data.data.return;
        const items = data.data.items || [];
        const history = data.data.history || [];

        document.getElementById('detailRmaNumber').textContent = ret.rma_number;

        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Return Information</h6>
                    <table class="table table-sm">
                        <tr><td>RMA Number</td><td><strong>${ret.rma_number}</strong></td></tr>
                        <tr><td>Order Number</td><td>${ret.order_number || '-'}</td></tr>
                        <tr><td>Status</td><td><span class="status-badge status-${ret.status}">${ret.status}</span></td></tr>
                        <tr><td>Type</td><td>${ret.type}</td></tr>
                        <tr><td>Reason</td><td>${ret.reason_name}</td></tr>
                        <tr><td>Created</td><td>${new Date(ret.created_at).toLocaleString()}</td></tr>
                    </table>

                    <h6 class="mt-4">Customer</h6>
                    <p>${ret.first_name} ${ret.last_name}<br>
                    <small class="text-muted">${ret.customer_email}<br>${ret.customer_phone || ''}</small></p>

                    <h6 class="mt-4">Update Status</h6>
                    <div class="row g-2">
                        <div class="col-8">
                            <select class="form-select form-select-sm" id="updateStatus">
                                <option value="pending" ${ret.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="approved" ${ret.status === 'approved' ? 'selected' : ''}>Approved</option>
                                <option value="rejected" ${ret.status === 'rejected' ? 'selected' : ''}>Rejected</option>
                                <option value="received" ${ret.status === 'received' ? 'selected' : ''}>Received</option>
                                <option value="inspecting" ${ret.status === 'inspecting' ? 'selected' : ''}>Inspecting</option>
                                <option value="processed" ${ret.status === 'processed' ? 'selected' : ''}>Processed</option>
                                <option value="refunded" ${ret.status === 'refunded' ? 'selected' : ''}>Refunded</option>
                                <option value="exchanged" ${ret.status === 'exchanged' ? 'selected' : ''}>Exchanged</option>
                                <option value="closed" ${ret.status === 'closed' ? 'selected' : ''}>Closed</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-primary btn-sm w-100" onclick="updateReturnStatus(${ret.id})">Update</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Refund Details</h6>
                    <table class="table table-sm">
                        <tr><td>Refund Amount</td><td>$${parseFloat(ret.refund_amount || 0).toFixed(2)}</td></tr>
                        <tr><td>Restocking Fee</td><td>$${parseFloat(ret.restocking_fee || 0).toFixed(2)}</td></tr>
                        <tr><td>Refund Method</td><td>${ret.refund_method || 'Not set'}</td></tr>
                        <tr><td>Tracking Number</td><td>${ret.tracking_number || 'Not set'}</td></tr>
                    </table>

                    <h6 class="mt-4">Return Items</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Product</th><th>Qty</th><th>Condition</th><th>Refund</th></tr></thead>
                        <tbody>
                            ${items.map(item => `
                                <tr>
                                    <td>${item.product_name}<br><small class="text-muted">${item.product_upc}</small></td>
                                    <td>${item.quantity}</td>
                                    <td>${item.condition}</td>
                                    <td>$${parseFloat(item.refund_amount || 0).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <h6 class="mt-4">Status History</h6>
                    <div class="small" style="max-height: 200px; overflow-y: auto;">
                        ${history.map(h => `
                            <div class="mb-2 pb-2 border-bottom">
                                <strong>${h.old_status || 'New'}</strong> → <strong>${h.new_status}</strong>
                                <br><small class="text-muted">${new Date(h.created_at).toLocaleString()} ${h.changed_by_name ? '- ' + h.changed_by_name : ''}</small>
                                ${h.notes ? '<br><small>' + h.notes + '</small>' : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading return:', error);
        content.innerHTML = '<div class="text-center text-danger py-4">Error loading return details</div>';
    }
}

async function updateReturnStatus(id) {
    const status = document.getElementById('updateStatus').value;
    try {
        const response = await fetch(`${API_BASE}/admin/returns/${id}/status`, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ status })
        });
        if (response.ok) {
            loadReturns();
            loadStats();
            viewReturn(id);
        }
    } catch (error) {
        console.error('Error updating status:', error);
    }
}

let itemIndex = 1;
function addReturnItem() {
    const container = document.getElementById('returnItemsContainer');
    const html = `
        <div class="return-item row g-2 mb-2">
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="UPC" name="items[${itemIndex}][product_upc]" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Product Name" name="items[${itemIndex}][product_name]" required>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control" placeholder="Qty" name="items[${itemIndex}][quantity]" value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control" placeholder="Price" name="items[${itemIndex}][unit_price]" required>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="items[${itemIndex}][condition]" required>
                    <option value="unopened">Unopened</option>
                    <option value="like_new">Like New</option>
                    <option value="good" selected>Good</option>
                    <option value="fair">Fair</option>
                    <option value="damaged">Damaged</option>
                    <option value="defective">Defective</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    itemIndex++;

    container.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.return-item').remove();
        });
    });
}

async function saveReturn() {
    const form = document.getElementById('newReturnForm');
    const formData = new FormData(form);

    const items = [];
    document.querySelectorAll('.return-item').forEach((row, i) => {
        items.push({
            product_upc: row.querySelector('[name*="product_upc"]').value,
            product_name: row.querySelector('[name*="product_name"]').value,
            quantity: parseInt(row.querySelector('[name*="quantity"]').value),
            unit_price: parseFloat(row.querySelector('[name*="unit_price"]').value),
            condition: row.querySelector('[name*="condition"]').value
        });
    });

    const data = {
        order_id: parseInt(document.getElementById('newOrderId').value),
        reason_id: parseInt(document.getElementById('newReturnReason').value),
        type: document.getElementById('newReturnType').value,
        customer_notes: document.getElementById('newCustomerNotes').value,
        admin_notes: document.getElementById('newAdminNotes').value,
        items: items
    };

    try {
        const response = await fetch(`${API_BASE}/admin/returns`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('newReturnModal')).hide();
            form.reset();
            loadReturns();
            loadStats();
        } else {
            const error = await response.json();
            alert('Error: ' + (error.message || 'Failed to create return'));
        }
    } catch (error) {
        console.error('Error saving return:', error);
        alert('Error creating return');
    }
}

async function saveReason(e) {
    e.preventDefault();

    const data = {
        name: document.getElementById('newReasonName').value,
        code: document.getElementById('newReasonCode').value.toUpperCase(),
        requires_photo: document.getElementById('newReasonPhoto').checked
    };

    try {
        const response = await fetch(`${API_BASE}/admin/returns/reasons`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        if (response.ok) {
            document.getElementById('addReasonForm').reset();
            loadReasons();
        }
    } catch (error) {
        console.error('Error saving reason:', error);
    }
}

async function deleteReason(id) {
    if (!confirm('Are you sure you want to delete this reason?')) return;

    try {
        await fetch(`${API_BASE}/admin/returns/reasons/${id}`, { method: 'DELETE' });
        loadReasons();
    } catch (error) {
        console.error('Error deleting reason:', error);
    }
}

function renderPagination(data) {
    const pagination = document.getElementById('pagination');
    if (data.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';
    if (data.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${data.current_page - 1})">Previous</a></li>`;
    }

    for (let i = 1; i <= data.last_page; i++) {
        if (i === data.current_page) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else if (Math.abs(i - data.current_page) <= 2 || i === 1 || i === data.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${i})">${i}</a></li>`;
        } else if (Math.abs(i - data.current_page) === 3) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    if (data.current_page < data.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${data.current_page + 1})">Next</a></li>`;
    }

    pagination.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    loadReturns();
}
</script>
@endpush
