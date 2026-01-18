@extends('layouts.admin')

@section('title', 'Wholesale Account Details')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">{{ $account->account_number ?? 'Account' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.wholesale') }}">Wholesale</a></li>
                <li class="breadcrumb-item active">{{ $account->account_number ?? '' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal" title="Create a new wholesale order">
            <i class="bi bi-plus-lg me-1"></i> New Order
        </button>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAccountModal" title="Edit account details">
            <i class="bi bi-pencil me-1"></i> Edit
        </button>
    </div>
</div>
@endsection

@section('content')
@if(!$account)
<div class="alert alert-danger">Account not found</div>
@else
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Account Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $account->business_name }}</h5>
                    <div class="d-flex gap-2">
                        @php
                            $tierColors = ['bronze' => 'warning', 'silver' => 'secondary', 'gold' => 'warning', 'platinum' => 'info'];
                            $statusColors = ['pending' => 'warning', 'approved' => 'success', 'suspended' => 'danger', 'closed' => 'secondary'];
                        @endphp
                        <span class="badge bg-{{ $tierColors[$account->tier] ?? 'secondary' }}">
                            <i class="bi bi-award me-1"></i>{{ ucfirst($account->tier) }}
                        </span>
                        <span class="badge bg-{{ $statusColors[$account->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$account->status] ?? 'secondary' }}">
                            {{ ucfirst($account->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @if($account->business_type)
                        <p class="mb-2"><i class="bi bi-building me-2 text-muted"></i>{{ $account->business_type }}</p>
                        @endif
                        @if($account->tax_id)
                        <p class="mb-2"><i class="bi bi-file-text me-2 text-muted"></i>Tax ID: {{ $account->tax_id }}</p>
                        @endif
                        @if($account->primary_contact_name)
                        <p class="mb-2"><i class="bi bi-person me-2 text-muted"></i>{{ $account->primary_contact_name }}</p>
                        @endif
                        @if($account->primary_contact_email)
                        <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $account->primary_contact_email }}</p>
                        @endif
                        @if($account->primary_contact_phone)
                        <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $account->primary_contact_phone }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><i class="bi bi-percent me-2 text-muted"></i>Discount: <strong>{{ $account->discount_percentage }}%</strong></p>
                        <p class="mb-2"><i class="bi bi-credit-card me-2 text-muted"></i>Credit Limit: <strong>${{ number_format($account->credit_limit) }}</strong></p>
                        <p class="mb-2"><i class="bi bi-cash me-2 text-muted"></i>Current Balance: <strong class="{{ ($account->current_balance ?? 0) > 0 ? 'text-danger' : 'text-success' }}">${{ number_format($account->current_balance ?? 0) }}</strong></p>
                        @if($account->payment_terms)
                        <p class="mb-2"><i class="bi bi-calendar-check me-2 text-muted"></i>Payment Terms: {{ $account->payment_terms }}</p>
                        @endif
                    </div>
                </div>
                @if($account->billing_address)
                <hr>
                <h6>Billing Address</h6>
                <p class="text-muted mb-0">{{ $account->billing_address }}</p>
                @endif
                @if($account->shipping_address)
                <h6 class="mt-3">Shipping Address</h6>
                <p class="text-muted mb-0">{{ $account->shipping_address }}</p>
                @endif
                @if($account->notes)
                <hr>
                <h6>Notes</h6>
                <p class="text-muted mb-0">{{ $account->notes }}</p>
                @endif
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-cart me-2"></i>Wholesale Orders</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Order #</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            @php $order = (object) $order; @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold">{{ $order->order_number }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div>${{ number_format($order->total_amount, 2) }}</div>
                                    @if($order->discount_amount > 0)
                                    <small class="text-success">-${{ number_format($order->discount_amount, 2) }} discount</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $orderStatusColors = ['pending' => 'warning', 'confirmed' => 'info', 'processing' => 'primary', 'shipped' => 'success', 'delivered' => 'success', 'cancelled' => 'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $orderStatusColors[$order->status] ?? 'secondary' }}-subtle text-{{ $orderStatusColors[$order->status] ?? 'secondary' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewOrder({{ $order->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-cart fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No orders yet</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tier</label>
                    <select class="form-select" id="tierSelect" onchange="updateAccount('tier', this.value)">
                        <option value="bronze" {{ $account->tier == 'bronze' ? 'selected' : '' }}>Bronze (10%)</option>
                        <option value="silver" {{ $account->tier == 'silver' ? 'selected' : '' }}>Silver (15%)</option>
                        <option value="gold" {{ $account->tier == 'gold' ? 'selected' : '' }}>Gold (20%)</option>
                        <option value="platinum" {{ $account->tier == 'platinum' ? 'selected' : '' }}>Platinum (25%)</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    @if($account->status === 'pending')
                    <button class="btn btn-success" onclick="approveAccount()">
                        <i class="bi bi-check-lg me-1"></i> Approve Account
                    </button>
                    @endif
                    @if($account->status === 'approved')
                    <button class="btn btn-outline-danger" onclick="suspendAccount()">
                        <i class="bi bi-pause-circle me-1"></i> Suspend Account
                    </button>
                    @endif
                    @if($account->status === 'suspended')
                    <button class="btn btn-success" onclick="reactivateAccount()">
                        <i class="bi bi-play-circle me-1"></i> Reactivate Account
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Credit Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Credit Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Credit Used</span>
                        <span>${{ number_format($account->current_balance ?? 0) }} / ${{ number_format($account->credit_limit) }}</span>
                    </div>
                    @php
                        $creditUsedPercent = $account->credit_limit > 0 ? min(100, (($account->current_balance ?? 0) / $account->credit_limit) * 100) : 0;
                    @endphp
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $creditUsedPercent > 80 ? 'danger' : ($creditUsedPercent > 50 ? 'warning' : 'success') }}"
                             style="width: {{ $creditUsedPercent }}%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Available Credit:</span>
                    <strong class="text-success">${{ number_format(max(0, $account->credit_limit - ($account->current_balance ?? 0))) }}</strong>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Account Stats</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="fs-4 fw-bold text-primary">{{ count($orders) }}</div>
                            <small class="text-muted">Total Orders</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded">
                            @php
                                $totalSpent = collect($orders)->sum('total_amount');
                            @endphp
                            <div class="fs-4 fw-bold text-success">${{ number_format($totalSpent, 0) }}</div>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <p class="mb-2 small">
                    <strong>Created:</strong><br>
                    {{ \Carbon\Carbon::parse($account->created_at)->format('M d, Y g:i A') }}
                </p>
                @if($account->approved_at)
                <p class="mb-2 small">
                    <strong>Approved:</strong><br>
                    {{ \Carbon\Carbon::parse($account->approved_at)->format('M d, Y g:i A') }}
                </p>
                @endif
                @if($account->last_order_at)
                <p class="mb-2 small">
                    <strong>Last Order:</strong><br>
                    {{ \Carbon\Carbon::parse($account->last_order_at)->format('M d, Y g:i A') }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Create Wholesale Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="orderForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">PO Number</label>
                            <input type="text" class="form-control" id="poNumber" name="po_number" placeholder="Customer PO #">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="totalAmount" name="total_amount" step="0.01" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shippingAddress" name="shipping_address" rows="2">{{ $account->shipping_address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="orderNotes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between">
                            <span>Discount ({{ $account->discount_percentage }}%):</span>
                            <span id="discountDisplay">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Net Amount:</span>
                            <span id="netAmountDisplay">$0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Edit Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAccountForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Business Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editBusinessName" value="{{ $account->business_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Type</label>
                            <select class="form-select" id="editBusinessType">
                                <option value="">Select type</option>
                                <option value="Retail" {{ $account->business_type == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Distributor" {{ $account->business_type == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                <option value="Educational" {{ $account->business_type == 'Educational' ? 'selected' : '' }}>Educational</option>
                                <option value="Manufacturer" {{ $account->business_type == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                <option value="Other" {{ $account->business_type == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tax ID</label>
                            <input type="text" class="form-control" id="editTaxId" value="{{ $account->tax_id }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Credit Limit</label>
                            <input type="number" class="form-control" id="editCreditLimit" value="{{ $account->credit_limit }}" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Contact Name</label>
                            <input type="text" class="form-control" id="editContactName" value="{{ $account->primary_contact_name }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Contact Email</label>
                            <input type="email" class="form-control" id="editContactEmail" value="{{ $account->primary_contact_email }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Contact Phone</label>
                            <input type="tel" class="form-control" id="editContactPhone" value="{{ $account->primary_contact_phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Terms</label>
                            <select class="form-select" id="editPaymentTerms">
                                <option value="">Select terms</option>
                                <option value="Net 15" {{ $account->payment_terms == 'Net 15' ? 'selected' : '' }}>Net 15</option>
                                <option value="Net 30" {{ $account->payment_terms == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                                <option value="Net 45" {{ $account->payment_terms == 'Net 45' ? 'selected' : '' }}>Net 45</option>
                                <option value="Net 60" {{ $account->payment_terms == 'Net 60' ? 'selected' : '' }}>Net 60</option>
                                <option value="Due on Receipt" {{ $account->payment_terms == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Billing Address</label>
                            <textarea class="form-control" id="editBillingAddress" rows="2">{{ $account->billing_address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="editShippingAddress" rows="2">{{ $account->shipping_address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="editNotes" rows="2">{{ $account->notes }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';
const ACCOUNT_ID = {{ $account->id ?? 0 }};
const DISCOUNT_PERCENT = {{ $account->discount_percentage ?? 0 }};

// Update discount display when total amount changes
document.getElementById('totalAmount').addEventListener('input', function() {
    const total = parseFloat(this.value) || 0;
    const discount = total * (DISCOUNT_PERCENT / 100);
    const net = total - discount;

    document.getElementById('discountDisplay').textContent = '$' + discount.toFixed(2);
    document.getElementById('netAmountDisplay').textContent = '$' + net.toFixed(2);
});

async function updateAccount(field, value) {
    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${ACCOUNT_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ [field]: value })
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to update');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

async function approveAccount() {
    if (!confirm('Approve this wholesale account?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${ACCOUNT_ID}/approve`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            alert('Account approved');
            location.reload();
        } else {
            alert('Failed to approve account');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

async function suspendAccount() {
    const reason = prompt('Reason for suspension:');
    if (!reason) return;

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${ACCOUNT_ID}/suspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });

        if (response.ok) {
            alert('Account suspended');
            location.reload();
        } else {
            alert('Failed to suspend account');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

async function reactivateAccount() {
    if (!confirm('Reactivate this account?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${ACCOUNT_ID}/approve`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            alert('Account reactivated');
            location.reload();
        } else {
            alert('Failed to reactivate account');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

function viewOrder(orderId) {
    alert('Order details view coming soon');
}

document.getElementById('orderForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const totalAmount = parseFloat(document.getElementById('totalAmount').value);
    const discountAmount = totalAmount * (DISCOUNT_PERCENT / 100);

    const data = {
        po_number: document.getElementById('poNumber').value || null,
        total_amount: totalAmount,
        discount_amount: discountAmount,
        shipping_address: document.getElementById('shippingAddress').value || null,
        notes: document.getElementById('orderNotes').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${ACCOUNT_ID}/orders`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Order created successfully');
            location.reload();
        } else {
            const error = await response.json();
            alert(error.message || 'Failed to create order');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
});

document.getElementById('editAccountForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = {
        business_name: document.getElementById('editBusinessName').value,
        business_type: document.getElementById('editBusinessType').value || null,
        tax_id: document.getElementById('editTaxId').value || null,
        credit_limit: document.getElementById('editCreditLimit').value || null,
        primary_contact_name: document.getElementById('editContactName').value || null,
        primary_contact_email: document.getElementById('editContactEmail').value || null,
        primary_contact_phone: document.getElementById('editContactPhone').value || null,
        payment_terms: document.getElementById('editPaymentTerms').value || null,
        billing_address: document.getElementById('editBillingAddress').value || null,
        shipping_address: document.getElementById('editShippingAddress').value || null,
        notes: document.getElementById('editNotes').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${ACCOUNT_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to update account');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
});
</script>
@endpush
@endif
@endsection
