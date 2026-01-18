@extends('layouts.admin')

@section('title', 'Customer Tags')

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
        <h1 class="h3 mb-1">Customer Tags</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Customer Tags</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tagModal">
        <i class="bi bi-plus-lg me-1"></i> Create Tag
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Tag Stats -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-tags-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Tags</h6>
                                <h3 class="mb-0" id="totalTags">{{ count($tags) }}</h3>
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
                                <h6 class="text-muted mb-1">Auto Tags</h6>
                                <h3 class="mb-0" id="autoTags">{{ collect($tags)->where('is_auto', true)->count() }}</h3>
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
                                    <i class="bi bi-hand-index-fill text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Manual Tags</h6>
                                <h3 class="mb-0" id="manualTags">{{ collect($tags)->where('is_auto', false)->count() }}</h3>
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
                                <h6 class="text-muted mb-1">Tagged Customers</h6>
                                <h3 class="mb-0" id="taggedCustomers">{{ collect($tags)->sum('customer_count') ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tags List -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">All Tags</h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="tagFilter" id="filterAll" value="all" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="filterAll">All</label>

                            <input type="radio" class="btn-check" name="tagFilter" id="filterAuto" value="auto">
                            <label class="btn btn-outline-secondary btn-sm" for="filterAuto">Auto</label>

                            <input type="radio" class="btn-check" name="tagFilter" id="filterManual" value="manual">
                            <label class="btn btn-outline-secondary btn-sm" for="filterManual">Manual</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tag</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th class="text-center">Customers</th>
                                <th class="text-center">Created</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tagsTableBody">
                            @forelse($tags as $tag)
                            <tr data-tag-id="{{ $tag['id'] ?? '' }}" data-is-auto="{{ ($tag['is_auto'] ?? false) ? 'true' : 'false' }}" onclick="highlightRow(event)" style="cursor: pointer;">
                                <td class="ps-4">
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $tag['color'] ?? '#6c757d' }}; font-size: 0.9rem;">
                                        {{ $tag['name'] ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $tag['description'] ?? 'No description' }}</span>
                                </td>
                                <td>
                                    @if($tag['is_auto'] ?? false)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-lightning-fill me-1"></i>Automatic
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-hand-index me-1"></i>Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $tag['customer_count'] ?? 0 }}</span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ isset($tag['created_at']) ? \Carbon\Carbon::parse($tag['created_at'])->format('M d, Y') : '-' }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editTag({{ json_encode($tag) }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="viewTagCustomers({{ $tag['id'] ?? 0 }})" title="View Customers">
                                            <i class="bi bi-people"></i>
                                        </button>
                                        @if(!($tag['is_auto'] ?? false))
                                        <button class="btn btn-outline-danger" onclick="deleteTag({{ $tag['id'] ?? 0 }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                        <p class="mb-0">No tags found</p>
                                        <small>Create your first customer tag to get started</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination -->
            <div class="card-footer bg-white border-0" id="tagsPaginationContainer">
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="tagsPagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Tag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="tagModalTitle">Create Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tagForm">
                <div class="modal-body">
                    <input type="hidden" id="tagId" name="id">

                    <div class="mb-3">
                        <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tagName" name="name" required maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" class="form-control form-control-color" id="tagColor" name="color" value="#3498db">
                            <div id="colorPreview" class="badge rounded-pill px-3 py-2" style="background-color: #3498db;">
                                Preview
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="tagDescription" name="description" rows="3" maxlength="255"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="tagIsAuto" name="is_auto">
                        <label class="form-check-label" for="tagIsAuto">
                            <strong>Automatic Tag</strong>
                            <small class="d-block text-muted">System will automatically assign this tag based on rules</small>
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="tagSubmitBtn">Create Tag</button>
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
    var selectedRows = document.querySelectorAll('#tagsTableBody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    // Add selection to clicked row
    row.classList.add('row-selected');
}

// Pagination variables
var tagsCurrentPage = 1;
var tagsPerPage = 10;
var allTagsData = @json($tags);
var currentFilter = 'all';

// Initialize pagination on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTagsTable();
});

function getFilteredTags() {
    if (currentFilter === 'all') return allTagsData;
    if (currentFilter === 'auto') return allTagsData.filter(t => t.is_auto);
    if (currentFilter === 'manual') return allTagsData.filter(t => !t.is_auto);
    return allTagsData;
}

