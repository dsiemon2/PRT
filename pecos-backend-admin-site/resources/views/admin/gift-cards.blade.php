@extends('layouts.admin')

@section('title', 'Gift Cards')

@section('content')
<div class="page-header">
    <h1>Gift Card Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Gift Cards</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-gift"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Gift Cards</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value" id="stat-active">-</div>
            <div class="label">Active Cards</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value" id="stat-balance">$0</div>
            <div class="label">Total Balance</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="value" id="stat-outstanding">$0</div>
            <div class="label">Outstanding</div>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search by code or email...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="used">Used</option>
                    <option value="expired">Expired</option>
                    <option value="voided">Voided</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterGiftCards()">Filter</button>
            </div>
            <div class="col-md-5 text-end">
                <button type="button" class="btn btn-success" onclick="openCreateModal()">
                    <i class="bi bi-plus"></i> Create Gift Card
                </button>
                <a href="{{ env('API_PUBLIC_URL', 'http://localhost:8300/api/v1') }}/admin/gift-cards/export" class="btn btn-outline-secondary" target="_blank">
                    <i class="bi bi-download"></i> Export
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Gift Cards Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Code</th>
                <th>Initial</th>
                <th>Balance</th>
                <th>Recipient</th>
                <th>Purchased</th>
                <th>Expires</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="giftCardsTable">
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading gift cards...
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

<!-- Create Gift Card Modal -->
<div class="modal fade" id="createGiftCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title">Create Gift Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createGiftCardForm">
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" class="form-control" id="createAmount" min="1" max="1000" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" id="createRecipientEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recipient Name</label>
                        <input type="text" class="form-control" id="createRecipientName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Personal Message</label>
                        <textarea class="form-control" id="createMessage" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiration Date</label>
                        <input type="date" class="form-control" id="createExpiresAt">
                        <small class="text-muted">Leave empty for no expiration</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="createGiftCard()">Create Gift Card</button>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Balance Modal -->
<div class="modal fade" id="adjustBalanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title">Adjust Gift Card Balance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adjustBalanceForm">
                    <input type="hidden" id="adjustCardId">
                    <div class="mb-3">
                        <label class="form-label">Gift Card Code</label>
                        <input type="text" class="form-control" id="adjustCardCode" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Balance</label>
                        <input type="text" class="form-control" id="adjustCurrentBalance" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjustment Amount *</label>
                        <input type="number" class="form-control" id="adjustAmount" step="0.01" required placeholder="Positive to add, negative to subtract">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason *</label>
                        <input type="text" class="form-control" id="adjustReason" required placeholder="e.g., Promotional credit, Correction">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="adjustBalance()">Apply Adjustment</button>
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
let createModal, adjustModal;

