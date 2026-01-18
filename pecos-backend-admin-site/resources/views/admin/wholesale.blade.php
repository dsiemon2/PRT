@extends('layouts.admin')

@section('title', 'Wholesale Accounts')

@push('styles')
<style>
/* Row selection styles */
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
    color: #333 !important;
}
.table tbody tr.row-selected td strong,
.table tbody tr.row-selected td .fw-semibold {
    color: #333 !important;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Wholesale Accounts</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leads') }}">Sales Pipeline</a></li>
                <li class="breadcrumb-item active">Wholesale</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accountModal" title="Create a new wholesale account">
        <i class="bi bi-plus-lg me-1"></i> New Account
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                <small class="text-muted">Total Accounts</small>
                            </div>
                            <div class="text-primary">
                                <i class="bi bi-building fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['approved'] ?? 0 }}</h3>
                                <small class="text-muted">Approved</small>
                            </div>
                            <div class="text-success">
                                <i class="bi bi-check-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                                <small class="text-muted">Pending Approval</small>
                            </div>
                            <div class="text-warning">
                                <i class="bi bi-hourglass-split fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">${{ number_format($stats['total_credit_limit'] ?? 0) }}</h3>
                                <small class="text-muted">Total Credit</small>
                            </div>
                            <div class="text-info">
                                <i class="bi bi-credit-card fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            @php
                $tierColors = ['bronze' => 'warning', 'silver' => 'secondary', 'gold' => 'warning', 'platinum' => 'info'];
                $tierIcons = ['bronze' => 'award', 'silver' => 'award-fill', 'gold' => 'trophy', 'platinum' => 'diamond'];
            @endphp
            @foreach($tiers as $tier)
            @php
                // Handle both array and object formats
                $tierCode = is_array($tier) ? ($tier['code'] ?? '') : ($tier->code ?? '');
                $tierName = is_array($tier) ? ($tier['name'] ?? '') : ($tier->name ?? '');
                $tierDiscount = is_array($tier) ? ($tier['discount'] ?? 0) : ($tier->discount ?? 0);
                $tierCreditLimit = is_array($tier) ? ($tier['credit_limit'] ?? 0) : ($tier->credit_limit ?? 0);
            @endphp
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-{{ $tierIcons[$tierCode] ?? 'award' }} text-{{ $tierColors[$tierCode] ?? 'secondary' }} fs-4 me-2"></i>
                            <h6 class="mb-0">{{ $tierName }}</h6>
                        </div>
                        <div class="small text-muted mb-2">{{ $tierDiscount }}% discount</div>
                        <div class="small text-muted">Up to ${{ number_format($tierCreditLimit) }} credit</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.wholesale') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="suspended" {{ ($filters['status'] ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="closed" {{ ($filters['status'] ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tier</label>
                        <select name="tier" class="form-select">
                            <option value="">All Tiers</option>
                            <option value="bronze" {{ ($filters['tier'] ?? '') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                            <option value="silver" {{ ($filters['tier'] ?? '') == 'silver' ? 'selected' : '' }}>Silver</option>
                            <option value="gold" {{ ($filters['tier'] ?? '') == 'gold' ? 'selected' : '' }}>Gold</option>
                            <option value="platinum" {{ ($filters['tier'] ?? '') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Business name, account #, contact..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Account</th>
                                <th>Business</th>
                                <th class="text-center">Tier</th>
                                <th class="text-center">Discount</th>
                                <th class="text-end">Credit Limit</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="accountsTableBody">
                            @forelse($accounts['data'] ?? [] as $account)
                            @php $account = (object) $account; @endphp
                            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <a href="{{ route('admin.wholesale.detail', $account->id) }}" class="text-decoration-none">
                                        <div class="fw-semibold">{{ $account->account_number }}</div>
                                        <small class="text-muted">{{ $account->primary_contact_name ?? '-' }}</small>
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $account->business_name }}</div>
                                    <small class="text-muted">{{ $account->business_type ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $tierColors = ['bronze' => 'warning', 'silver' => 'secondary', 'gold' => 'warning', 'platinum' => 'info'];
                                    @endphp
                                    <span class="badge bg-{{ $tierColors[$account->tier] ?? 'secondary' }}">
                                        {{ ucfirst($account->tier) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $account->discount_percentage }}%</td>
                                <td class="text-end">${{ number_format($account->credit_limit) }}</td>
                                <td class="text-center">
                                    @php
                                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'suspended' => 'danger', 'closed' => 'secondary'];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$account->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$account->status] ?? 'secondary' }}">
                                        {{ ucfirst($account->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.wholesale.detail', $account->id) }}" class="btn btn-outline-primary" title="View account details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($account->status === 'pending')
                                        <button class="btn btn-outline-success" onclick="approveAccount({{ $account->id }})" title="Approve this wholesale account">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-building fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No wholesale accounts found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination -->
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small" id="accountsInfo">Showing 0 entries</div>
                    <nav aria-label="Accounts pagination">
                        <ul class="pagination pagination-sm mb-0" id="accountsPagination">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Account Modal -->
<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">New Wholesale Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="accountForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer ID <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="customerId" name="customer_id" required>
                            <small class="text-muted">Enter the existing customer ID</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="businessName" name="business_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Type</label>
                            <select class="form-select" id="businessType" name="business_type">
                                <option value="">Select type</option>
                                <option value="Retail">Retail</option>
                                <option value="Distributor">Distributor</option>
                                <option value="Educational">Educational</option>
                                <option value="Manufacturer">Manufacturer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tax ID</label>
                            <input type="text" class="form-control" id="taxId" name="tax_id">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Contact Name</label>
                            <input type="text" class="form-control" id="contactName" name="primary_contact_name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Contact Email</label>
                            <input type="email" class="form-control" id="contactEmail" name="primary_contact_email">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Primary Contact Phone</label>
                            <input type="tel" class="form-control" id="contactPhone" name="primary_contact_phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Billing Address</label>
                            <textarea class="form-control" id="billingAddress" name="billing_address" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';

// Row selection function
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;
    // Don't highlight if clicking on buttons, links, or selects
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.closest('button') || target.closest('a') || target.closest('select') ||
        target.closest('.btn-group')) {
        return;
    }
    // Remove selection from all other rows
    var selectedRows = document.querySelectorAll('.table tbody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    // Add selection to clicked row
    row.classList.add('row-selected');
}

// Sample wholesale accounts data
var sampleAccounts = [
    { id: 1, account_number: 'WH-2024-001', business_name: 'Riverside Trading Co', business_type: 'Distributor', primary_contact_name: 'Michael Chen', tier: 'gold', discount_percentage: 20, credit_limit: 50000, status: 'approved' },
    { id: 2, account_number: 'WH-2024-002', business_name: 'Mountain Supply LLC', business_type: 'Retail', primary_contact_name: 'Sarah Johnson', tier: 'silver', discount_percentage: 15, credit_limit: 25000, status: 'approved' },
    { id: 3, account_number: 'WH-2024-003', business_name: 'Pacific Coast Goods', business_type: 'Distributor', primary_contact_name: 'James Wilson', tier: 'platinum', discount_percentage: 25, credit_limit: 100000, status: 'approved' },
    { id: 4, account_number: 'WH-2024-004', business_name: 'Sunset Retail Group', business_type: 'Retail', primary_contact_name: 'Emily Davis', tier: 'bronze', discount_percentage: 10, credit_limit: 10000, status: 'pending' },
    { id: 5, account_number: 'WH-2024-005', business_name: 'Northern Star Imports', business_type: 'Distributor', primary_contact_name: 'Robert Taylor', tier: 'gold', discount_percentage: 20, credit_limit: 50000, status: 'approved' },
    { id: 6, account_number: 'WH-2024-006', business_name: 'Valley Fresh Markets', business_type: 'Retail', primary_contact_name: 'Amanda Brown', tier: 'silver', discount_percentage: 15, credit_limit: 25000, status: 'pending' },
    { id: 7, account_number: 'WH-2024-007', business_name: 'East Coast Distributors', business_type: 'Distributor', primary_contact_name: 'David Martinez', tier: 'platinum', discount_percentage: 25, credit_limit: 100000, status: 'approved' },
    { id: 8, account_number: 'WH-2024-008', business_name: 'Central Supply Network', business_type: 'Manufacturer', primary_contact_name: 'Lisa Anderson', tier: 'gold', discount_percentage: 20, credit_limit: 50000, status: 'suspended' }
];

// Pagination variables
var accountsPerPage = 5;
var currentAccountsPage = 1;
var serverAccounts = @json($accounts['data'] ?? []);
var allAccountsData = serverAccounts.length > 0 ? serverAccounts : sampleAccounts;

function renderAccountsTable() {
    var tbody = document.getElementById('accountsTableBody');
    var start = (currentAccountsPage - 1) * accountsPerPage;
    var end = start + accountsPerPage;
    var pageData = allAccountsData.slice(start, end);

    var tierColors = { bronze: 'warning', silver: 'secondary', gold: 'warning', platinum: 'info' };
    var statusColors = { pending: 'warning', approved: 'success', suspended: 'danger', closed: 'secondary' };

    var html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="7" class="text-center py-5"><div class="text-muted"><i class="bi bi-building fs-1 d-block mb-2"></i><p class="mb-0">No wholesale accounts found</p></div></td></tr>';
    } else {
        pageData.forEach(function(account) {
            html += '<tr onclick="highlightRow(event)" style="cursor: pointer;">';
            html += '<td class="ps-4"><a href="/admin/wholesale/' + account.id + '" class="text-decoration-none"><div class="fw-semibold">' + account.account_number + '</div><small class="text-muted">' + (account.primary_contact_name || '-') + '</small></a></td>';
            html += '<td><div>' + account.business_name + '</div><small class="text-muted">' + (account.business_type || 'N/A') + '</small></td>';
            html += '<td class="text-center"><span class="badge bg-' + (tierColors[account.tier] || 'secondary') + '">' + (account.tier ? account.tier.charAt(0).toUpperCase() + account.tier.slice(1) : 'N/A') + '</span></td>';
            html += '<td class="text-center">' + account.discount_percentage + '%</td>';
            html += '<td class="text-end">$' + Number(account.credit_limit).toLocaleString() + '</td>';
            html += '<td class="text-center"><span class="badge bg-' + (statusColors[account.status] || 'secondary') + '-subtle text-' + (statusColors[account.status] || 'secondary') + '">' + (account.status ? account.status.charAt(0).toUpperCase() + account.status.slice(1) : 'N/A') + '</span></td>';
            html += '<td class="text-end pe-4"><div class="btn-group btn-group-sm"><a href="/admin/wholesale/' + account.id + '" class="btn btn-outline-primary" title="View account details"><i class="bi bi-eye"></i></a>';
            if (account.status === 'pending') {
                html += '<button class="btn btn-outline-success" onclick="approveAccount(' + account.id + ')" title="Approve this wholesale account"><i class="bi bi-check-lg"></i></button>';
            }
            html += '</div></td>';
            html += '</tr>';
        });
    }
    tbody.innerHTML = html;

    // Update info text
    var total = allAccountsData.length;
    var showing = Math.min(end, total);
    document.getElementById('accountsInfo').textContent = 'Showing ' + (start + 1) + ' to ' + showing + ' of ' + total + ' entries';

    renderAccountsPagination();
}

function renderAccountsPagination() {
    var totalPages = Math.ceil(allAccountsData.length / accountsPerPage);
    var pagination = document.getElementById('accountsPagination');
    var html = '';

    // Previous button
    html += '<li class="page-item ' + (currentAccountsPage === 1 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToAccountsPage(' + (currentAccountsPage - 1) + '); return false;">Previous</a></li>';

    // Page numbers
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === currentAccountsPage ? 'active' : '') + '">';
        html += '<a class="page-link" href="#" onclick="goToAccountsPage(' + i + '); return false;">' + i + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (currentAccountsPage === totalPages ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToAccountsPage(' + (currentAccountsPage + 1) + '); return false;">Next</a></li>';

    pagination.innerHTML = html;
}

function goToAccountsPage(page) {
    var totalPages = Math.ceil(allAccountsData.length / accountsPerPage);
    if (page < 1 || page > totalPages) return;
    currentAccountsPage = page;
    renderAccountsTable();
}

// Initialize table on page load
document.addEventListener('DOMContentLoaded', function() {
    renderAccountsTable();
});

document.getElementById('accountForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = {
        customer_id: document.getElementById('customerId').value,
        business_name: document.getElementById('businessName').value,
        business_type: document.getElementById('businessType').value || null,
        tax_id: document.getElementById('taxId').value || null,
        primary_contact_name: document.getElementById('contactName').value || null,
        primary_contact_email: document.getElementById('contactEmail').value || null,
        primary_contact_phone: document.getElementById('contactPhone').value || null,
        billing_address: document.getElementById('billingAddress').value || null,
        notes: document.getElementById('notes').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Account created successfully');
            location.reload();
        } else {
            const error = await response.json();
            alert(error.message || 'Failed to create account');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
});

async function approveAccount(id) {
    if (!confirm('Approve this wholesale account?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/wholesale/${id}/approve`, {
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
</script>
@endpush
