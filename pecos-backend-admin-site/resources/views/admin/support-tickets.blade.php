@extends('layouts.admin')

@section('title', 'Support Tickets')

@push('styles')
<style>
/* Row selection styles */
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
    color: #333 !important;
}
.table tbody tr.row-selected td strong,
.table tbody tr.row-selected td .fw-semibold,
.table tbody tr.row-selected td .fw-medium {
    color: #333 !important;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Support Tickets</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Support Tickets</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
        <i class="bi bi-plus-lg me-1"></i> New Ticket
    </button>
</div>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="bi bi-circle-fill fs-5"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['open'] ?? 0 }}</h3>
                        <small class="text-muted">Open</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="bi bi-arrow-repeat fs-5"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['in_progress'] ?? 0 }}</h3>
                        <small class="text-muted">In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['pending_customer'] ?? 0 }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-danger mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['urgent'] ?? 0 }}</h3>
                        <small class="text-muted">Urgent</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-muted mb-2">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <h3 class="mb-0">{{ $stats['avg_response_hours'] ?? 0 }}h</h3>
                        <small class="text-muted">Avg Response</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="bi bi-emoji-smile fs-5"></i>
                        </div>
                        <h3 class="mb-0">{{ number_format($stats['satisfaction_avg'] ?? 0, 1) }}</h3>
                        <small class="text-muted">Satisfaction</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.support') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="open" {{ ($filters['status'] ?? '') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ ($filters['status'] ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="pending_customer" {{ ($filters['status'] ?? '') == 'pending_customer' ? 'selected' : '' }}>Pending Customer</option>
                            <option value="resolved" {{ ($filters['status'] ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ ($filters['status'] ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="">All Priorities</option>
                            <option value="urgent" {{ ($filters['priority'] ?? '') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="high" {{ ($filters['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ ($filters['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ ($filters['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="order" {{ ($filters['category'] ?? '') == 'order' ? 'selected' : '' }}>Order</option>
                            <option value="return" {{ ($filters['category'] ?? '') == 'return' ? 'selected' : '' }}>Return</option>
                            <option value="product" {{ ($filters['category'] ?? '') == 'product' ? 'selected' : '' }}>Product</option>
                            <option value="shipping" {{ ($filters['category'] ?? '') == 'shipping' ? 'selected' : '' }}>Shipping</option>
                            <option value="billing" {{ ($filters['category'] ?? '') == 'billing' ? 'selected' : '' }}>Billing</option>
                            <option value="other" {{ ($filters['category'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Ticket #, subject, customer..." value="{{ $filters['search'] ?? '' }}">
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

    <!-- Tickets Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Ticket</th>
                                <th>Customer</th>
                                <th>Subject</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Priority</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Messages</th>
                                <th class="text-center">Created</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ticketsTableBody">
                            @forelse($tickets['data'] ?? [] as $ticket)
                            @php $ticket = (object) $ticket; @endphp
                            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <a href="{{ route('admin.support.detail', $ticket->id) }}" class="text-decoration-none fw-semibold">
                                        {{ $ticket->ticket_number }}
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-medium">{{ $ticket->customer_first_name }} {{ $ticket->customer_last_name }}</span>
                                    </div>
                                    <small class="text-muted">{{ $ticket->customer_email }}</small>
                                </td>
                                <td>
                                    <span title="{{ $ticket->subject }}">{{ Str::limit($ticket->subject, 40) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($ticket->category) }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $priorityColors = [
                                            'urgent' => 'danger',
                                            'high' => 'warning',
                                            'medium' => 'info',
                                            'low' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'open' => 'warning',
                                            'in_progress' => 'info',
                                            'pending_customer' => 'secondary',
                                            'resolved' => 'success',
                                            'closed' => 'dark'
                                        ];
                                        $statusLabels = [
                                            'open' => 'Open',
                                            'in_progress' => 'In Progress',
                                            'pending_customer' => 'Pending',
                                            'resolved' => 'Resolved',
                                            'closed' => 'Closed'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$ticket->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $ticket->message_count ?? 0 }}</span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.support.detail', $ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No support tickets found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Client-side Pagination -->
            <div class="card-footer bg-white border-0" id="ticketsPaginationContainer">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="ticketsPagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1" aria-labelledby="newTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newTicketModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Create Support Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newTicketForm">
                <div class="modal-body">
                    <!-- Customer Lookup -->
                    <div class="mb-3">
                        <label class="form-label">Customer Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="customerEmail"
                               placeholder="Enter customer's email address" required>
                        <div id="customerInfo" class="mt-2 d-none">
                            <small class="text-success"><i class="bi bi-check-circle me-1"></i><span id="customerName"></span></small>
                        </div>
                        <small class="text-muted">Start typing to search for existing customers</small>
                    </div>

                    <!-- Related Order (populated after customer is found) -->
                    <div class="mb-3" id="orderSelectContainer" style="display: none;">
                        <label class="form-label">Related Order (Optional)</label>
                        <select class="form-select" id="ticketOrderId">
                            <option value="">Not related to a specific order</option>
                        </select>
                    </div>

                    <!-- Category & Priority -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="ticketCategory" required>
                                <option value="">Select a category...</option>
                                <option value="order">Order Issue</option>
                                <option value="return">Return/Exchange</option>
                                <option value="product">Product Question</option>
                                <option value="shipping">Shipping</option>
                                <option value="billing">Billing</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="ticketPriority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ticketSubject"
                               placeholder="Brief description of the issue" required maxlength="255">
                    </div>

                    <!-- Message -->
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ticketMessage" rows="5"
                                  placeholder="Please describe the issue in detail. Include any relevant order numbers, product names, or other information."
                                  required></textarea>
                    </div>

                    <!-- Internal Note -->
                    <div class="mb-3">
                        <label class="form-label">Internal Note (Optional)</label>
                        <textarea class="form-control" id="ticketInternalNote" rows="2"
                                  placeholder="Internal notes visible only to staff (not shown to customer)"></textarea>
                        <small class="text-muted"><i class="bi bi-eye-slash me-1"></i>This note will not be visible to the customer</small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>This ticket will be created on behalf of the customer. They will receive an email notification.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';
let foundCustomerId = null;
let lookupTimeout = null;

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
    var selectedRows = document.querySelectorAll('#ticketsTableBody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    // Add selection to clicked row
    row.classList.add('row-selected');
}

// Sample tickets data
var sampleTickets = [
    { id: 1, ticket_number: 'TKT-2024-001', customer_first_name: 'John', customer_last_name: 'Smith', customer_email: 'john.smith@email.com', subject: 'Order not received after 2 weeks', category: 'shipping', priority: 'high', status: 'open', message_count: 3, created_at: '2024-12-01T10:30:00Z' },
    { id: 2, ticket_number: 'TKT-2024-002', customer_first_name: 'Sarah', customer_last_name: 'Johnson', customer_email: 'sarah.j@email.com', subject: 'Wrong item delivered', category: 'order', priority: 'urgent', status: 'in_progress', message_count: 5, created_at: '2024-12-01T14:15:00Z' },
    { id: 3, ticket_number: 'TKT-2024-003', customer_first_name: 'Mike', customer_last_name: 'Davis', customer_email: 'mike.d@email.com', subject: 'Request for refund', category: 'refund', priority: 'medium', status: 'pending_customer', message_count: 2, created_at: '2024-11-30T09:00:00Z' },
    { id: 4, ticket_number: 'TKT-2024-004', customer_first_name: 'Emily', customer_last_name: 'Brown', customer_email: 'emily.b@email.com', subject: 'Product quality issue', category: 'product', priority: 'high', status: 'open', message_count: 1, created_at: '2024-11-29T16:45:00Z' },
    { id: 5, ticket_number: 'TKT-2024-005', customer_first_name: 'David', customer_last_name: 'Wilson', customer_email: 'david.w@email.com', subject: 'Cannot apply discount code', category: 'billing', priority: 'low', status: 'resolved', message_count: 4, created_at: '2024-11-28T11:20:00Z' },
    { id: 6, ticket_number: 'TKT-2024-006', customer_first_name: 'Lisa', customer_last_name: 'Taylor', customer_email: 'lisa.t@email.com', subject: 'Tracking number not working', category: 'shipping', priority: 'medium', status: 'in_progress', message_count: 2, created_at: '2024-11-27T13:00:00Z' },
    { id: 7, ticket_number: 'TKT-2024-007', customer_first_name: 'James', customer_last_name: 'Anderson', customer_email: 'james.a@email.com', subject: 'Account login issues', category: 'account', priority: 'low', status: 'closed', message_count: 3, created_at: '2024-11-26T08:30:00Z' },
    { id: 8, ticket_number: 'TKT-2024-008', customer_first_name: 'Amanda', customer_last_name: 'Martinez', customer_email: 'amanda.m@email.com', subject: 'Missing items from order', category: 'order', priority: 'urgent', status: 'open', message_count: 1, created_at: '2024-12-02T07:45:00Z' }
];

// Pagination variables
var ticketsCurrentPage = 1;
var ticketsPerPage = 5;
var serverTickets = @json($tickets['data'] ?? []);
var allTicketsData = serverTickets.length > 0 ? serverTickets : sampleTickets;

// Initialize pagination on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTicketsTable();
});

function renderTicketsTable() {
    var tbody = document.getElementById('ticketsTableBody');
    var startIndex = (ticketsCurrentPage - 1) * ticketsPerPage;
    var endIndex = startIndex + ticketsPerPage;
    var pageData = allTicketsData.slice(startIndex, endIndex);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5"><div class="text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">No support tickets found</p></div></td></tr>';
        document.getElementById('ticketsPaginationContainer').style.display = 'none';
        return;
    }

    var priorityColors = { urgent: 'danger', high: 'warning', medium: 'info', low: 'secondary' };
    var statusColors = { open: 'warning', in_progress: 'info', pending_customer: 'secondary', resolved: 'success', closed: 'dark' };
    var statusLabels = { open: 'Open', in_progress: 'In Progress', pending_customer: 'Pending', resolved: 'Resolved', closed: 'Closed' };

    tbody.innerHTML = pageData.map(function(ticket) {
        var createdText = getRelativeTime(ticket.created_at);
        var subjectText = ticket.subject.length > 40 ? ticket.subject.substring(0, 40) + '...' : ticket.subject;

        return '<tr onclick="highlightRow(event)" style="cursor: pointer;">' +
            '<td class="ps-4"><a href="/admin/support/' + ticket.id + '" class="text-decoration-none fw-semibold">' + ticket.ticket_number + '</a></td>' +
            '<td><div><span class="fw-medium">' + ticket.customer_first_name + ' ' + ticket.customer_last_name + '</span></div><small class="text-muted">' + ticket.customer_email + '</small></td>' +
            '<td><span title="' + ticket.subject + '">' + subjectText + '</span></td>' +
            '<td class="text-center"><span class="badge bg-secondary-subtle text-secondary">' + (ticket.category.charAt(0).toUpperCase() + ticket.category.slice(1)) + '</span></td>' +
            '<td class="text-center"><span class="badge bg-' + (priorityColors[ticket.priority] || 'secondary') + '">' + (ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1)) + '</span></td>' +
            '<td class="text-center"><span class="badge bg-' + (statusColors[ticket.status] || 'secondary') + '-subtle text-' + (statusColors[ticket.status] || 'secondary') + '">' + (statusLabels[ticket.status] || ticket.status) + '</span></td>' +
            '<td class="text-center"><span class="badge bg-light text-dark">' + (ticket.message_count || 0) + '</span></td>' +
            '<td class="text-center text-muted">' + createdText + '</td>' +
            '<td class="text-end pe-4"><a href="/admin/support/' + ticket.id + '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a></td>' +
            '</tr>';
    }).join('');

    renderTicketsPagination();
}

function getRelativeTime(dateStr) {
    var date = new Date(dateStr);
    var now = new Date();
    var diffMs = now - date;
    var diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    var diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    if (diffHours < 24) return diffHours + ' hours ago';
    if (diffDays === 1) return 'yesterday';
    if (diffDays < 7) return diffDays + ' days ago';
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function renderTicketsPagination() {
    var totalPages = Math.ceil(allTicketsData.length / ticketsPerPage);
    var paginationEl = document.getElementById('ticketsPagination');
    var container = document.getElementById('ticketsPaginationContainer');

    if (totalPages <= 1) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    var html = '<li class="page-item ' + (ticketsCurrentPage === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" onclick="goToTicketsPage(' + (ticketsCurrentPage - 1) + '); return false;">&laquo;</a></li>';
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === ticketsCurrentPage ? 'active' : '') + '"><a class="page-link" href="#" onclick="goToTicketsPage(' + i + '); return false;">' + i + '</a></li>';
    }
    html += '<li class="page-item ' + (ticketsCurrentPage === totalPages ? 'disabled' : '') + '"><a class="page-link" href="#" onclick="goToTicketsPage(' + (ticketsCurrentPage + 1) + '); return false;">&raquo;</a></li>';
    paginationEl.innerHTML = html;
}

function goToTicketsPage(page) {
    var totalPages = Math.ceil(allTicketsData.length / ticketsPerPage);
    if (page < 1 || page > totalPages) return;
    ticketsCurrentPage = page;
    renderTicketsTable();
}

// Customer lookup by email
const customerEmailInput = document.getElementById('customerEmail');
const customerInfoDiv = document.getElementById('customerInfo');
const customerNameSpan = document.getElementById('customerName');
const orderSelectContainer = document.getElementById('orderSelectContainer');
const orderSelect = document.getElementById('ticketOrderId');

customerEmailInput.addEventListener('input', function() {
    // Clear previous timeout
    if (lookupTimeout) clearTimeout(lookupTimeout);

    // Reset customer info and orders
    foundCustomerId = null;
    customerInfoDiv.classList.add('d-none');
    customerInfoDiv.classList.remove('text-success', 'text-danger');
    orderSelectContainer.style.display = 'none';
    orderSelect.innerHTML = '<option value="">Not related to a specific order</option>';

    const email = this.value.trim();
    if (!email || !email.includes('@')) return;

    // Debounce the lookup
    lookupTimeout = setTimeout(async () => {
        try {
            const response = await fetch(`${API_BASE}/admin/customers?search=${encodeURIComponent(email)}&per_page=5`);
            const result = await response.json();

            if (result.data && result.data.length > 0) {
                // Find exact email match
                const customer = result.data.find(c => c.email && c.email.toLowerCase() === email.toLowerCase());

                if (customer) {
                    foundCustomerId = customer.id;
                    const customerDisplayName = `${customer.first_name || ''} ${customer.last_name || ''} (ID: ${customer.id})`.trim();
                    customerInfoDiv.classList.remove('d-none', 'text-danger');
                    customerInfoDiv.classList.add('text-success');
                    customerInfoDiv.innerHTML = `<small class="text-success"><i class="bi bi-check-circle me-1"></i>${customerDisplayName}</small>`;

                    // Fetch customer's orders
                    loadCustomerOrders(customer.id);
                } else {
                    customerInfoDiv.classList.remove('d-none', 'text-success');
                    customerInfoDiv.classList.add('text-danger');
                    customerInfoDiv.innerHTML = `<small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No exact match found. Check email address.</small>`;
                }
            } else {
                customerInfoDiv.classList.remove('d-none', 'text-success');
                customerInfoDiv.innerHTML = `<small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Customer not found. They may need to register first.</small>`;
            }
        } catch (error) {
            console.error('Customer lookup error:', error);
            customerInfoDiv.classList.remove('d-none');
            customerInfoDiv.innerHTML = `<small class="text-danger"><i class="bi bi-x-circle me-1"></i>Error looking up customer.</small>`;
        }
    }, 500);
});

// Load customer orders
async function loadCustomerOrders(customerId) {
    try {
        const response = await fetch(`${API_BASE}/users/${customerId}/orders?limit=10`);
        const result = await response.json();

        if (result.data && result.data.length > 0) {
            orderSelect.innerHTML = '<option value="">Not related to a specific order</option>';
            result.data.forEach(order => {
                const orderDate = new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const option = document.createElement('option');
                option.value = order.id;
                option.textContent = `Order #${order.order_number} - ${orderDate} - $${parseFloat(order.total).toFixed(2)}`;
                orderSelect.appendChild(option);
            });
            orderSelectContainer.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading customer orders:', error);
    }
}

// Create ticket form
document.getElementById('newTicketForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!foundCustomerId) {
        showToast('Please enter a valid customer email address. The customer must have an existing account.', 'error');
        customerEmailInput.focus();
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';

    try {
        const ticketData = {
            customer_id: foundCustomerId,
            subject: document.getElementById('ticketSubject').value.trim(),
            category: document.getElementById('ticketCategory').value,
            priority: document.getElementById('ticketPriority').value,
            message: document.getElementById('ticketMessage').value.trim(),
            sender_type: 'staff',
            sender_id: {{ session('admin_user.id', 1) }}
        };

        // Add optional fields
        const orderId = document.getElementById('ticketOrderId').value;
        if (orderId) {
            ticketData.order_id = parseInt(orderId);
        }

        const internalNote = document.getElementById('ticketInternalNote').value.trim();
        if (internalNote) {
            ticketData.internal_note = internalNote;
        }

        const response = await fetch(`${API_BASE}/admin/support/tickets`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(ticketData)
        });

        const result = await response.json();

        if (response.ok && result.data) {
            showToast(`Ticket ${result.data.ticket_number} created successfully!`, 'success');

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('newTicketModal'));
            modal.hide();

            // Reset form
            this.reset();
            foundCustomerId = null;
            customerInfoDiv.classList.add('d-none');
            orderSelectContainer.style.display = 'none';
            orderSelect.innerHTML = '<option value="">Not related to a specific order</option>';

            // Redirect to ticket detail
            setTimeout(() => {
                window.location.href = `/admin/support/${result.data.id}`;
            }, 1000);
        } else {
            const errorMsg = result.message || result.error || 'Failed to create ticket';
            showToast(errorMsg, 'error');
        }
    } catch (error) {
        console.error('Create ticket error:', error);
        showToast('An error occurred while creating the ticket. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Reset form when modal closes
document.getElementById('newTicketModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('newTicketForm').reset();
    foundCustomerId = null;
    customerInfoDiv.classList.add('d-none');
    orderSelectContainer.style.display = 'none';
    orderSelect.innerHTML = '<option value="">Not related to a specific order</option>';
});

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
