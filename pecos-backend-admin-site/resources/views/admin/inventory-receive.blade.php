@extends('layouts.admin')

@section('title', 'Receive Inventory')

@section('content')
<div class="page-header">
    <h1>Receive Inventory</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Receive Inventory</li>
        </ol>
    </nav>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Select a purchase order below to receive incoming inventory. Scan or manually enter product UPCs to quickly process received items.
</div>

<!-- Pending Purchase Orders -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Pending Purchase Orders</h5>
    </div>
    <div class="card-body">
        <div id="pendingPOsList">
            <div class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading pending orders...
            </div>
        </div>
    </div>
</div>

<!-- Receiving Interface (shown when PO selected) -->
<div id="receivingInterface" style="display: none;">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Receiving: <span id="currentPONumber"></span></h5>
                <button class="btn btn-sm btn-secondary" onclick="cancelReceiving()">Cancel</button>
            </div>
        </div>
        <div class="card-body">
            <!-- Supplier Info -->
            <div class="mb-3">
                <strong>Supplier:</strong> <span id="supplierInfo"></span>
            </div>

            <!-- UPC Scanner -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Scan or Enter UPC</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="upcInput" placeholder="Scan barcode or enter UPC..." autofocus>
                        <button class="btn btn-prt" onclick="findProductByUPC()">
                            <i class="bi bi-search"></i> Find
                        </button>
                    </div>
                </div>
            </div>

            <!-- Items to Receive -->
            <h6>Items in this Purchase Order</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU/UPC</th>
                            <th>Ordered</th>
                            <th>Already Received</th>
                            <th>Remaining</th>
                            <th>Receive Now</th>
                            <th>Condition</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="receiveItemsTable">
                        <!-- Items will be populated here -->
                    </tbody>
                </table>
            </div>

            <!-- Receive Button -->
            <div class="text-end mt-3">
                <button class="btn btn-success btn-lg" onclick="processReceiving()">
                    <i class="bi bi-check-circle"></i> Process Receiving
                </button>
            </div>
        </div>
    </div>

    <!-- Receiving Log -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Receiving History for this Session</h6>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody id="receivingLogTable">
                    <tr>
                        <td colspan="4" class="text-center text-muted">No items received yet</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.po-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.2s;
}

.po-card:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.po-card.selected {
    background-color: #e3f2fd;
    border-color: #0d6efd;
}

#upcInput {
    font-size: 1.2rem;
    padding: 12px;
}

.qty-to-receive {
    width: 100px;
    font-size: 1rem;
    font-weight: bold;
}

.condition-select {
    width: 130px;
}

.item-notes {
    width: 150px;
}

.table tbody tr.highlight-row {
    background-color: #fff3cd !important;
    animation: highlightPulse 1s ease-in-out;
}

@keyframes highlightPulse {
    0%, 100% { background-color: #fff3cd; }
    50% { background-color: #ffc107; }
}
</style>

@endsection

@push('scripts')
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let currentPO = null;
let receivingLog = [];

document.addEventListener('DOMContentLoaded', function() {
    loadPendingPOs();

    // Handle Enter key in UPC input
    document.getElementById('upcInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            findProductByUPC();
        }
    });
});

