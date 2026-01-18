@extends('layouts.admin')

@section('title', 'Deals Pipeline')

@push('styles')
<style>
/* Row selection styles */
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
    color: #333 !important;
}
.table tbody tr.row-selected td strong,
.table tbody tr.row-selected td .fw-semibold,
.table tbody tr.row-selected td .fw-bold {
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
        <h1 class="h3 mb-1">Deals Pipeline</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leads') }}">Sales Pipeline</a></li>
                <li class="breadcrumb-item active">Deals</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dealModal" title="Create a new deal">
        <i class="bi bi-plus-lg me-1"></i> New Deal
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
                                <h3 class="mb-0">{{ $stats['total_open'] ?? 0 }}</h3>
                                <small class="text-muted">Open Deals</small>
                            </div>
                            <div class="text-primary">
                                <i class="bi bi-briefcase fs-2"></i>
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
                                <h3 class="mb-0">${{ number_format($stats['total_value'] ?? 0) }}</h3>
                                <small class="text-muted">Total Pipeline</small>
                            </div>
                            <div class="text-success">
                                <i class="bi bi-currency-dollar fs-2"></i>
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
                                <h3 class="mb-0">${{ number_format($stats['weighted_value'] ?? 0) }}</h3>
                                <small class="text-muted">Weighted Value</small>
                            </div>
                            <div class="text-info">
                                <i class="bi bi-calculator fs-2"></i>
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
                                <h3 class="mb-0">{{ $stats['won_this_month'] ?? 0 }}</h3>
                                <small class="text-muted">Won This Month</small>
                            </div>
                            <div class="text-warning">
                                <i class="bi bi-trophy fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline View -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-kanban me-2"></i>Pipeline View</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($pipeline as $stage)
                    @php $stage = (object) $stage; @endphp
                    <div class="col">
                        <div class="card h-100" style="border-top: 3px solid {{ $stage->stage->color ?? '#6c757d' }};">
                            <div class="card-header bg-light py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $stage->stage->name ?? 'Unknown' }}</strong>
                                    <span class="badge bg-secondary">{{ $stage->count ?? 0 }}</span>
                                </div>
                                <small class="text-muted">${{ number_format($stage->total_value ?? 0) }}</small>
                            </div>
                            <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                                @forelse($stage->deals ?? [] as $deal)
                                @php $deal = (object) $deal; @endphp
                                <a href="{{ route('admin.deals.detail', $deal->id) }}" class="card mb-2 text-decoration-none">
                                    <div class="card-body p-2">
                                        <div class="fw-semibold text-dark">{{ $deal->title }}</div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-success fw-bold">${{ number_format($deal->value) }}</small>
                                            <small class="text-muted">{{ $deal->probability }}%</small>
                                        </div>
                                        @if($deal->expected_close_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($deal->expected_close_date)->format('M d') }}
                                        </small>
                                        @endif
                                    </div>
                                </a>
                                @empty
                                <div class="text-center text-muted py-3">
                                    <small>No deals</small>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Deals Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-table me-2"></i>All Deals</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Deal</th>
                                <th>Company</th>
                                <th class="text-center">Stage</th>
                                <th class="text-end">Value</th>
                                <th class="text-center">Probability</th>
                                <th>Expected Close</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dealsTableBody">
                            @forelse($deals['data'] ?? [] as $deal)
                            @php $deal = (object) $deal; @endphp
                            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <a href="{{ route('admin.deals.detail', $deal->id) }}" class="text-decoration-none">
                                        <div class="fw-semibold">{{ $deal->title }}</div>
                                        <small class="text-muted">{{ $deal->deal_number }}</small>
                                    </a>
                                </td>
                                <td>{{ $deal->lead_company ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge" style="background-color: {{ $deal->stage_color ?? '#6c757d' }}">
                                        {{ $deal->stage_name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">${{ number_format($deal->value, 2) }}</td>
                                <td class="text-center">
                                    <div class="progress" style="width: 60px; height: 8px;">
                                        <div class="progress-bar" style="width: {{ $deal->probability }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $deal->probability }}%</small>
                                </td>
                                <td>
                                    @if($deal->expected_close_date)
                                    {{ \Carbon\Carbon::parse($deal->expected_close_date)->format('M d, Y') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.deals.detail', $deal->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-briefcase fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No deals found</p>
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
                    <div class="text-muted small" id="dealsInfo">Showing 0 entries</div>
                    <nav aria-label="Deals pagination">
                        <ul class="pagination pagination-sm mb-0" id="dealsPagination">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Deal Modal -->
<div class="modal fade" id="dealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">New Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="dealForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dealTitle" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="dealValue" name="value" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stage <span class="text-danger">*</span></label>
                            <select class="form-select" id="dealStage" name="stage_id" required>
                                @foreach($stages as $stage)
                                @php $stage = (object) $stage; @endphp
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Close Date</label>
                            <input type="date" class="form-control" id="dealCloseDate" name="expected_close_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Probability (%)</label>
                            <input type="number" class="form-control" id="dealProbability" name="probability" min="0" max="100">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="dealNotes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Deal</button>
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

// Sample deals data
var sampleDeals = [
    { id: 1, title: 'Enterprise Software License', deal_number: 'DEAL-2024-001', lead_company: 'Acme Corporation', stage_name: 'Proposal', stage_color: '#17a2b8', value: 45000, probability: 60, expected_close_date: '2024-12-15' },
    { id: 2, title: 'Annual Support Contract', deal_number: 'DEAL-2024-002', lead_company: 'TechStart Inc', stage_name: 'Negotiation', stage_color: '#ffc107', value: 28500, probability: 75, expected_close_date: '2024-12-20' },
    { id: 3, title: 'Custom Development Project', deal_number: 'DEAL-2024-003', lead_company: 'Global Retail Co', stage_name: 'Qualification', stage_color: '#6c757d', value: 120000, probability: 30, expected_close_date: '2025-01-10' },
    { id: 4, title: 'Cloud Migration Services', deal_number: 'DEAL-2024-004', lead_company: 'HealthCare Plus', stage_name: 'Proposal', stage_color: '#17a2b8', value: 85000, probability: 55, expected_close_date: '2024-12-28' },
    { id: 5, title: 'Hardware Upgrade Package', deal_number: 'DEAL-2024-005', lead_company: 'Manufacturing Ltd', stage_name: 'Closed Won', stage_color: '#28a745', value: 32000, probability: 100, expected_close_date: '2024-11-30' },
    { id: 6, title: 'Security Audit Services', deal_number: 'DEAL-2024-006', lead_company: 'Finance Corp', stage_name: 'Discovery', stage_color: '#007bff', value: 15000, probability: 20, expected_close_date: '2025-01-15' },
    { id: 7, title: 'Training Program Implementation', deal_number: 'DEAL-2024-007', lead_company: 'Education Group', stage_name: 'Negotiation', stage_color: '#ffc107', value: 22000, probability: 80, expected_close_date: '2024-12-18' },
    { id: 8, title: 'Data Analytics Platform', deal_number: 'DEAL-2024-008', lead_company: 'Research Institute', stage_name: 'Qualification', stage_color: '#6c757d', value: 95000, probability: 40, expected_close_date: '2025-02-01' }
];

// Pagination variables
var dealsPerPage = 5;
var currentDealsPage = 1;
var serverDeals = @json($deals['data'] ?? []);
var allDealsData = serverDeals.length > 0 ? serverDeals : sampleDeals;

function renderDealsTable() {
    var tbody = document.getElementById('dealsTableBody');
    var start = (currentDealsPage - 1) * dealsPerPage;
    var end = start + dealsPerPage;
    var pageData = allDealsData.slice(start, end);

    var html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="7" class="text-center py-5"><div class="text-muted"><i class="bi bi-briefcase fs-1 d-block mb-2"></i><p class="mb-0">No deals found</p></div></td></tr>';
    } else {
        pageData.forEach(function(deal) {
            var closeDate = deal.expected_close_date ? new Date(deal.expected_close_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-';
            html += '<tr onclick="highlightRow(event)" style="cursor: pointer;">';
            html += '<td class="ps-4"><a href="/admin/deals/' + deal.id + '" class="text-decoration-none"><div class="fw-semibold">' + deal.title + '</div><small class="text-muted">' + deal.deal_number + '</small></a></td>';
            html += '<td>' + (deal.lead_company || '-') + '</td>';
            html += '<td class="text-center"><span class="badge" style="background-color: ' + (deal.stage_color || '#6c757d') + '">' + (deal.stage_name || 'Unknown') + '</span></td>';
            html += '<td class="text-end fw-bold text-success">$' + Number(deal.value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
            html += '<td class="text-center"><div class="progress" style="width: 60px; height: 8px;"><div class="progress-bar" style="width: ' + deal.probability + '%"></div></div><small class="text-muted">' + deal.probability + '%</small></td>';
            html += '<td>' + closeDate + '</td>';
            html += '<td class="text-end pe-4"><a href="/admin/deals/' + deal.id + '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a></td>';
            html += '</tr>';
        });
    }
    tbody.innerHTML = html;

    // Update info text
    var total = allDealsData.length;
    var showing = Math.min(end, total);
    document.getElementById('dealsInfo').textContent = 'Showing ' + (start + 1) + ' to ' + showing + ' of ' + total + ' entries';

    renderDealsPagination();
}

function renderDealsPagination() {
    var totalPages = Math.ceil(allDealsData.length / dealsPerPage);
    var pagination = document.getElementById('dealsPagination');
    var html = '';

    // Previous button
    html += '<li class="page-item ' + (currentDealsPage === 1 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToDealsPage(' + (currentDealsPage - 1) + '); return false;">Previous</a></li>';

    // Page numbers
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === currentDealsPage ? 'active' : '') + '">';
        html += '<a class="page-link" href="#" onclick="goToDealsPage(' + i + '); return false;">' + i + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (currentDealsPage === totalPages ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToDealsPage(' + (currentDealsPage + 1) + '); return false;">Next</a></li>';

    pagination.innerHTML = html;
}

function goToDealsPage(page) {
    var totalPages = Math.ceil(allDealsData.length / dealsPerPage);
    if (page < 1 || page > totalPages) return;
    currentDealsPage = page;
    renderDealsTable();
}

// Initialize table on page load
document.addEventListener('DOMContentLoaded', function() {
    renderDealsTable();
});

document.getElementById('dealForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = {
        title: document.getElementById('dealTitle').value,
        value: document.getElementById('dealValue').value,
        stage_id: document.getElementById('dealStage').value,
        expected_close_date: document.getElementById('dealCloseDate').value || null,
        probability: document.getElementById('dealProbability').value || null,
        notes: document.getElementById('dealNotes').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/deals`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Deal created successfully');
            location.reload();
        } else {
            const error = await response.json();
            alert(error.message || 'Failed to create deal');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
});
</script>
@endpush