document.addEventListener('DOMContentLoaded', function() {
    createModal = new bootstrap.Modal(document.getElementById('createGiftCardModal'));
    adjustModal = new bootstrap.Modal(document.getElementById('adjustBalanceModal'));
    loadStats();
    loadGiftCards();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/gift-cards/stats`);
        const data = await response.json();

        if (data.success || data.stats) {
            const stats = data.stats || data.data || data;
            document.getElementById('stat-total').textContent = Number(stats.total_cards || 0).toLocaleString();
            document.getElementById('stat-active').textContent = Number(stats.active_cards || 0).toLocaleString();
            document.getElementById('stat-balance').textContent = '$' + Number(stats.total_balance || 0).toLocaleString();
            document.getElementById('stat-outstanding').textContent = '$' + Number(stats.outstanding_balance || 0).toLocaleString();
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadGiftCards(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/gift-cards?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;

        const response = await fetch(url);
        const result = await response.json();

        // API returns {success, data: {current_page, data: [...], ...}}
        const paginated = result.data || {};
        const giftCards = paginated.data || [];
        const meta = {
            current_page: paginated.current_page || 1,
            last_page: paginated.last_page || 1,
            from: paginated.from || 1,
            to: paginated.to || giftCards.length,
            total: paginated.total || giftCards.length,
            per_page: paginated.per_page || perPage
        };

        renderGiftCards(giftCards);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading gift cards:', error);
        document.getElementById('giftCardsTable').innerHTML =
            '<tr><td colspan="8" class="text-center text-danger">Error loading gift cards</td></tr>';
    }
}

function renderGiftCards(giftCards) {
    const tbody = document.getElementById('giftCardsTable');

    if (giftCards.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="8" class="text-center py-4 text-muted">No gift cards found</td>
        </tr>`;
        return;
    }

    let html = '';
    giftCards.forEach(card => {
        const currentBalance = card.current_balance ?? card.balance ?? 0;
        const isUsed = currentBalance <= 0 || card.status === 'used';
        const isExpired = card.status === 'expired' || (card.expires_at && new Date(card.expires_at) < new Date());
        const isVoided = card.status === 'voided';

        let statusClass, statusText;
        if (isVoided) {
            statusClass = 'inactive';
            statusText = 'Voided';
        } else if (isExpired) {
            statusClass = 'pending';
            statusText = 'Expired';
        } else if (isUsed) {
            statusClass = 'inactive';
            statusText = 'Used';
        } else {
            statusClass = 'active';
            statusText = 'Active';
        }

        const initialBalance = '$' + Number(card.initial_balance || card.amount || 0).toFixed(2);
        const balanceDisplay = '$' + Number(currentBalance).toFixed(2);
        const purchaseDate = card.created_at
            ? new Date(card.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})
            : 'N/A';
        const expiresDate = card.expires_at
            ? new Date(card.expires_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})
            : 'Never';

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td><code>${card.code || 'N/A'}</code></td>`;
        html += `<td>${initialBalance}</td>`;
        html += `<td>${balanceDisplay}</td>`;
        html += `<td>${card.recipient_email || card.email || '-'}</td>`;
        html += `<td>${purchaseDate}</td>`;
        html += `<td>${expiresDate}</td>`;
        html += `<td><span class="status-badge ${statusClass}">${statusText}</span></td>`;
        html += `<td>
            <a href="/admin/gift-cards/${card.id}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
            <button class="btn btn-sm btn-outline-success" onclick="openAdjustModal(${card.id}, '${(card.code || '').replace(/'/g, "\\'")}', ${currentBalance})" ${isVoided ? 'disabled' : ''} title="Adjust Balance"><i class="bi bi-plus-circle"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="voidCard(${card.id}, '${(card.code || '').replace(/'/g, "\\'")}')" ${isUsed || isExpired || isVoided ? 'disabled' : ''} title="Void Card"><i class="bi bi-x-circle"></i></button>
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
    html += `<a class="page-link" href="#" onclick="loadGiftCards(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadGiftCards(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadGiftCards(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadGiftCards(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadGiftCards(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterGiftCards() {
    loadGiftCards(1);
}

function openCreateModal() {
    document.getElementById('createGiftCardForm').reset();
    createModal.show();
}

function openAdjustModal(cardId, code, currentBalance) {
    document.getElementById('adjustCardId').value = cardId;
    document.getElementById('adjustCardCode').value = code;
    document.getElementById('adjustCurrentBalance').value = '$' + parseFloat(currentBalance).toFixed(2);
    document.getElementById('adjustAmount').value = '';
    document.getElementById('adjustReason').value = '';
    adjustModal.show();
}

async function adjustBalance() {
    const cardId = document.getElementById('adjustCardId').value;
    const amount = parseFloat(document.getElementById('adjustAmount').value);
    const reason = document.getElementById('adjustReason').value;

    if (!amount || !reason) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/gift-cards/${cardId}/adjust`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount, reason })
        });

        const result = await response.json();

        if (result.success) {
            alert(`Balance adjusted! New balance: $${result.data.new_balance}`);
            adjustModal.hide();
            loadGiftCards(currentPage);
            loadStats();
        } else {
            alert('Error: ' + (result.message || 'Failed to adjust balance'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adjusting balance: ' + error.message);
    }
}

async function voidCard(cardId, code) {
    if (!confirm(`Are you sure you want to void gift card ${code}? This cannot be undone.`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/gift-cards/${cardId}/void`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Gift card voided successfully');
            loadGiftCards(currentPage);
            loadStats();
        } else {
            alert('Error: ' + (result.message || 'Failed to void gift card'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error voiding gift card: ' + error.message);
    }
}

async function createGiftCard() {
    const amount = parseFloat(document.getElementById('createAmount').value);
    const recipientEmail = document.getElementById('createRecipientEmail').value;
    const recipientName = document.getElementById('createRecipientName').value;
    const message = document.getElementById('createMessage').value;
    const expiresAt = document.getElementById('createExpiresAt').value;

    if (!amount || amount < 1) {
        alert('Please enter a valid amount');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/gift-cards`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                initial_balance: amount,
                recipient_email: recipientEmail || null,
                recipient_name: recipientName || null,
                message: message || null,
                expires_at: expiresAt || null
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(`Gift card created!\nCode: ${result.data.code}\nAmount: $${result.data.initial_balance}`);
            createModal.hide();
            loadGiftCards(currentPage);
            loadStats();
        } else {
            alert('Error: ' + (result.message || 'Failed to create gift card'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error creating gift card: ' + error.message);
    }
}
</script>
@endpush
