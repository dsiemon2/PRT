@extends('layouts.admin')

@section('title', 'Loyalty Program')

@section('content')
<div class="page-header">
    <h1>Loyalty Program</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Loyalty</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-people"></i>
            </div>
            <div class="value">{{ number_format($stats['total_members'] ?? 0) }}</div>
            <div class="label">Total Members</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-star"></i>
            </div>
            <div class="value">{{ number_format($stats['total_points_issued'] ?? 0) }}</div>
            <div class="label">Points Issued</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-gift"></i>
            </div>
            <div class="value">{{ number_format($stats['total_points_redeemed'] ?? 0) }}</div>
            <div class="label">Points Redeemed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-trophy"></i>
            </div>
            <div class="value">{{ number_format($stats['points_outstanding'] ?? 0) }}</div>
            <div class="label">Outstanding Points</div>
        </div>
    </div>
</div>

<!-- Members Table -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Loyalty Members</h5>
    </div>
    <div class="card-body">
        <form class="row mb-3" method="GET">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search members..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="tier">
                    <option value="">All Tiers</option>
                    <option value="bronze" {{ ($filters['tier'] ?? '') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="silver" {{ ($filters['tier'] ?? '') == 'silver' ? 'selected' : '' }}>Silver</option>
                    <option value="gold" {{ ($filters['tier'] ?? '') == 'gold' ? 'selected' : '' }}>Gold</option>
                    <option value="platinum" {{ ($filters['tier'] ?? '') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-prt w-100">Filter</button>
            </div>
        </form>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Tier</th>
                    <th>Points Balance</th>
                    <th>Lifetime Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members['data'] ?? [] as $member)
                @php
                    $m = is_object($member) ? $member : (object)$member;
                    $tierColors = [
                        'bronze' => 'background: #cd7f32;',
                        'silver' => 'background: #6c757d;',
                        'gold' => 'background: #ffc107; color: #000;',
                        'platinum' => 'background: #343a40;',
                    ];
                    $tierStyle = $tierColors[$m->tier_level ?? 'bronze'] ?? 'background: #6c757d;';
                @endphp
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>{{ ($m->first_name ?? '') . ' ' . ($m->last_name ?? '') ?: $m->email }}</td>
                    <td><span class="badge" style="{{ $tierStyle }}">{{ ucfirst($m->tier_level ?? 'Bronze') }}</span></td>
                    <td>{{ number_format($m->points_balance ?? 0) }}</td>
                    <td>{{ number_format($m->lifetime_points ?? 0) }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewMember({{ $m->user_id }}, '{{ addslashes(($m->first_name ?? '') . ' ' . ($m->last_name ?? '')) }}', '{{ $m->email ?? '' }}')" title="View Transactions" data-bs-toggle="tooltip"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-success" onclick="openAdjustModal({{ $m->user_id }}, '{{ addslashes(($m->first_name ?? '') . ' ' . ($m->last_name ?? '')) }}', {{ $m->points_balance ?? 0 }})" title="Adjust Points" data-bs-toggle="tooltip"><i class="bi bi-plus-circle"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No loyalty members found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if(isset($members['last_page']) && $members['last_page'] > 1)
<nav class="mt-4 mb-4">
    <ul class="pagination justify-content-center">
        <li class="page-item {{ $members['current_page'] == 1 ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $members['current_page'] - 1 }}">Previous</a>
        </li>
        @for($i = 1; $i <= min($members['last_page'], 5); $i++)
        <li class="page-item {{ $members['current_page'] == $i ? 'active' : '' }}">
            <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
        </li>
        @endfor
        <li class="page-item {{ $members['current_page'] == $members['last_page'] ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $members['current_page'] + 1 }}">Next</a>
        </li>
    </ul>
</nav>
@endif

<!-- Tier Configuration -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Tier Configuration</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tier</th>
                        <th>Min Points</th>
                        <th>Earning Multiplier</th>
                        <th>Benefits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tiers as $tier)
                    @php
                        $t = is_object($tier) ? $tier : (object)$tier;
                        $tierColors = [
                            'bronze' => 'background: #cd7f32;',
                            'silver' => 'background: #6c757d;',
                            'gold' => 'background: #ffc107; color: #000;',
                            'platinum' => 'background: #343a40;',
                        ];
                        $tierStyle = $tierColors[$t->tier_name ?? 'bronze'] ?? 'background: #6c757d;';
                    @endphp
                    <tr onclick="highlightRow(event)" style="cursor: pointer;">
                        <td><span class="badge" style="{{ $tierStyle }}">{{ ucfirst($t->tier_name ?? 'Unknown') }}</span></td>
                        <td>{{ number_format($t->min_lifetime_points ?? $t->min_points ?? 0) }}</td>
                        <td>{{ $t->points_multiplier ?? 1 }}x</td>
                        <td>{{ str_replace('|', ', ', $t->benefits ?? 'N/A') }}</td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="openEditTierModal({{ $t->id ?? 0 }}, '{{ $t->tier_name ?? '' }}', {{ $t->min_lifetime_points ?? $t->min_points ?? 0 }}, {{ $t->points_multiplier ?? 1 }}, '{{ addslashes($t->benefits ?? '') }}')" title="Edit Tier" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></button></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No tiers configured</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Member Modal -->
<div class="modal fade" id="viewMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Name:</strong> <span id="viewMemberName"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong> <span id="viewMemberEmail"></span>
                    </div>
                </div>
                <h6>Recent Transactions</h6>
                <div id="memberTransactions">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Points Modal -->
<div class="modal fade" id="adjustPointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Points</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adjustPointsForm">
                    <input type="hidden" id="adjustUserId">
                    <div class="mb-3">
                        <label class="form-label">Member</label>
                        <input type="text" class="form-control" id="adjustMemberName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Balance</label>
                        <input type="text" class="form-control" id="adjustCurrentBalance" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Points to Add/Remove</label>
                        <input type="number" class="form-control" id="adjustPoints" required placeholder="Use negative for deductions">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" class="form-control" id="adjustReason" required placeholder="e.g., Manual adjustment, Promotion">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="adjustPoints()">Apply Adjustment</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tier Modal -->
<div class="modal fade" id="editTierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTierForm">
                    <input type="hidden" id="editTierId">
                    <div class="mb-3">
                        <label class="form-label">Tier Name</label>
                        <input type="text" class="form-control" id="editTierName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Lifetime Points *</label>
                        <input type="number" class="form-control" id="editMinPoints" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Points Multiplier *</label>
                        <input type="number" class="form-control" id="editMultiplier" required min="1" max="10" step="0.1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Benefits (pipe-separated)</label>
                        <textarea class="form-control" id="editBenefits" rows="3" placeholder="e.g., Free shipping|10% discount|Early access"></textarea>
                        <small class="text-muted">Separate benefits with | character</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTier()">Save Changes</button>
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
let viewMemberModal, adjustPointsModal, editTierModal;

document.addEventListener('DOMContentLoaded', function() {
    viewMemberModal = new bootstrap.Modal(document.getElementById('viewMemberModal'));
    adjustPointsModal = new bootstrap.Modal(document.getElementById('adjustPointsModal'));
    editTierModal = new bootstrap.Modal(document.getElementById('editTierModal'));

    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
});

async function viewMember(userId, name, email) {
    document.getElementById('viewMemberName').textContent = name || email;
    document.getElementById('viewMemberEmail').textContent = email;
    document.getElementById('memberTransactions').innerHTML = '<p class="text-muted">Loading...</p>';
    viewMemberModal.show();

    try {
        const response = await fetch(`${API_BASE}/admin/loyalty/members/${userId}/transactions`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            let html = '<table class="table table-sm"><thead><tr><th>Date</th><th>Type</th><th>Points</th><th>Description</th></tr></thead><tbody>';
            result.data.slice(0, 10).forEach(tx => {
                const date = new Date(tx.created_at).toLocaleDateString();
                const typeClass = tx.transaction_type === 'earned' || tx.transaction_type === 'bonus' ? 'text-success' : 'text-danger';
                const sign = tx.transaction_type === 'redeemed' ? '-' : '+';
                html += `<tr>
                    <td>${date}</td>
                    <td><span class="badge bg-${tx.transaction_type === 'earned' ? 'success' : tx.transaction_type === 'bonus' ? 'info' : 'warning'}">${tx.transaction_type}</span></td>
                    <td class="${typeClass}">${sign}${tx.points}</td>
                    <td>${tx.description || 'N/A'}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('memberTransactions').innerHTML = html;
        } else {
            document.getElementById('memberTransactions').innerHTML = '<p class="text-muted">No transactions found</p>';
        }
    } catch (error) {
        document.getElementById('memberTransactions').innerHTML = '<p class="text-danger">Error loading transactions</p>';
    }
}

function openAdjustModal(userId, name, currentBalance) {
    document.getElementById('adjustUserId').value = userId;
    document.getElementById('adjustMemberName').value = name || 'Member #' + userId;
    document.getElementById('adjustCurrentBalance').value = currentBalance + ' points';
    document.getElementById('adjustPoints').value = '';
    document.getElementById('adjustReason').value = '';
    adjustPointsModal.show();
}

async function adjustPoints() {
    const userId = document.getElementById('adjustUserId').value;
    const points = parseInt(document.getElementById('adjustPoints').value);
    const reason = document.getElementById('adjustReason').value;

    if (!points || !reason) {
        alert('Please fill in all fields');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/loyalty/adjust-points`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                user_id: parseInt(userId),
                points: points,
                reason: reason
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(`Points adjusted successfully! New balance: ${result.data.new_balance}`);
            adjustPointsModal.hide();
            window.location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to adjust points'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adjusting points: ' + error.message);
    }
}

function openEditTierModal(tierId, tierName, minPoints, multiplier, benefits) {
    document.getElementById('editTierId').value = tierId;
    document.getElementById('editTierName').value = tierName.charAt(0).toUpperCase() + tierName.slice(1);
    document.getElementById('editMinPoints').value = minPoints;
    document.getElementById('editMultiplier').value = multiplier;
    document.getElementById('editBenefits').value = benefits;
    editTierModal.show();
}

async function saveTier() {
    const tierId = document.getElementById('editTierId').value;
    const minPoints = parseInt(document.getElementById('editMinPoints').value);
    const multiplier = parseFloat(document.getElementById('editMultiplier').value);
    const benefits = document.getElementById('editBenefits').value;

    if (!minPoints && minPoints !== 0) {
        alert('Please enter minimum points');
        return;
    }

    if (!multiplier) {
        alert('Please enter points multiplier');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/loyalty/tiers/${tierId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                min_lifetime_points: minPoints,
                points_multiplier: multiplier,
                benefits: benefits
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Tier updated successfully!');
            editTierModal.hide();
            window.location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to update tier'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating tier: ' + error.message);
    }
}
</script>
@endpush
