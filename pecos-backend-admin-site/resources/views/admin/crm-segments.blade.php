@extends('layouts.admin')

@section('title', 'Customer Segments')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Customer Segments</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Customer Segments</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#segmentModal">
        <i class="bi bi-plus-lg me-1"></i> Create Segment
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Segment Stats -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-pie-chart-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Segments</h6>
                                <h3 class="mb-0">{{ count($segments) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-lightning-fill text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Dynamic Segments</h6>
                                <h3 class="mb-0">{{ collect($segments)->where('is_dynamic', true)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-bookmark-star-fill text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Preset Segments</h6>
                                <h3 class="mb-0">{{ collect($segments)->where('is_preset', true)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-people-fill text-warning fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Segmented Customers</h6>
                                <h3 class="mb-0">{{ collect($segments)->sum('customer_count') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preset Segments -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-bookmark-star text-primary me-2"></i>
                    <h5 class="mb-0">Preset Segments</h5>
                    <span class="badge bg-primary ms-2">{{ collect($segments)->where('is_preset', true)->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse(collect($segments)->where('is_preset', true) as $segment)
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 border segment-card" data-segment-id="{{ $segment['id'] ?? '' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $segment['name'] ?? 'Unknown' }}</h6>
                                    <span class="badge bg-primary-subtle text-primary">Preset</span>
                                </div>
                                <p class="card-text text-muted small mb-3">{{ $segment['description'] ?? 'No description' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-people text-muted me-1"></i>
                                        <span class="fw-semibold">{{ number_format($segment['customer_count'] ?? 0) }}</span>
                                        <span class="text-muted small">customers</span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="refreshSegment({{ $segment['id'] ?? 0 }})" title="Refresh">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="viewSegmentCustomers({{ $segment['id'] ?? 0 }})" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-bookmark-star fs-1 d-block mb-2"></i>
                            <p>No preset segments available</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Segments -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-sliders text-success me-2"></i>
                        <h5 class="mb-0">Custom Segments</h5>
                        <span class="badge bg-success ms-2">{{ collect($segments)->where('is_preset', false)->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Segment Name</th>
                                <th>Description</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Customers</th>
                                <th class="text-center">Last Updated</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="segmentsTableBody">
                            @forelse(collect($segments)->where('is_preset', false) as $segment)
                            <tr data-segment-id="{{ $segment['id'] ?? '' }}" onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-pie-chart text-success"></i>
                                        </div>
                                        <span class="fw-semibold">{{ $segment['name'] ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($segment['description'] ?? 'No description', 50) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($segment['is_dynamic'] ?? false)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-lightning-fill me-1"></i>Dynamic
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-list-check me-1"></i>Static
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ number_format($segment['customer_count'] ?? 0) }}</span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ isset($segment['updated_at']) ? \Carbon\Carbon::parse($segment['updated_at'])->diffForHumans() : '-' }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editSegment({{ json_encode($segment) }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-success" onclick="refreshSegment({{ $segment['id'] ?? 0 }})" title="Refresh">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="viewSegmentCustomers({{ $segment['id'] ?? 0 }})" title="View Customers">
                                            <i class="bi bi-people"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="exportSegment({{ $segment['id'] ?? 0 }})" title="Export">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteSegment({{ $segment['id'] ?? 0 }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-pie-chart fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No custom segments created yet</p>
                                        <small>Create a segment to group customers based on specific criteria</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination -->
            <div class="card-footer bg-white border-0" id="segmentsPaginationContainer">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="segmentsPagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Segment Modal -->
<div class="modal fade" id="segmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="segmentModalTitle">Create Segment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="segmentForm">
                <div class="modal-body">
                    <input type="hidden" id="segmentId" name="id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Segment Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="segmentName" name="name" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segment Type</label>
                            <select class="form-select" id="segmentType" name="is_dynamic">
                                <option value="1">Dynamic (Auto-update)</option>
                                <option value="0">Static (Manual)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="segmentDescription" name="description" rows="2" maxlength="500"></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">
                        <i class="bi bi-funnel me-1"></i> Segment Rules
                        <small class="text-muted fw-normal">(Customers matching ALL rules will be included)</small>
                    </h6>

                    <div id="rulesContainer">
                        <!-- Rules will be added here dynamically -->
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mt-3" onclick="addRule()">
                        <i class="bi bi-plus-lg me-1"></i> Add Rule
                    </button>
                </div>
                <div class="modal-footer border-0">
                    <div class="me-auto">
                        <span class="text-muted small" id="estimatedCount">
                            <i class="bi bi-people me-1"></i> Estimated: <span id="countValue">-</span> customers
                        </span>
                    </div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="segmentSubmitBtn">Create Segment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.segment-card {
    transition: all 0.2s ease;
}
.segment-card:hover {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
}
.rule-row {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
}
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
    var selectedRows = document.querySelectorAll('#segmentsTableBody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    // Add selection to clicked row
    row.classList.add('row-selected');
}

// Sample data for Custom Segments (used when no data from server)
var sampleSegments = [
    { id: 1, name: 'High Value Customers', description: 'Customers who spent more than $1000', is_dynamic: true, is_preset: false, customer_count: 245, updated_at: '2024-12-01T10:30:00Z', rules: [{ field: 'total_spent', operator: '>', value: 1000 }] },
    { id: 2, name: 'Recent Purchasers', description: 'Customers who ordered in the last 30 days', is_dynamic: true, is_preset: false, customer_count: 128, updated_at: '2024-12-02T08:15:00Z', rules: [{ field: 'last_order_days', operator: '<=', value: 30 }] },
    { id: 3, name: 'Inactive Customers', description: 'Customers with no orders in 90 days', is_dynamic: true, is_preset: false, customer_count: 523, updated_at: '2024-11-28T14:45:00Z', rules: [{ field: 'last_order_days', operator: '>', value: 90 }] },
    { id: 4, name: 'VIP Tier Members', description: 'Gold and platinum loyalty members', is_dynamic: false, is_preset: false, customer_count: 87, updated_at: '2024-11-25T09:00:00Z', rules: [{ field: 'loyalty_tier', operator: '=', value: 'gold' }] },
    { id: 5, name: 'Repeat Buyers', description: 'Customers with 3+ orders', is_dynamic: true, is_preset: false, customer_count: 342, updated_at: '2024-11-30T16:20:00Z', rules: [{ field: 'order_count', operator: '>=', value: 3 }] },
    { id: 6, name: 'Big Spenders', description: 'Average order value over $200', is_dynamic: true, is_preset: false, customer_count: 156, updated_at: '2024-12-01T11:00:00Z', rules: [{ field: 'avg_order_value', operator: '>', value: 200 }] },
    { id: 7, name: 'New Sign-ups', description: 'Accounts created in last 14 days', is_dynamic: true, is_preset: false, customer_count: 67, updated_at: '2024-12-02T09:30:00Z', rules: [{ field: 'created_at', operator: 'within_days', value: 14 }] },
    { id: 8, name: 'Email Engaged', description: 'Opened email in last 7 days', is_dynamic: true, is_preset: false, customer_count: 412, updated_at: '2024-12-01T15:00:00Z', rules: [{ field: 'email_opened_days', operator: '<=', value: 7 }] }
];

// Pagination variables
var segmentsCurrentPage = 1;
var segmentsPerPage = 5;
var serverSegments = @json(collect($segments)->where('is_preset', false)->values());
var allSegmentsData = serverSegments.length > 0 ? serverSegments : sampleSegments;

// Initialize pagination on page load
document.addEventListener('DOMContentLoaded', function() {
    renderSegmentsTable();
});

function renderSegmentsTable() {
    var tbody = document.getElementById('segmentsTableBody');
    var startIndex = (segmentsCurrentPage - 1) * segmentsPerPage;
    var endIndex = startIndex + segmentsPerPage;
    var pageData = allSegmentsData.slice(startIndex, endIndex);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><div class="text-muted"><i class="bi bi-pie-chart fs-1 d-block mb-2"></i><p class="mb-0">No custom segments created yet</p><small>Create a segment to group customers based on specific criteria</small></div></td></tr>';
        document.getElementById('segmentsPaginationContainer').style.display = 'none';
        return;
    }

    tbody.innerHTML = pageData.map(function(segment) {
        var typeHtml = segment.is_dynamic
            ? '<span class="badge bg-success-subtle text-success"><i class="bi bi-lightning-fill me-1"></i>Dynamic</span>'
            : '<span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-list-check me-1"></i>Static</span>';

        var updatedText = segment.updated_at ? getRelativeTime(segment.updated_at) : '-';
        var description = segment.description || 'No description';
        if (description.length > 50) description = description.substring(0, 50) + '...';

        return '<tr data-segment-id="' + (segment.id || '') + '" onclick="highlightRow(event)" style="cursor: pointer;">' +
            '<td class="ps-4">' +
                '<div class="d-flex align-items-center">' +
                    '<div class="bg-success bg-opacity-10 rounded-circle p-2 me-2"><i class="bi bi-pie-chart text-success"></i></div>' +
                    '<span class="fw-semibold">' + (segment.name || 'Unknown') + '</span>' +
                '</div>' +
            '</td>' +
            '<td><span class="text-muted">' + description + '</span></td>' +
            '<td class="text-center">' + typeHtml + '</td>' +
            '<td class="text-center"><span class="fw-semibold">' + (segment.customer_count || 0).toLocaleString() + '</span></td>' +
            '<td class="text-center text-muted">' + updatedText + '</td>' +
            '<td class="text-end pe-4">' +
                '<div class="btn-group btn-group-sm">' +
                    '<button class="btn btn-outline-primary" onclick="editSegment(' + JSON.stringify(segment).replace(/"/g, '&quot;') + ')" title="Edit"><i class="bi bi-pencil"></i></button>' +
                    '<button class="btn btn-outline-success" onclick="refreshSegment(' + (segment.id || 0) + ')" title="Refresh"><i class="bi bi-arrow-clockwise"></i></button>' +
                    '<button class="btn btn-outline-info" onclick="viewSegmentCustomers(' + (segment.id || 0) + ')" title="View Customers"><i class="bi bi-people"></i></button>' +
                    '<button class="btn btn-outline-warning" onclick="exportSegment(' + (segment.id || 0) + ')" title="Export"><i class="bi bi-download"></i></button>' +
                    '<button class="btn btn-outline-danger" onclick="deleteSegment(' + (segment.id || 0) + ')" title="Delete"><i class="bi bi-trash"></i></button>' +
                '</div>' +
            '</td></tr>';
    }).join('');

    renderSegmentsPagination();
}

function getRelativeTime(dateStr) {
    var date = new Date(dateStr);
    var now = new Date();
    var diffMs = now - date;
    var diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    var diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    var diffMins = Math.floor(diffMs / (1000 * 60));

    if (diffMins < 60) return diffMins + ' min ago';
    if (diffHours < 24) return diffHours + ' hours ago';
    if (diffDays === 1) return 'yesterday';
    if (diffDays < 7) return diffDays + ' days ago';
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function renderSegmentsPagination() {
    var totalPages = Math.ceil(allSegmentsData.length / segmentsPerPage);
    var paginationEl = document.getElementById('segmentsPagination');
    var container = document.getElementById('segmentsPaginationContainer');

    if (totalPages <= 1) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    var html = '';

    // Previous button
    html += '<li class="page-item ' + (segmentsCurrentPage === 1 ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="goToSegmentsPage(' + (segmentsCurrentPage - 1) + '); return false;">&laquo;</a></li>';

    // Page numbers
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === segmentsCurrentPage ? 'active' : '') + '">' +
                '<a class="page-link" href="#" onclick="goToSegmentsPage(' + i + '); return false;">' + i + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (segmentsCurrentPage === totalPages ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="goToSegmentsPage(' + (segmentsCurrentPage + 1) + '); return false;">&raquo;</a></li>';

    paginationEl.innerHTML = html;
}

function goToSegmentsPage(page) {
    var totalPages = Math.ceil(allSegmentsData.length / segmentsPerPage);
    if (page < 1 || page > totalPages) return;
    segmentsCurrentPage = page;
    renderSegmentsTable();
}

const RULE_FIELDS = [
    { value: 'total_spent', label: 'Total Spent ($)', type: 'number' },
    { value: 'order_count', label: 'Order Count', type: 'number' },
    { value: 'avg_order_value', label: 'Average Order Value ($)', type: 'number' },
    { value: 'last_order_days', label: 'Days Since Last Order', type: 'number' },
    { value: 'created_at', label: 'Account Created', type: 'date' },
    { value: 'loyalty_tier', label: 'Loyalty Tier', type: 'select', options: ['bronze', 'silver', 'gold', 'platinum'] },
    { value: 'email_opened_days', label: 'Days Since Email Opened', type: 'number' }
];

const OPERATORS = {
    number: [
        { value: '=', label: 'Equals' },
        { value: '!=', label: 'Not Equals' },
        { value: '>', label: 'Greater Than' },
        { value: '>=', label: 'Greater Than or Equal' },
        { value: '<', label: 'Less Than' },
        { value: '<=', label: 'Less Than or Equal' }
    ],
    date: [
        { value: 'within_days', label: 'Within Last X Days' },
        { value: 'before_days', label: 'More Than X Days Ago' }
    ],
    select: [
        { value: '=', label: 'Is' },
        { value: '!=', label: 'Is Not' }
    ]
};

let ruleIndex = 0;

function addRule(rule = null) {
    const container = document.getElementById('rulesContainer');
    const idx = ruleIndex++;

    const fieldOptions = RULE_FIELDS.map(f =>
        `<option value="${f.value}" ${rule && rule.field === f.value ? 'selected' : ''}>${f.label}</option>`
    ).join('');

    const html = `
        <div class="rule-row" id="rule-${idx}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Field</label>
                    <select class="form-select form-select-sm" name="rules[${idx}][field]" onchange="updateOperators(${idx}, this.value)" required>
                        <option value="">Select field...</option>
                        ${fieldOptions}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Operator</label>
                    <select class="form-select form-select-sm" name="rules[${idx}][operator]" id="operator-${idx}" required>
                        <option value="">Select operator...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Value</label>
                    <input type="text" class="form-control form-control-sm" name="rules[${idx}][value]" id="value-${idx}"
                           value="${rule ? rule.value : ''}" required>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRule(${idx})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    if (rule) {
        updateOperators(idx, rule.field);
        setTimeout(() => {
            document.querySelector(`[name="rules[${idx}][operator]"]`).value = rule.operator;
        }, 0);
    }
}

function updateOperators(idx, fieldValue) {
    const field = RULE_FIELDS.find(f => f.value === fieldValue);
    const operatorSelect = document.getElementById(`operator-${idx}`);
    const valueInput = document.getElementById(`value-${idx}`);

    if (!field) {
        operatorSelect.innerHTML = '<option value="">Select operator...</option>';
        return;
    }

    const operators = OPERATORS[field.type] || OPERATORS.number;
    operatorSelect.innerHTML = '<option value="">Select operator...</option>' +
        operators.map(o => `<option value="${o.value}">${o.label}</option>`).join('');

    // Update value input type
    if (field.type === 'select') {
        valueInput.outerHTML = `
            <select class="form-select form-select-sm" name="rules[${idx}][value]" id="value-${idx}" required>
                <option value="">Select...</option>
                ${field.options.map(o => `<option value="${o}">${o.charAt(0).toUpperCase() + o.slice(1)}</option>`).join('')}
            </select>
        `;
    } else if (field.type === 'number') {
        valueInput.type = 'number';
        valueInput.placeholder = 'Enter number...';
    } else {
        valueInput.type = 'number';
        valueInput.placeholder = 'Number of days...';
    }
}

function removeRule(idx) {
    const rule = document.getElementById(`rule-${idx}`);
    if (rule) rule.remove();
}

function getRulesFromForm() {
    const rules = [];
    const container = document.getElementById('rulesContainer');
    container.querySelectorAll('.rule-row').forEach(row => {
        const field = row.querySelector('[name*="[field]"]').value;
        const operator = row.querySelector('[name*="[operator]"]').value;
        const value = row.querySelector('[name*="[value]"]').value;
        if (field && operator && value) {
            rules.push({ field, operator, value });
        }
    });
    return rules;
}

// Edit segment
function editSegment(segment) {
    document.getElementById('segmentModalTitle').textContent = 'Edit Segment';
    document.getElementById('segmentSubmitBtn').textContent = 'Update Segment';
    document.getElementById('segmentId').value = segment.id;
    document.getElementById('segmentName').value = segment.name;
    document.getElementById('segmentDescription').value = segment.description || '';
    document.getElementById('segmentType').value = segment.is_dynamic ? '1' : '0';

    // Clear and add rules
    document.getElementById('rulesContainer').innerHTML = '';
    ruleIndex = 0;

    const rules = typeof segment.rules === 'string' ? JSON.parse(segment.rules) : (segment.rules || []);
    if (rules.length === 0) {
        addRule();
    } else {
        rules.forEach(rule => addRule(rule));
    }

    new bootstrap.Modal(document.getElementById('segmentModal')).show();
}

// Reset modal
document.getElementById('segmentModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('segmentModalTitle').textContent = 'Create Segment';
    document.getElementById('segmentSubmitBtn').textContent = 'Create Segment';
    document.getElementById('segmentForm').reset();
    document.getElementById('segmentId').value = '';
    document.getElementById('rulesContainer').innerHTML = '';
    ruleIndex = 0;
});

document.getElementById('segmentModal').addEventListener('shown.bs.modal', function() {
    if (document.getElementById('rulesContainer').children.length === 0) {
        addRule();
    }
});

// Form submission
document.getElementById('segmentForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const segmentId = document.getElementById('segmentId').value;
    const isEdit = !!segmentId;

    const data = {
        name: document.getElementById('segmentName').value,
        description: document.getElementById('segmentDescription').value,
        is_dynamic: document.getElementById('segmentType').value === '1',
        rules: getRulesFromForm()
    };

    try {
        const url = isEdit
            ? `${API_BASE}/admin/crm/segments/${segmentId}`
            : `${API_BASE}/admin/crm/segments`;

        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            showToast(isEdit ? 'Segment updated successfully' : 'Segment created successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            const error = await response.json();
            showToast(error.message || 'Failed to save segment', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
});

// Refresh segment
async function refreshSegment(id) {
    try {
        showToast('Refreshing segment...', 'info');
        const response = await fetch(`${API_BASE}/admin/crm/segments/${id}/recalculate`, {
            method: 'POST',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            showToast('Segment refreshed successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to refresh segment', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// Delete segment
async function deleteSegment(id) {
    if (!confirm('Are you sure you want to delete this segment?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/crm/segments/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            showToast('Segment deleted successfully', 'success');
            document.querySelector(`tr[data-segment-id="${id}"]`)?.remove();
        } else {
            showToast('Failed to delete segment', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// View segment customers
function viewSegmentCustomers(segmentId) {
    window.location.href = `{{ route('admin.customers') }}?segment=${segmentId}`;
}

// Export segment
async function exportSegment(id) {
    window.open(`${API_BASE}/admin/crm/segments/${id}/export`, '_blank');
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>
@endpush