async function loadPendingPOs() {
    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/pending-receiving`);
        const data = await response.json();

        if (data.success) {
            renderPendingPOs(data.data);
        }
    } catch (error) {
        console.error('Error loading pending POs:', error);
        document.getElementById('pendingPOsList').innerHTML =
            '<div class="alert alert-danger">Error loading pending orders</div>';
    }
}

function renderPendingPOs(pos) {
    const container = document.getElementById('pendingPOsList');

    if (pos.length === 0) {
        container.innerHTML = '<div class="alert alert-success">No pending purchase orders to receive.</div>';
        return;
    }

    let html = '';
    pos.forEach(po => {
        const orderDate = new Date(po.order_date).toLocaleDateString();
        const expectedDate = po.expected_delivery_date ? new Date(po.expected_delivery_date).toLocaleDateString() : '-';

        html += `
            <div class="po-card" onclick="selectPOForReceiving(${po.id})">
                <div class="row">
                    <div class="col-md-3">
                        <strong>${po.po_number}</strong><br>
                        <small class="text-muted">${po.supplier_name}</small>
                    </div>
                    <div class="col-md-3">
                        <small>Order Date: ${orderDate}</small><br>
                        <small>Expected: ${expectedDate}</small>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-info">${po.status.replace('_', ' ')}</span>
                    </div>
                    <div class="col-md-2">
                        <strong>${po.pending_items_count}</strong> items pending
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-sm btn-primary">Select</button>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

async function selectPOForReceiving(poId) {
    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/${poId}`);
        const data = await response.json();

        if (data.success) {
            currentPO = data.data;
            showReceivingInterface();
        }
    } catch (error) {
        console.error('Error loading PO:', error);
        alert('Error loading purchase order details');
    }
}

function showReceivingInterface() {
    document.getElementById('currentPONumber').textContent = currentPO.po_number;
    document.getElementById('supplierInfo').textContent = currentPO.supplier_name;

    // Render items
    const tbody = document.getElementById('receiveItemsTable');
    let html = '';

    currentPO.items.forEach(item => {
        const remaining = item.quantity_ordered - item.quantity_received;

        if (remaining > 0) {
            html += `
                <tr id="item-row-${item.id}" data-upc="${item.sku}">
                    <td>${item.product_name}</td>
                    <td><code>${item.sku}</code></td>
                    <td>${item.quantity_ordered}</td>
                    <td>${item.quantity_received}</td>
                    <td><strong>${remaining}</strong></td>
                    <td>
                        <input type="number" class="form-control form-control-sm qty-to-receive"
                            id="qty-${item.id}"
                            data-item-id="${item.id}"
                            min="0" max="${remaining}" value="0">
                    </td>
                    <td>
                        <select class="form-select form-select-sm condition-select" id="condition-${item.id}">
                            <option value="good">Good</option>
                            <option value="damaged">Damaged</option>
                            <option value="defective">Defective</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm item-notes"
                            id="notes-${item.id}" placeholder="Notes...">
                    </td>
                </tr>
            `;
        }
    });

    if (html === '') {
        html = '<tr><td colspan="8" class="text-center">All items have been received</td></tr>';
    }

    tbody.innerHTML = html;

    // Show interface
    document.getElementById('receivingInterface').style.display = 'block';

    // Reset receiving log
    receivingLog = [];
    document.getElementById('receivingLogTable').innerHTML =
        '<tr><td colspan="4" class="text-center text-muted">No items received yet</td></tr>';

    // Focus UPC input
    setTimeout(() => document.getElementById('upcInput').focus(), 100);
}

function cancelReceiving() {
    if (receivingLog.length > 0) {
        if (!confirm('You have items in the receiving log. Cancel receiving?')) {
            return;
        }
    }

    document.getElementById('receivingInterface').style.display = 'none';
    currentPO = null;
    loadPendingPOs();
}

function findProductByUPC() {
    const upc = document.getElementById('upcInput').value.trim();

    if (!upc) {
        alert('Please enter a UPC');
        return;
    }

    // Find matching row
    const rows = document.querySelectorAll('#receiveItemsTable tr');
    let found = false;

    rows.forEach(row => {
        const rowUPC = row.getAttribute('data-upc');
        if (rowUPC && rowUPC === upc) {
            found = true;

            // Highlight row
            row.classList.add('highlight-row');
            setTimeout(() => row.classList.remove('highlight-row'), 2000);

            // Focus and increment quantity input
            const itemId = row.querySelector('.qty-to-receive').getAttribute('data-item-id');
            const qtyInput = document.getElementById(`qty-${itemId}`);
            const currentQty = parseInt(qtyInput.value) || 0;
            const maxQty = parseInt(qtyInput.max);

            if (currentQty < maxQty) {
                qtyInput.value = currentQty + 1;
                qtyInput.focus();
                qtyInput.select();
            } else {
                alert('Maximum quantity for this item already entered');
            }

            // Scroll to row
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    if (!found) {
        alert(`UPC "${upc}" not found in this purchase order`);
    }

    // Clear UPC input
    document.getElementById('upcInput').value = '';
    document.getElementById('upcInput').focus();
}

async function processReceiving() {
    // Collect items to receive
    const itemsToReceive = [];

    currentPO.items.forEach(item => {
        const qtyInput = document.getElementById(`qty-${item.id}`);

        if (qtyInput) {
            const qty = parseInt(qtyInput.value) || 0;

            if (qty > 0) {
                const condition = document.getElementById(`condition-${item.id}`).value;
                const notes = document.getElementById(`notes-${item.id}`).value;

                itemsToReceive.push({
                    purchase_order_item_id: item.id,
                    quantity_received: qty,
                    condition: condition,
                    notes: notes || null
                });
            }
        }
    });

    if (itemsToReceive.length === 0) {
        alert('Please enter quantities to receive');
        return;
    }

    // Confirm
    const totalQty = itemsToReceive.reduce((sum, item) => sum + item.quantity_received, 0);
    if (!confirm(`Receive ${totalQty} items from ${itemsToReceive.length} product(s)?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/purchase-orders/${currentPO.id}/receive`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ items: itemsToReceive })
        });

        const data = await response.json();

        if (data.success) {
            // Add to receiving log
            itemsToReceive.forEach(item => {
                const poItem = currentPO.items.find(i => i.id === item.purchase_order_item_id);
                receivingLog.push({
                    time: new Date().toLocaleTimeString(),
                    product: poItem.product_name,
                    quantity: item.quantity_received,
                    condition: item.condition
                });
            });

            updateReceivingLog();

            alert(`Successfully received ${totalQty} items!\nPO Status: ${data.data.status}`);

            if (data.data.fully_received) {
                // PO fully received, return to pending list
                document.getElementById('receivingInterface').style.display = 'none';
                currentPO = null;
                loadPendingPOs();
            } else {
                // Reload PO to show updated quantities
                selectPOForReceiving(currentPO.id);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to receive items'));
        }
    } catch (error) {
        console.error('Error processing receiving:', error);
        alert('Error processing receiving');
    }
}

function updateReceivingLog() {
    const tbody = document.getElementById('receivingLogTable');

    if (receivingLog.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No items received yet</td></tr>';
        return;
    }

    let html = '';
    receivingLog.forEach(log => {
        let conditionClass = 'success';
        if (log.condition === 'damaged') conditionClass = 'warning';
        if (log.condition === 'defective') conditionClass = 'danger';

        html += `
            <tr>
                <td>${log.time}</td>
                <td>${log.product}</td>
                <td><strong>${log.quantity}</strong></td>
                <td><span class="badge bg-${conditionClass}">${log.condition}</span></td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}
</script>
@endpush
