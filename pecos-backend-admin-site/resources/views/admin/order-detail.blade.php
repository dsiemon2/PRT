@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="page-header">
    <h1>Order #{{ $order['order_number'] ?? $order['order_id'] ?? 'N/A' }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}">Orders</a></li>
            <li class="breadcrumb-item active">#{{ $order['order_number'] ?? $order['order_id'] ?? 'N/A' }}</li>
        </ol>
    </nav>
</div>

@if($order)
<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order['items'] ?? [] as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3" style="width:50px;height:50px;"></div>
                                    <div>
                                        <strong>{{ $item['product_name'] ?? $item['Description'] ?? 'Unknown Product' }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item['UPC_Code'] ?? $item['sku'] ?? 'N/A' }}</td>
                            <td>${{ number_format($item['price'] ?? 0, 2) }}</td>
                            <td>{{ $item['quantity'] ?? 1 }}</td>
                            <td>${{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Subtotal:</td>
                            <td>${{ number_format($order['subtotal'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Shipping:</td>
                            <td>${{ number_format($order['shipping_cost'] ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Tax:</td>
                            <td>${{ number_format($order['tax'] ?? 0, 2) }}</td>
                        </tr>
                        @if(($order['discount'] ?? 0) > 0)
                        <tr>
                            <td colspan="4" class="text-end text-success">Discount:</td>
                            <td class="text-success">-${{ number_format($order['discount'], 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-secondary">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>${{ number_format($order['total'] ?? 0, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($order['status_history'] ?? [] as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $history['status'] == 'delivered' ? 'success' : ($history['status'] == 'shipped' ? 'primary' : 'secondary') }}"></div>
                        <div class="timeline-content">
                            <strong>{{ ucfirst($history['status'] ?? 'Unknown') }}</strong>
                            <p class="text-muted mb-0">{{ isset($history['created_at']) ? date('M d, Y g:i A', strtotime($history['created_at'])) : 'N/A' }}</p>
                            @if($history['notes'] ?? null)
                            <small>{{ $history['notes'] }}</small>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <strong>Order Placed</strong>
                            <p class="text-muted mb-0">{{ isset($order['created_at']) ? date('M d, Y g:i A', strtotime($order['created_at'])) : 'N/A' }}</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Admin Notes -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Admin Notes</h5>
            </div>
            <div class="card-body">
                @if($order['order_notes'] ?? $order['admin_notes'] ?? null)
                <div class="mb-3 p-3 bg-light rounded" id="existingNotes">
                    <p class="mb-0">{{ $order['order_notes'] ?? $order['admin_notes'] }}</p>
                </div>
                @endif
                <textarea class="form-control" rows="3" placeholder="Add a note..." id="noteText"></textarea>
                <button class="btn btn-sm btn-outline-primary mt-2" onclick="addNote()">Add Note</button>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status & Actions -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Status & Actions</h5>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong>
                    <span class="status-badge {{ ($order['status'] ?? '') == 'delivered' ? 'active' : (($order['status'] ?? '') == 'cancelled' ? 'inactive' : 'pending') }}">
                        {{ ucfirst($order['status'] ?? 'Unknown') }}
                    </span>
                </p>
                <p><strong>Payment:</strong>
                    <span class="badge bg-{{ ($order['payment_status'] ?? 'paid') == 'paid' ? 'success' : 'warning' }}">
                        {{ ucfirst($order['payment_status'] ?? 'Paid') }}
                    </span>
                </p>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Update Status</label>
                    <select class="form-select" id="orderStatus">
                        <option value="pending" {{ ($order['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ ($order['status'] ?? '') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ ($order['status'] ?? '') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ ($order['status'] ?? '') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ ($order['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="refunded" {{ ($order['status'] ?? '') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-prt" onclick="updateStatus()">Update Status</button>
                    <button class="btn btn-outline-primary" onclick="resendConfirmation()"><i class="bi bi-envelope"></i> Resend Confirmation</button>
                    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Print Invoice</button>
                    <button class="btn btn-outline-warning" onclick="issueRefund()"><i class="bi bi-arrow-return-left"></i> Issue Refund</button>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Customer</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $order['customer_name'] ?? $order['UserName'] ?? 'Guest' }}</strong></p>
                <p><i class="bi bi-envelope me-2"></i> {{ $order['customer_email'] ?? $order['Email'] ?? 'N/A' }}</p>
                <p><i class="bi bi-telephone me-2"></i> {{ $order['customer_phone'] ?? $order['Phone'] ?? 'N/A' }}</p>
                @if($order['user_id'] ?? null)
                <a href="{{ route('admin.customers.detail', $order['user_id']) }}" class="btn btn-sm btn-outline-primary">View Customer</a>
                @endif
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Shipping Address</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    @if($order['shipping_address'] ?? null)
                        @if(is_array($order['shipping_address']))
                            {{ $order['shipping_address']['name'] ?? '' }}<br>
                            {{ $order['shipping_address']['address1'] ?? '' }}<br>
                            @if($order['shipping_address']['address2'] ?? null)
                            {{ $order['shipping_address']['address2'] }}<br>
                            @endif
                            {{ $order['shipping_address']['city'] ?? '' }}, {{ $order['shipping_address']['state'] ?? '' }} {{ $order['shipping_address']['zip'] ?? '' }}
                        @else
                            {{ $order['shipping_address'] }}
                        @endif
                    @else
                        No shipping address provided
                    @endif
                </p>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body">
                <p><strong>Method:</strong> {{ ucfirst($order['payment_method'] ?? 'N/A') }}</p>
                @if($order['transaction_id'] ?? null)
                <p><strong>Transaction ID:</strong> {{ $order['transaction_id'] }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">Order not found</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 8px;
    bottom: -12px;
    width: 2px;
    background: #dee2e6;
}
.timeline-item:last-child::before {
    display: none;
}
.timeline-marker {
    position: absolute;
    left: -30px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
</style>

<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
const ORDER_ID = {{ $order['id'] ?? 0 }};

async function updateStatus() {
    const status = document.getElementById('orderStatus').value;

    try {
        const response = await fetch(`${API_BASE}/admin/orders/${ORDER_ID}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });

        const data = await response.json();

        if (data.success) {
            alert('Status updated successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update status'));
        }
    } catch (error) {
        console.error('Update status error:', error);
        alert('Error updating status');
    }
}

async function addNote() {
    const noteText = document.getElementById('noteText').value.trim();

    if (!noteText) {
        alert('Please enter a note');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/orders/${ORDER_ID}/notes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes: noteText })
        });

        const data = await response.json();

        if (data.success) {
            alert('Note added successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add note'));
        }
    } catch (error) {
        console.error('Add note error:', error);
        alert('Error adding note');
    }
}

async function issueRefund() {
    if (!confirm('Are you sure you want to issue a refund for this order?')) {
        return;
    }

    const amount = prompt('Enter refund amount (leave blank for full refund):');

    try {
        const response = await fetch(`${API_BASE}/admin/orders/${ORDER_ID}/refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                amount: amount ? parseFloat(amount) : null,
                reason: 'Admin initiated refund'
            })
        });

        const data = await response.json();

        if (data.success) {
            alert('Refund processed successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to process refund'));
        }
    } catch (error) {
        console.error('Refund error:', error);
        alert('Error processing refund');
    }
}

function resendConfirmation() {
    alert('Confirmation email would be sent to: {{ $order["customer_email"] ?? "customer" }}');
    // TODO: Implement email sending API endpoint
}
</script>
@endsection
