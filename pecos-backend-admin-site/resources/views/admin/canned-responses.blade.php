@extends('layouts.admin')

@section('title', 'Canned Responses')

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
        <h1 class="h3 mb-1">Canned Responses</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.support') }}">Support</a></li>
                <li class="breadcrumb-item active">Canned Responses</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#responseModal">
        <i class="bi bi-plus-lg me-1"></i> Add Response
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Canned Responses</h5>
                    <span class="badge bg-primary">{{ count($responses) }} responses</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Shortcut</th>
                                <th>Category</th>
                                <th>Preview</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="responsesTableBody">
                            @forelse($responses as $response)
                            @php $response = (object) $response; @endphp
                            <tr data-response-id="{{ $response->id }}" onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <span class="fw-semibold">{{ $response->title }}</span>
                                </td>
                                <td>
                                    @if($response->shortcut)
                                    <code>{{ $response->shortcut }}</code>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($response->category)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($response->category) }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted" title="{{ $response->content }}">
                                        {{ Str::limit($response->content, 60) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($response->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editResponse({{ json_encode($response) }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="previewResponse({{ json_encode($response) }})" title="Preview">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteResponse({{ $response->id }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No canned responses found</p>
                                        <small>Create quick responses to speed up your support workflow</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination -->
            <div class="card-footer bg-white border-0" id="responsesPaginationContainer">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="responsesPagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tips Section -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6><i class="bi bi-lightbulb me-2"></i>Tips for Canned Responses</h6>
                <ul class="mb-0 small">
                    <li>Use template variables like <code>@{{customer.first_name}}</code> for personalization</li>
                    <li>Create shortcuts (e.g., <code>/tracking</code>) for quick access while typing</li>
                    <li>Organize responses by category to find them easily</li>
                    <li>Keep responses professional but warm and friendly</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="responseModalTitle">Add Canned Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="responseForm">
                <div class="modal-body">
                    <input type="hidden" id="responseId" name="id">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="responseTitle" name="title" required placeholder="e.g., Order Status Inquiry">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Shortcut</label>
                            <input type="text" class="form-control" id="responseShortcut" name="shortcut" placeholder="e.g., /orderstatus">
                            <small class="text-muted">Type this in the message box to insert</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="responseCategory" name="category">
                                <option value="">No category</option>
                                <option value="order">Order</option>
                                <option value="shipping">Shipping</option>
                                <option value="return">Return</option>
                                <option value="product">Product</option>
                                <option value="billing">Billing</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="responseActive" name="is_active" checked>
                                <label class="form-check-label" for="responseActive">Active</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="responseContent" name="content" rows="8" required
                                      placeholder="Type your canned response here..."></textarea>
                            <small class="text-muted">You can use variables like @{{customer.first_name}}, @{{order.number}}, etc.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="responseSubmitBtn">Add Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="previewTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light rounded p-3" id="previewContent" style="white-space: pre-wrap;"></div>
            </div>
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
    var selectedRows = document.querySelectorAll('#responsesTableBody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    // Add selection to clicked row
    row.classList.add('row-selected');
}

// Sample canned responses data
var sampleResponses = [
    { id: 1, title: 'Order Status Inquiry', shortcut: '/orderstatus', category: 'order', content: 'Thank you for reaching out! I would be happy to help you with your order status. Let me look that up for you right away.', is_active: true },
    { id: 2, title: 'Shipping Delay Apology', shortcut: '/shipdelay', category: 'shipping', content: 'We sincerely apologize for the delay in shipping your order. We understand how frustrating this can be and we are working hard to get your package to you as soon as possible.', is_active: true },
    { id: 3, title: 'Refund Request', shortcut: '/refund', category: 'return', content: 'I understand you would like to request a refund. I would be happy to help you with that. Please allow 5-7 business days for the refund to appear in your account.', is_active: true },
    { id: 4, title: 'Product Information', shortcut: '/productinfo', category: 'product', content: 'Thank you for your interest in our products! I would be happy to provide more information about this item for you.', is_active: true },
    { id: 5, title: 'Billing Question', shortcut: '/billing', category: 'billing', content: 'I see you have a question about your billing. Let me review your account and help resolve this for you.', is_active: true },
    { id: 6, title: 'Thank You Response', shortcut: '/thanks', category: 'general', content: 'Thank you so much for your kind words! We truly appreciate your business and feedback. Is there anything else I can help you with today?', is_active: true },
    { id: 7, title: 'Return Instructions', shortcut: '/return', category: 'return', content: 'To initiate a return, please pack the item securely in its original packaging and use the prepaid shipping label we will email you within 24 hours.', is_active: true },
    { id: 8, title: 'Tracking Information', shortcut: '/tracking', category: 'shipping', content: 'Your order has been shipped! You can track your package using the tracking number provided in your shipping confirmation email.', is_active: true }
];

// Pagination variables
var responsesCurrentPage = 1;
var responsesPerPage = 5;
var serverResponses = @json($responses);
var allResponsesData = serverResponses.length > 0 ? serverResponses : sampleResponses;

// Initialize pagination on page load
document.addEventListener('DOMContentLoaded', function() {
    renderResponsesTable();
});

function renderResponsesTable() {
    var tbody = document.getElementById('responsesTableBody');
    var startIndex = (responsesCurrentPage - 1) * responsesPerPage;
    var endIndex = startIndex + responsesPerPage;
    var pageData = allResponsesData.slice(startIndex, endIndex);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><div class="text-muted"><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">No canned responses found</p><small>Create quick responses to speed up your support workflow</small></div></td></tr>';
        document.getElementById('responsesPaginationContainer').style.display = 'none';
        return;
    }

    tbody.innerHTML = pageData.map(function(response) {
        var shortcut = response.shortcut ? '<code>' + response.shortcut + '</code>' : '<span class="text-muted">-</span>';
        var category = response.category ? '<span class="badge bg-secondary-subtle text-secondary">' + response.category.charAt(0).toUpperCase() + response.category.slice(1) + '</span>' : '<span class="text-muted">-</span>';
        var status = response.is_active ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>';
        var preview = response.content.length > 60 ? response.content.substring(0, 60) + '...' : response.content;

        return '<tr data-response-id="' + response.id + '" onclick="highlightRow(event)" style="cursor: pointer;">' +
            '<td class="ps-4"><span class="fw-semibold">' + response.title + '</span></td>' +
            '<td>' + shortcut + '</td>' +
            '<td>' + category + '</td>' +
            '<td><span class="text-muted" title="' + response.content.replace(/"/g, '&quot;') + '">' + preview + '</span></td>' +
            '<td class="text-center">' + status + '</td>' +
            '<td class="text-end pe-4">' +
                '<div class="btn-group btn-group-sm">' +
                    '<button class="btn btn-outline-primary" onclick="editResponse(' + JSON.stringify(response).replace(/"/g, '&quot;') + ')" title="Edit"><i class="bi bi-pencil"></i></button>' +
                    '<button class="btn btn-outline-info" onclick="previewResponse(' + JSON.stringify(response).replace(/"/g, '&quot;') + ')" title="Preview"><i class="bi bi-eye"></i></button>' +
                    '<button class="btn btn-outline-danger" onclick="deleteResponse(' + response.id + ')" title="Delete"><i class="bi bi-trash"></i></button>' +
                '</div>' +
            '</td></tr>';
    }).join('');

    renderResponsesPagination();
}

function renderResponsesPagination() {
    var totalPages = Math.ceil(allResponsesData.length / responsesPerPage);
    var paginationEl = document.getElementById('responsesPagination');
    var container = document.getElementById('responsesPaginationContainer');

    if (totalPages <= 1) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    var html = '';

    // Previous button
    html += '<li class="page-item ' + (responsesCurrentPage === 1 ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="goToResponsesPage(' + (responsesCurrentPage - 1) + '); return false;">&laquo;</a></li>';

    // Page numbers
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === responsesCurrentPage ? 'active' : '') + '">' +
                '<a class="page-link" href="#" onclick="goToResponsesPage(' + i + '); return false;">' + i + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (responsesCurrentPage === totalPages ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="goToResponsesPage(' + (responsesCurrentPage + 1) + '); return false;">&raquo;</a></li>';

    paginationEl.innerHTML = html;
}

function goToResponsesPage(page) {
    var totalPages = Math.ceil(allResponsesData.length / responsesPerPage);
    if (page < 1 || page > totalPages) return;
    responsesCurrentPage = page;
    renderResponsesTable();
}

// Edit response
function editResponse(response) {
    document.getElementById('responseModalTitle').textContent = 'Edit Canned Response';
    document.getElementById('responseSubmitBtn').textContent = 'Update Response';
    document.getElementById('responseId').value = response.id;
    document.getElementById('responseTitle').value = response.title;
    document.getElementById('responseShortcut').value = response.shortcut || '';
    document.getElementById('responseCategory').value = response.category || '';
    document.getElementById('responseActive').checked = response.is_active;
    document.getElementById('responseContent').value = response.content;

    new bootstrap.Modal(document.getElementById('responseModal')).show();
}

// Preview response
function previewResponse(response) {
    document.getElementById('previewTitle').textContent = response.title;

    // Replace template variables with sample values
    let content = response.content
        .replace(/@?\{\{customer\.first_name\}\}/g, 'John')
        .replace(/@?\{\{customer\.last_name\}\}/g, 'Smith')
        .replace(/@?\{\{order\.number\}\}/g, 'ORD-12345')
        .replace(/@?\{\{order\.total\}\}/g, '$150.00');

    document.getElementById('previewContent').textContent = content;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Reset modal
document.getElementById('responseModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('responseModalTitle').textContent = 'Add Canned Response';
    document.getElementById('responseSubmitBtn').textContent = 'Add Response';
    document.getElementById('responseForm').reset();
    document.getElementById('responseId').value = '';
});

// Form submission
document.getElementById('responseForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const responseId = document.getElementById('responseId').value;
    const isEdit = !!responseId;

    const data = {
        title: document.getElementById('responseTitle').value,
        shortcut: document.getElementById('responseShortcut').value || null,
        category: document.getElementById('responseCategory').value || null,
        is_active: document.getElementById('responseActive').checked,
        content: document.getElementById('responseContent').value
    };

    try {
        const url = isEdit
            ? `${API_BASE}/admin/support/canned-responses/${responseId}`
            : `${API_BASE}/admin/support/canned-responses`;

        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            showToast(isEdit ? 'Response updated successfully' : 'Response created successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            const error = await response.json();
            showToast(error.message || 'Failed to save response', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
});

// Delete response
async function deleteResponse(id) {
    if (!confirm('Are you sure you want to delete this canned response?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/support/canned-responses/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            showToast('Response deleted successfully', 'success');
            document.querySelector(`tr[data-response-id="${id}"]`)?.remove();
        } else {
            showToast('Failed to delete response', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
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
