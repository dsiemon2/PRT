@extends('layouts.admin')

@section('title', 'Leads')

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
        <h1 class="h3 mb-1">Leads</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leads') }}">Sales Pipeline</a></li>
                <li class="breadcrumb-item active">Leads</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leadModal" title="Create a new lead">
        <i class="bi bi-plus-lg me-1"></i> New Lead
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        <small class="text-muted">Total Leads</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="bi bi-star-fill fs-4"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['new'] ?? 0 }}</h3>
                        <small class="text-muted">New</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['qualified'] ?? 0 }}</h3>
                        <small class="text-muted">Qualified</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-danger mb-2">
                            <i class="bi bi-fire fs-4"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['hot_leads'] ?? 0 }}</h3>
                        <small class="text-muted">Hot Leads</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <h3 class="mb-0">${{ number_format($stats['total_value'] ?? 0) }}</h3>
                        <small class="text-muted">Pipeline Value</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2">
                            <i class="bi bi-graph-up fs-4"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['avg_score'] ?? 0 }}</h3>
                        <small class="text-muted">Avg Score</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.leads') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="new" {{ ($filters['status'] ?? '') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ ($filters['status'] ?? '') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="qualified" {{ ($filters['status'] ?? '') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                            <option value="proposal" {{ ($filters['status'] ?? '') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                            <option value="negotiation" {{ ($filters['status'] ?? '') == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                            <option value="won" {{ ($filters['status'] ?? '') == 'won' ? 'selected' : '' }}>Won</option>
                            <option value="lost" {{ ($filters['status'] ?? '') == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="">All Priorities</option>
                            <option value="hot" {{ ($filters['priority'] ?? '') == 'hot' ? 'selected' : '' }}>Hot</option>
                            <option value="high" {{ ($filters['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ ($filters['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ ($filters['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Source</label>
                        <select name="source_id" class="form-select">
                            <option value="">All Sources</option>
                            @foreach($sources as $source)
                            <option value="{{ $source->id ?? $source['id'] ?? '' }}" {{ ($filters['source_id'] ?? '') == ($source->id ?? $source['id'] ?? '') ? 'selected' : '' }}>
                                {{ $source->name ?? $source['name'] ?? '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, email, company..." value="{{ $filters['search'] ?? '' }}">
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

    <!-- Leads Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Lead</th>
                                <th>Company</th>
                                <th>Source</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Priority</th>
                                <th class="text-end">Value</th>
                                <th class="text-center">Score</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody">
                            @forelse($leads['data'] ?? [] as $lead)
                            @php $lead = (object) $lead; @endphp
                            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <a href="{{ route('admin.leads.detail', $lead->id) }}" class="text-decoration-none">
                                        <div class="fw-semibold">{{ $lead->first_name }} {{ $lead->last_name }}</div>
                                        <small class="text-muted">{{ $lead->email }}</small>
                                    </a>
                                </td>
                                <td>{{ $lead->company ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $lead->source_name ?? 'Unknown' }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'new' => 'success',
                                            'contacted' => 'info',
                                            'qualified' => 'primary',
                                            'proposal' => 'warning',
                                            'negotiation' => 'warning',
                                            'won' => 'success',
                                            'lost' => 'danger',
                                            'dormant' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$lead->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $priorityColors = ['hot' => 'danger', 'high' => 'warning', 'medium' => 'info', 'low' => 'secondary'];
                                    @endphp
                                    <span class="badge bg-{{ $priorityColors[$lead->priority] ?? 'secondary' }}">
                                        {{ ucfirst($lead->priority) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($lead->estimated_value)
                                    ${{ number_format($lead->estimated_value, 2) }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="width: 60px; height: 8px;" title="{{ $lead->lead_score ?? 0 }}/100">
                                        <div class="progress-bar bg-{{ ($lead->lead_score ?? 0) >= 70 ? 'success' : (($lead->lead_score ?? 0) >= 40 ? 'warning' : 'danger') }}"
                                             style="width: {{ $lead->lead_score ?? 0 }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.leads.detail', $lead->id) }}" class="btn btn-sm btn-outline-primary" title="View lead details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No leads found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination -->
            <div class="card-footer bg-white border-top py-3" id="leadsPaginationContainer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small" id="leadsInfo">Showing 0 entries</div>
                    <nav aria-label="Leads pagination">
                        <ul class="pagination pagination-sm mb-0" id="leadsPagination">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Lead Modal -->
<div class="modal fade" id="leadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="leadForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input type="text" class="form-control" id="company" name="company">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="jobTitle" name="job_title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Source</label>
                            <select class="form-select" id="sourceId" name="source_id">
                                <option value="">Select source</option>
                                @foreach($sources as $source)
                                <option value="{{ $source->id ?? $source['id'] ?? '' }}">{{ $source->name ?? $source['name'] ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="hot">Hot</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimated Value</label>
                            <input type="number" class="form-control" id="estimatedValue" name="estimated_value" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Lead</button>
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
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.closest('button') || target.closest('a') || target.closest('select') ||
        target.closest('.btn-group')) {
        return;
    }
    var selectedRows = document.querySelectorAll('#leadsTableBody tr.row-selected');
    selectedRows.forEach(function(r) { r.classList.remove('row-selected'); });
    row.classList.add('row-selected');
}

// Sample leads data
var sampleLeads = [
    { id: 1, first_name: 'Robert', last_name: 'Johnson', email: 'robert.j@company.com', company: 'Tech Solutions Inc', source_name: 'Website', status: 'new', priority: 'hot', estimated_value: 15000, lead_score: 85 },
    { id: 2, first_name: 'Jennifer', last_name: 'Williams', email: 'j.williams@enterprise.com', company: 'Enterprise Corp', source_name: 'Referral', status: 'contacted', priority: 'high', estimated_value: 25000, lead_score: 72 },
    { id: 3, first_name: 'Michael', last_name: 'Brown', email: 'mbrown@startup.io', company: 'Startup Labs', source_name: 'LinkedIn', status: 'qualified', priority: 'medium', estimated_value: 8000, lead_score: 65 },
    { id: 4, first_name: 'Sarah', last_name: 'Davis', email: 'sarah.d@retail.com', company: 'Retail Plus', source_name: 'Trade Show', status: 'proposal', priority: 'high', estimated_value: 45000, lead_score: 78 },
    { id: 5, first_name: 'David', last_name: 'Miller', email: 'dmiller@consulting.com', company: 'Miller Consulting', source_name: 'Cold Call', status: 'negotiation', priority: 'hot', estimated_value: 32000, lead_score: 88 },
    { id: 6, first_name: 'Emily', last_name: 'Wilson', email: 'ewilson@design.co', company: 'Creative Design Co', source_name: 'Website', status: 'new', priority: 'medium', estimated_value: 12000, lead_score: 55 },
    { id: 7, first_name: 'James', last_name: 'Taylor', email: 'jtaylor@finance.net', company: 'Finance Group', source_name: 'Email Campaign', status: 'contacted', priority: 'low', estimated_value: 5000, lead_score: 35 },
    { id: 8, first_name: 'Amanda', last_name: 'Anderson', email: 'amanda.a@health.org', company: 'Health Services', source_name: 'Referral', status: 'won', priority: 'high', estimated_value: 28000, lead_score: 92 }
];

// Pagination
var leadsCurrentPage = 1;
var leadsPerPage = 5;
var serverLeads = @json($leads['data'] ?? []);
var allLeadsData = serverLeads.length > 0 ? serverLeads : sampleLeads;

document.addEventListener('DOMContentLoaded', function() { renderLeadsTable(); });

function renderLeadsTable() {
    var tbody = document.getElementById('leadsTableBody');
    var startIndex = (leadsCurrentPage - 1) * leadsPerPage;
    var pageData = allLeadsData.slice(startIndex, startIndex + leadsPerPage);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="text-muted"><i class="bi bi-people fs-1 d-block mb-2"></i><p class="mb-0">No leads found</p></div></td></tr>';
        document.getElementById('leadsInfo').textContent = 'Showing 0 entries';
        document.getElementById('leadsPagination').innerHTML = '';
        return;
    }

    var statusColors = { new: 'success', contacted: 'info', qualified: 'primary', proposal: 'warning', negotiation: 'warning', won: 'success', lost: 'danger', dormant: 'secondary' };
    var priorityColors = { hot: 'danger', high: 'warning', medium: 'info', low: 'secondary' };

    tbody.innerHTML = pageData.map(function(lead) {
        var scoreColor = lead.lead_score >= 70 ? 'success' : (lead.lead_score >= 40 ? 'warning' : 'danger');
        var valueText = lead.estimated_value ? '$' + lead.estimated_value.toLocaleString() : '<span class="text-muted">-</span>';
        return '<tr onclick="highlightRow(event)" style="cursor: pointer;">' +
            '<td class="ps-4"><a href="/admin/leads/' + lead.id + '" class="text-decoration-none"><div class="fw-semibold">' + lead.first_name + ' ' + lead.last_name + '</div><small class="text-muted">' + lead.email + '</small></a></td>' +
            '<td>' + (lead.company || '-') + '</td>' +
            '<td><span class="badge bg-secondary-subtle text-secondary">' + (lead.source_name || 'Unknown') + '</span></td>' +
            '<td class="text-center"><span class="badge bg-' + (statusColors[lead.status] || 'secondary') + '-subtle text-' + (statusColors[lead.status] || 'secondary') + '">' + lead.status.charAt(0).toUpperCase() + lead.status.slice(1) + '</span></td>' +
            '<td class="text-center"><span class="badge bg-' + (priorityColors[lead.priority] || 'secondary') + '">' + lead.priority.charAt(0).toUpperCase() + lead.priority.slice(1) + '</span></td>' +
            '<td class="text-end">' + valueText + '</td>' +
            '<td class="text-center"><div class="progress" style="width: 60px; height: 8px;" title="' + lead.lead_score + '/100"><div class="progress-bar bg-' + scoreColor + '" style="width: ' + lead.lead_score + '%"></div></div></td>' +
            '<td class="text-end pe-4"><a href="/admin/leads/' + lead.id + '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a></td></tr>';
    }).join('');
    renderLeadsPagination();
}

function renderLeadsPagination() {
    var totalPages = Math.ceil(allLeadsData.length / leadsPerPage);
    var total = allLeadsData.length;
    var startIndex = (leadsCurrentPage - 1) * leadsPerPage;
    var endIndex = Math.min(startIndex + leadsPerPage, total);

    // Update info text
    document.getElementById('leadsInfo').textContent = 'Showing ' + (startIndex + 1) + ' to ' + endIndex + ' of ' + total + ' entries';

    var paginationEl = document.getElementById('leadsPagination');
    var container = document.getElementById('leadsPaginationContainer');
    container.style.display = 'block';

    var html = '<li class="page-item ' + (leadsCurrentPage === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" onclick="goToLeadsPage(' + (leadsCurrentPage - 1) + '); return false;">Previous</a></li>';
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === leadsCurrentPage ? 'active' : '') + '"><a class="page-link" href="#" onclick="goToLeadsPage(' + i + '); return false;">' + i + '</a></li>';
    }
    html += '<li class="page-item ' + (leadsCurrentPage === totalPages ? 'disabled' : '') + '"><a class="page-link" href="#" onclick="goToLeadsPage(' + (leadsCurrentPage + 1) + '); return false;">Next</a></li>';
    paginationEl.innerHTML = html;
}

function goToLeadsPage(page) {
    var totalPages = Math.ceil(allLeadsData.length / leadsPerPage);
    if (page < 1 || page > totalPages) return;
    leadsCurrentPage = page;
    renderLeadsTable();
}

document.getElementById('leadForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = {
        first_name: document.getElementById('firstName').value,
        last_name: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value || null,
        company: document.getElementById('company').value || null,
        job_title: document.getElementById('jobTitle').value || null,
        source_id: document.getElementById('sourceId').value || null,
        priority: document.getElementById('priority').value,
        estimated_value: document.getElementById('estimatedValue').value || null,
        notes: document.getElementById('notes').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/leads`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Lead created successfully');
            location.reload();
        } else {
            const error = await response.json();
            alert(error.message || 'Failed to create lead');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
});
</script>
@endpush
