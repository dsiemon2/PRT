@extends('layouts.admin')

@section('title', 'Purchase Orders')

@section('content')
<div class="page-header">
    <h1>Purchase Orders</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Purchase Orders</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-2">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total POs</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-file-earmark-ruled"></i>
            </div>
            <div class="value" id="stat-draft">-</div>
            <div class="label">Draft</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-send"></i>
            </div>
            <div class="value" id="stat-ordered">-</div>
            <div class="label">Ordered</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-truck"></i>
            </div>
            <div class="value" id="stat-shipped">-</div>
            <div class="label">Shipped</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value" id="stat-received">-</div>
            <div class="label">Received</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value" id="stat-value">$0</div>
            <div class="label">Total Value</div>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="ordered">Ordered</option>
                    <option value="shipped">Shipped</option>
                    <option value="partially_received">Partially Received</option>
                    <option value="received">Received</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="supplierFilter" placeholder="Search supplier...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterPOs()">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-success w-100" onclick="showCreatePOModal()">
                    <i class="bi bi-plus-circle"></i> New PO
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Orders Table -->
<div class="admin-table">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Status</th>
                <th>Items</th>
                <th>Received</th>
                <th>Total Cost</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="poTable">
            <tr>
                <td colspan="9" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading purchase orders...
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

<!-- Create/Edit PO Modal -->
<div class="modal fade" id="poModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="poModalTitle">Create Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="poForm">
                    <input type="hidden" id="poId">

                    <!-- Supplier Information -->
                    <h6>Supplier Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Supplier *</label>
                            <select class="form-select" id="supplierSelect" onchange="fillSupplierInfo()" required>
                                <option value="">Select supplier...</option>
                                <option value="new">+ Add New Supplier</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier Name *</label>
                            <input type="text" class="form-control" id="supplierName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier Email</label>
                            <input type="email" class="form-control" id="supplierEmail">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier Phone</label>
                            <input type="tel" class="form-control" id="supplierPhone">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Supplier Address</label>
                            <textarea class="form-control" id="supplierAddress" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Order Information -->
                    <h6>Order Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Order Date *</label>
                            <input type="date" class="form-control" id="orderDate" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expected Delivery</label>
                            <input type="date" class="form-control" id="expectedDelivery">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" id="poNotes">
                        </div>
                    </div>

                    <!-- Line Items -->
                    <h6>Line Items</h6>
                    <table class="table table-sm" id="itemsTable">
                        <thead>
                            <tr>
                                <th width="40%">Product</th>
                                <th width="15%">Quantity</th>
                                <th width="15%">Unit Cost</th>
                                <th width="20%">Line Total</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLineItem()">
                        <i class="bi bi-plus"></i> Add Item
                    </button>

                    <!-- Totals -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end" id="subtotalDisplay">$0.00</td>
                                </tr>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm" id="shippingCost" value="0" step="0.01" min="0" onchange="calculateTotals()">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tax:</td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm" id="taxAmount" value="0" step="0.01" min="0" onchange="calculateTotals()">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong id="totalDisplay">$0.00</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="savePO()">Create Purchase Order</button>
            </div>
        </div>
    </div>
</div>

<!-- View PO Detail Modal -->
<div class="modal fade" id="poDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailContent">
                Loading...
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
let allProducts = []; // Cache for product search
let allSuppliers = []; // Cache for suppliers

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadPOs();
    loadProducts(); // For product dropdown
    loadSuppliers(); // For supplier dropdown

    // Set today's date as default
    document.getElementById('orderDate').valueAsDate = new Date();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/stats`);
        const data = await response.json();

        if (data.success) {
            document.getElementById('stat-total').textContent = data.data.total_purchase_orders;
            document.getElementById('stat-draft').textContent = data.data.draft;
            document.getElementById('stat-ordered').textContent = data.data.ordered;
            document.getElementById('stat-shipped').textContent = data.data.shipped;
            document.getElementById('stat-received').textContent = data.data.received;
            document.getElementById('stat-value').textContent = '$' + Number(data.data.total_value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadPOs(page = 1) {
    currentPage = page;

    try {
        const status = document.getElementById('statusFilter').value;
        const supplier = document.getElementById('supplierFilter').value;

        let url = `${API_BASE}/admin/purchase-orders?page=${page}&per_page=${perPage}`;
        if (status) url += `&status=${status}`;
        if (supplier) url += `&supplier=${encodeURIComponent(supplier)}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            renderPOs(data.data);
            renderPagination(data.meta);
        }
    } catch (error) {
        console.error('Error loading POs:', error);
        document.getElementById('poTable').innerHTML =
            '<tr><td colspan="9" class="text-center text-danger">Error loading purchase orders</td></tr>';
    }
}

function initTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
}

function renderPOs(pos) {
    const tbody = document.getElementById('poTable');

    if (pos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No purchase orders found</td></tr>';
        return;
    }

    let html = '';
    pos.forEach(po => {
        const orderDate = new Date(po.order_date).toLocaleDateString();
        const expectedDate = po.expected_delivery_date ? new Date(po.expected_delivery_date).toLocaleDateString() : '-';

        let statusClass = 'secondary';
        if (po.status === 'draft') statusClass = 'warning';
        else if (po.status === 'ordered') statusClass = 'info';
        else if (po.status === 'shipped') statusClass = 'primary';
        else if (po.status === 'received') statusClass = 'success';
        else if (po.status === 'partially_received') statusClass = 'info';
        else if (po.status === 'cancelled') statusClass = 'danger';

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td><strong>${po.po_number}</strong></td>`;
        html += `<td>${po.supplier_name}</td>`;
        html += `<td>${orderDate}</td>`;
        html += `<td>${expectedDate}</td>`;
        html += `<td><span class="badge bg-${statusClass}">${po.status.replace('_', ' ')}</span></td>`;
        html += `<td>${po.items_count} items</td>`;
        html += `<td><div class="progress" style="height: 20px;"><div class="progress-bar bg-success" style="width: ${po.received_percentage}%">${po.received_percentage}%</div></div></td>`;
        html += `<td>$${Number(po.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>`;
        html += `<td>`;
        html += `<button class="btn btn-sm btn-outline-primary" onclick="viewPODetail(${po.id})" title="View Purchase Order Details" data-bs-toggle="tooltip"><i class="bi bi-eye"></i></button> `;
        if (po.status === 'draft') {
            html += `<button class="btn btn-sm btn-outline-warning" onclick="editPO(${po.id})" title="Edit Purchase Order" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></button> `;
            html += `<button class="btn btn-sm btn-outline-danger" onclick="deletePO(${po.id})" title="Delete Purchase Order" data-bs-toggle="tooltip"><i class="bi bi-trash"></i></button>`;
        }
        if (po.status === 'ordered' || po.status === 'shipped') {
            html += `<a href="/admin/inventory/receive?po=${po.id}" class="btn btn-sm btn-outline-success" title="Receive Items" data-bs-toggle="tooltip"><i class="bi bi-box-arrow-in-down"></i></a>`;
        }
        html += `</td>`;
        html += `</tr>`;
    });

    tbody.innerHTML = html;

    // Initialize tooltips after rendering
    initTooltips();
}

function renderPagination(meta) {
    document.getElementById('paginationInfo').textContent =
        `Showing ${meta.from} to ${meta.to} of ${meta.total} entries`;

    const pagination = document.getElementById('pagination');
    let html = '';

    html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadPOs(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPOs(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadPOs(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPOs(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadPOs(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterPOs() {
    loadPOs(1);
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('supplierFilter').value = '';
    loadPOs(1);
}

function showCreatePOModal() {
    console.log('showCreatePOModal called');

    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        alert('Bootstrap is not loaded!');
        return;
    }

    document.getElementById('poForm').reset();
    document.getElementById('poId').value = '';
    document.getElementById('poModalTitle').textContent = 'Create Purchase Order';
    document.getElementById('itemsTableBody').innerHTML = '';
    document.getElementById('orderDate').valueAsDate = new Date();

    // Populate supplier dropdown
    const supplierSelect = document.getElementById('supplierSelect');
    supplierSelect.innerHTML = '<option value="">Select supplier...</option>';

    // Group suppliers by type
    const dropshippers = allSuppliers.filter(s => s.type === 'dropshipper');
    const otherSuppliers = allSuppliers.filter(s => s.type === 'supplier');

    if (dropshippers.length > 0) {
        const optgroup = document.createElement('optgroup');
        optgroup.label = 'Drop Shippers';
        dropshippers.forEach(ds => {
            const option = document.createElement('option');
            option.value = ds.name;
            option.textContent = ds.name;
            optgroup.appendChild(option);
        });
        supplierSelect.appendChild(optgroup);
    }

    if (otherSuppliers.length > 0) {
        const optgroup = document.createElement('optgroup');
        optgroup.label = 'Other Suppliers';
        otherSuppliers.forEach(sup => {
            const option = document.createElement('option');
            option.value = sup.name;
            option.textContent = sup.name;
            optgroup.appendChild(option);
        });
        supplierSelect.appendChild(optgroup);
    }

    // Add "Add New" option
    const newOption = document.createElement('option');
    newOption.value = 'new';
    newOption.textContent = '+ Add New Supplier';
    supplierSelect.appendChild(newOption);

    addLineItem(); // Add first empty row
    calculateTotals();

    const modalElement = document.getElementById('poModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

async function loadProducts() {
    try {
        const response = await fetch(`${API_BASE}/admin/inventory/products?per_page=1000`);
        const data = await response.json();
        if (data.success) {
            // API returns data.data.products, not just data.data
            allProducts = data.data.products || [];
        }
    } catch (error) {
        console.error('Error loading products:', error);
        allProducts = []; // Set empty array on error
    }
}

async function loadSuppliers() {
    try {
        // Load dropshippers
        const dropshippersResponse = await fetch(`${API_BASE}/admin/dropshippers`);
        const dropshippersData = await dropshippersResponse.json();

        // Load suppliers from suppliers table
        const suppliersResponse = await fetch(`${API_BASE}/admin/suppliers`);
        const suppliersData = await suppliersResponse.json();

        allSuppliers = [];

        // Add dropshippers (active only)
        if (dropshippersData.success && dropshippersData.data) {
            dropshippersData.data.forEach(ds => {
                if (ds.status === 'active') {
                    allSuppliers.push({
                        type: 'dropshipper',
                        id: ds.id,
                        name: ds.company_name,
                        email: ds.email,
                        phone: ds.phone,
                        address: ds.address || ''
                    });
                }
            });
        }

        // Add suppliers (active only)
        if (suppliersData.success && suppliersData.data) {
            suppliersData.data.forEach(sup => {
                if (sup.status === 'active') {
                    allSuppliers.push({
                        type: 'supplier',
                        id: sup.id,
                        name: sup.company_name,
                        email: sup.email || '',
                        phone: sup.phone || '',
                        address: sup.address || ''
                    });
                }
            });
        }
    } catch (error) {
        console.error('Error loading suppliers:', error);
        allSuppliers = [];
    }
}

function fillSupplierInfo() {
    const select = document.getElementById('supplierSelect');
    const selectedValue = select.value;

    if (selectedValue === 'new') {
        // Clear fields for new supplier
        document.getElementById('supplierName').value = '';
        document.getElementById('supplierName').readOnly = false;
        document.getElementById('supplierEmail').value = '';
        document.getElementById('supplierPhone').value = '';
        document.getElementById('supplierAddress').value = '';
        document.getElementById('supplierName').focus();
    } else if (selectedValue) {
        // Fill in existing supplier info
        const supplier = allSuppliers.find(s => s.name === selectedValue);
        if (supplier) {
            document.getElementById('supplierName').value = supplier.name;
            document.getElementById('supplierName').readOnly = true;
            document.getElementById('supplierEmail').value = supplier.email;
            document.getElementById('supplierPhone').value = supplier.phone;
            document.getElementById('supplierAddress').value = supplier.address;
        }
    } else {
        // No selection - clear fields
        document.getElementById('supplierName').value = '';
        document.getElementById('supplierName').readOnly = false;
        document.getElementById('supplierEmail').value = '';
        document.getElementById('supplierPhone').value = '';
        document.getElementById('supplierAddress').value = '';
    }
}

function addLineItem() {
    const tbody = document.getElementById('itemsTableBody');
    const row = tbody.insertRow();
    const rowIndex = tbody.rows.length - 1;

    row.innerHTML = `
        <td>
            <select class="form-control form-control-sm product-select" onchange="updateLineItem(${rowIndex})" required>
                <option value="">Select product...</option>
                ${allProducts.map(p => `<option value="${p.id}" data-price="${p.cost_price || p.SalePrice}">${p.ShortDescription} (${p.UPC})</option>`).join('')}
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm qty-input" min="1" value="1" onchange="updateLineItem(${rowIndex})" required></td>
        <td><input type="number" class="form-control form-control-sm cost-input" min="0" step="0.01" value="0" onchange="updateLineItem(${rowIndex})" required></td>
        <td class="line-total text-end">$0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeLineItem(this)"><i class="bi bi-x"></i></button></td>
    `;
}

function updateLineItem(rowIndex) {
    const tbody = document.getElementById('itemsTableBody');
    const row = tbody.rows[rowIndex];

    const productSelect = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.qty-input');
    const costInput = row.querySelector('.cost-input');
    const lineTotalCell = row.querySelector('.line-total');

    // Auto-fill cost if product selected and cost is 0
    if (productSelect.value && parseFloat(costInput.value) === 0) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const productPrice = selectedOption.getAttribute('data-price');
        if (productPrice) {
            costInput.value = parseFloat(productPrice).toFixed(2);
        }
    }

    const qty = parseInt(qtyInput.value) || 0;
    const cost = parseFloat(costInput.value) || 0;
    const lineTotal = qty * cost;

    lineTotalCell.textContent = '$' + lineTotal.toFixed(2);

    calculateTotals();
}

function removeLineItem(button) {
    const row = button.closest('tr');
    row.remove();
    calculateTotals();
}

function calculateTotals() {
    const tbody = document.getElementById('itemsTableBody');
    let subtotal = 0;

    for (let row of tbody.rows) {
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
        subtotal += qty * cost;
    }

    const shipping = parseFloat(document.getElementById('shippingCost').value) || 0;
    const tax = parseFloat(document.getElementById('taxAmount').value) || 0;
    const total = subtotal + shipping + tax;

    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
}

async function savePO() {
    // Validate form
    const supplierName = document.getElementById('supplierName').value;
    const orderDate = document.getElementById('orderDate').value;

    if (!supplierName || !orderDate) {
        alert('Please fill in required fields (Supplier Name and Order Date)');
        return;
    }

    // Get line items
    const tbody = document.getElementById('itemsTableBody');
    const items = [];

    for (let row of tbody.rows) {
        const productId = row.querySelector('.product-select').value;
        const qty = parseInt(row.querySelector('.qty-input').value);
        const cost = parseFloat(row.querySelector('.cost-input').value);

        if (productId && qty > 0 && cost >= 0) {
            items.push({
                product_id: parseInt(productId),
                quantity_ordered: qty,
                unit_cost: cost
            });
        }
    }

    if (items.length === 0) {
        alert('Please add at least one line item');
        return;
    }

    //  Determine supplier_id or dropshipper_id based on selected supplier
    const selectedSupplierName = document.getElementById('supplierSelect').value;
    const selectedSupplier = allSuppliers.find(s => s.name === selectedSupplierName);

    const poData = {
        supplier_name: supplierName,
        supplier_email: document.getElementById('supplierEmail').value,
        supplier_phone: document.getElementById('supplierPhone').value,
        supplier_address: document.getElementById('supplierAddress').value,
        order_date: orderDate,
        expected_delivery_date: document.getElementById('expectedDelivery').value,
        shipping_cost: parseFloat(document.getElementById('shippingCost').value) || 0,
        tax: parseFloat(document.getElementById('taxAmount').value) || 0,
        notes: document.getElementById('poNotes').value,
        items: items
    };

    // Add supplier_id or dropshipper_id if applicable
    if (selectedSupplier) {
        if (selectedSupplier.type === 'dropshipper') {
            poData.dropshipper_id = selectedSupplier.id;
        } else if (selectedSupplier.type === 'supplier') {
            poData.supplier_id = selectedSupplier.id;
        }
    }

    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(poData)
        });

        const data = await response.json();

        if (data.success) {
            alert('Purchase order created successfully!');
            bootstrap.Modal.getInstance(document.getElementById('poModal')).hide();
            loadPOs();
            loadStats();
        } else {
            alert('Error: ' + (data.message || 'Failed to create purchase order'));
        }
    } catch (error) {
        console.error('Error saving PO:', error);
        alert('Error creating purchase order');
    }
}

async function viewPODetail(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/${id}`);
        const data = await response.json();

        if (data.success) {
            const po = data.data;
            const orderDate = new Date(po.order_date).toLocaleDateString();
            const expectedDate = po.expected_delivery_date ? new Date(po.expected_delivery_date).toLocaleDateString() : '-';
            const actualDate = po.actual_delivery_date ? new Date(po.actual_delivery_date).toLocaleDateString() : '-';

            // Status badge color mapping
            let statusClass = 'secondary';
            if (po.status === 'draft') statusClass = 'warning';
            else if (po.status === 'ordered') statusClass = 'info';
            else if (po.status === 'shipped') statusClass = 'primary';
            else if (po.status === 'received') statusClass = 'success';
            else if (po.status === 'partially_received') statusClass = 'info';
            else if (po.status === 'cancelled') statusClass = 'danger';

            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Purchase Order</h6>
                        <p><strong>PO Number:</strong> ${po.po_number}<br>
                        <strong>Status:</strong> <span class="badge bg-${statusClass}">${po.status.replace('_', ' ')}</span><br>
                        <strong>Order Date:</strong> ${orderDate}<br>
                        <strong>Expected Delivery:</strong> ${expectedDate}<br>
                        <strong>Actual Delivery:</strong> ${actualDate}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Supplier</h6>
                        <p><strong>Name:</strong> ${po.supplier_name}<br>
                        <strong>Email:</strong> ${po.supplier_email || '-'}<br>
                        <strong>Phone:</strong> ${po.supplier_phone || '-'}<br>
                        <strong>Address:</strong> ${po.supplier_address || '-'}</p>
                    </div>
                </div>

                <h6>Line Items</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Ordered</th>
                            <th class="text-center">Received</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Handle items - check if it exists and has data
            const items = po.items || [];
            if (items.length === 0) {
                html += `
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> No line items found for this purchase order
                        </td>
                    </tr>
                `;
            } else {
                items.forEach(item => {
                    const receivedPct = item.quantity_ordered > 0
                        ? Math.round((item.quantity_received / item.quantity_ordered) * 100)
                        : 0;
                    const receivedClass = receivedPct >= 100 ? 'text-success' : (receivedPct > 0 ? 'text-warning' : '');

                    html += `
                        <tr>
                            <td>${item.product_name || 'Unknown Product'}</td>
                            <td>${item.sku || item.item_number || '-'}</td>
                            <td class="text-center">${item.quantity_ordered || 0}</td>
                            <td class="text-center ${receivedClass}"><strong>${item.quantity_received || 0}</strong></td>
                            <td class="text-end">$${parseFloat(item.unit_cost || 0).toFixed(2)}</td>
                            <td class="text-end">$${parseFloat(item.line_total || (item.quantity_ordered * item.unit_cost) || 0).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }

            html += `
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr><td>Subtotal:</td><td class="text-end">$${parseFloat(po.subtotal || 0).toFixed(2)}</td></tr>
                            <tr><td>Shipping:</td><td class="text-end">$${parseFloat(po.shipping_cost || 0).toFixed(2)}</td></tr>
                            <tr><td>Tax:</td><td class="text-end">$${parseFloat(po.tax || 0).toFixed(2)}</td></tr>
                            <tr><th>Total:</th><th class="text-end">$${parseFloat(po.total_cost || 0).toFixed(2)}</th></tr>
                        </table>
                    </div>
                </div>
            `;

            if (po.notes) {
                html += `<p><strong>Notes:</strong> ${po.notes}</p>`;
            }

            document.getElementById('poDetailContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('poDetailModal')).show();
        } else {
            alert('Error: ' + (data.message || 'Failed to load purchase order details'));
        }
    } catch (error) {
        console.error('Error loading PO details:', error);
        alert('Error loading purchase order details');
    }
}

async function editPO(id) {
    // Load the PO data and populate the form for editing
    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/${id}`);
        const data = await response.json();

        if (data.success) {
            const po = data.data;

            // Populate the form
            document.getElementById('poId').value = po.id;
            document.getElementById('poModalTitle').textContent = 'Edit Purchase Order: ' + po.po_number;
            document.getElementById('supplierName').value = po.supplier_name;
            document.getElementById('supplierEmail').value = po.supplier_email || '';
            document.getElementById('supplierPhone').value = po.supplier_phone || '';
            document.getElementById('supplierAddress').value = po.supplier_address || '';
            document.getElementById('orderDate').value = po.order_date.split('T')[0];
            document.getElementById('expectedDelivery').value = po.expected_delivery_date ? po.expected_delivery_date.split('T')[0] : '';
            document.getElementById('poNotes').value = po.notes || '';
            document.getElementById('shippingCost').value = po.shipping_cost || 0;
            document.getElementById('taxAmount').value = po.tax || 0;

            // Clear existing items and add PO items
            const tbody = document.getElementById('itemsTableBody');
            tbody.innerHTML = '';

            const items = po.items || [];
            if (items.length > 0) {
                items.forEach((item, index) => {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>
                            <select class="form-control form-control-sm product-select" onchange="updateLineItem(${index})" required>
                                <option value="">Select product...</option>
                                ${allProducts.map(p => `<option value="${p.id}" data-price="${p.cost_price || p.SalePrice}" ${p.id == item.product_id ? 'selected' : ''}>${p.ShortDescription} (${p.UPC})</option>`).join('')}
                            </select>
                        </td>
                        <td><input type="number" class="form-control form-control-sm qty-input" min="1" value="${item.quantity_ordered}" onchange="updateLineItem(${index})" required></td>
                        <td><input type="number" class="form-control form-control-sm cost-input" min="0" step="0.01" value="${parseFloat(item.unit_cost).toFixed(2)}" onchange="updateLineItem(${index})" required></td>
                        <td class="line-total text-end">$${parseFloat(item.line_total || item.quantity_ordered * item.unit_cost).toFixed(2)}</td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeLineItem(this)"><i class="bi bi-x"></i></button></td>
                    `;
                });
            } else {
                addLineItem();
            }

            calculateTotals();

            const modalElement = document.getElementById('poModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    } catch (error) {
        console.error('Error loading PO for edit:', error);
        alert('Error loading purchase order for editing');
    }
}

async function deletePO(id) {
    if (!confirm('Are you sure you want to delete this purchase order?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            alert('Purchase order deleted successfully');
            loadPOs();
            loadStats();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete purchase order'));
        }
    } catch (error) {
        console.error('Error deleting PO:', error);
        alert('Error deleting purchase order');
    }
}
</script>
@endpush