function renderTagsTable() {
    var tbody = document.getElementById('tagsTableBody');
    var filteredData = getFilteredTags();
    var startIndex = (tagsCurrentPage - 1) * tagsPerPage;
    var endIndex = startIndex + tagsPerPage;
    var pageData = filteredData.slice(startIndex, endIndex);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><div class="text-muted"><i class="bi bi-tags fs-1 d-block mb-2"></i><p class="mb-0">No tags found</p><small>Create your first customer tag to get started</small></div></td></tr>';
        document.getElementById('tagsPaginationContainer').style.display = 'none';
        return;
    }

    tbody.innerHTML = pageData.map(function(tag) {
        var typeHtml = tag.is_auto
            ? '<span class="badge bg-success-subtle text-success"><i class="bi bi-lightning-fill me-1"></i>Automatic</span>'
            : '<span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-hand-index me-1"></i>Manual</span>';

        var deleteBtn = !tag.is_auto
            ? '<button class="btn btn-outline-danger" onclick="deleteTag(' + tag.id + ')" title="Delete"><i class="bi bi-trash"></i></button>'
            : '';

        var createdDate = tag.created_at ? new Date(tag.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-';

        return '<tr data-tag-id="' + (tag.id || '') + '" data-is-auto="' + (tag.is_auto ? 'true' : 'false') + '" onclick="highlightRow(event)" style="cursor: pointer;">' +
            '<td class="ps-4"><span class="badge rounded-pill px-3 py-2" style="background-color: ' + (tag.color || '#6c757d') + '; font-size: 0.9rem;">' + (tag.name || 'Unknown') + '</span></td>' +
            '<td><span class="text-muted">' + (tag.description || 'No description') + '</span></td>' +
            '<td>' + typeHtml + '</td>' +
            '<td class="text-center"><span class="fw-semibold">' + (tag.customer_count || 0) + '</span></td>' +
            '<td class="text-center text-muted">' + createdDate + '</td>' +
            '<td class="text-end pe-4">' +
                '<div class="btn-group btn-group-sm">' +
                    '<button class="btn btn-outline-primary" onclick="editTag(' + JSON.stringify(tag).replace(/"/g, '&quot;') + ')" title="Edit"><i class="bi bi-pencil"></i></button>' +
                    '<button class="btn btn-outline-info" onclick="viewTagCustomers(' + (tag.id || 0) + ')" title="View Customers"><i class="bi bi-people"></i></button>' +
                    deleteBtn +
                '</div>' +
            '</td></tr>';
    }).join('');

    renderTagsPagination();
}

function renderTagsPagination() {
    var filteredData = getFilteredTags();
    var totalPages = Math.ceil(filteredData.length / tagsPerPage);
    var paginationEl = document.getElementById('tagsPagination');
    var container = document.getElementById('tagsPaginationContainer');

    if (totalPages <= 1) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    var html = '';

    // Previous button
    html += '<li class="page-item ' + (tagsCurrentPage === 1 ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="goToTagsPage(' + (tagsCurrentPage - 1) + '); return false;">&laquo;</a></li>';

    // Page numbers
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === tagsCurrentPage ? 'active' : '') + '">' +
                '<a class="page-link" href="#" onclick="goToTagsPage(' + i + '); return false;">' + i + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (tagsCurrentPage === totalPages ? 'disabled' : '') + '">' +
            '<a class="page-link" href="#" onclick="goToTagsPage(' + (tagsCurrentPage + 1) + '); return false;">&raquo;</a></li>';

    paginationEl.innerHTML = html;
}

function goToTagsPage(page) {
    var filteredData = getFilteredTags();
    var totalPages = Math.ceil(filteredData.length / tagsPerPage);
    if (page < 1 || page > totalPages) return;
    tagsCurrentPage = page;
    renderTagsTable();
}

// Color preview update
document.getElementById('tagColor').addEventListener('input', function() {
    const preview = document.getElementById('colorPreview');
    preview.style.backgroundColor = this.value;
});

// Tag name preview
document.getElementById('tagName').addEventListener('input', function() {
    const preview = document.getElementById('colorPreview');
    preview.textContent = this.value || 'Preview';
});

// Filter tags
document.querySelectorAll('input[name="tagFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        currentFilter = this.value;
        tagsCurrentPage = 1;
        renderTagsTable();
    });
});

// Open edit modal
function editTag(tag) {
    document.getElementById('tagModalTitle').textContent = 'Edit Tag';
    document.getElementById('tagSubmitBtn').textContent = 'Update Tag';
    document.getElementById('tagId').value = tag.id;
    document.getElementById('tagName').value = tag.name;
    document.getElementById('tagColor').value = tag.color;
    document.getElementById('tagDescription').value = tag.description || '';
    document.getElementById('tagIsAuto').checked = tag.is_auto;

    const preview = document.getElementById('colorPreview');
    preview.style.backgroundColor = tag.color;
    preview.textContent = tag.name;

    new bootstrap.Modal(document.getElementById('tagModal')).show();
}

// Reset modal on close
document.getElementById('tagModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('tagModalTitle').textContent = 'Create Tag';
    document.getElementById('tagSubmitBtn').textContent = 'Create Tag';
    document.getElementById('tagForm').reset();
    document.getElementById('tagId').value = '';
    document.getElementById('colorPreview').style.backgroundColor = '#3498db';
    document.getElementById('colorPreview').textContent = 'Preview';
});

// Form submission
document.getElementById('tagForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const tagId = document.getElementById('tagId').value;
    const isEdit = !!tagId;

    const data = {
        name: document.getElementById('tagName').value,
        color: document.getElementById('tagColor').value,
        description: document.getElementById('tagDescription').value,
        is_auto: document.getElementById('tagIsAuto').checked
    };

    try {
        const url = isEdit
            ? `${API_BASE}/admin/crm/tags/${tagId}`
            : `${API_BASE}/admin/crm/tags`;

        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            showToast(isEdit ? 'Tag updated successfully' : 'Tag created successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            const error = await response.json();
            showToast(error.message || 'Failed to save tag', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
});

// Delete tag
async function deleteTag(id) {
    if (!confirm('Are you sure you want to delete this tag? This will remove it from all customers.')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/crm/tags/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            showToast('Tag deleted successfully', 'success');
            document.querySelector(`tr[data-tag-id="${id}"]`)?.remove();
        } else {
            showToast('Failed to delete tag', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// View customers with tag
function viewTagCustomers(tagId) {
    window.location.href = `{{ route('admin.customers') }}?tag=${tagId}`;
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
