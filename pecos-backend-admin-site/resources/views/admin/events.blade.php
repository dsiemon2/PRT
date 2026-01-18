@extends('layouts.admin')

@section('title', 'Event Management')

@section('content')
<div class="page-header">
    <h1>Event Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Events</li>
        </ol>
    </nav>
</div>

<!-- Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search events...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="past">Past</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterEvents()">Filter</button>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" onclick="newEvent()"><i class="bi bi-plus"></i> Create Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Events Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Entered By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="eventsTable">
            <tr>
                <td colspan="6" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading events...
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

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title" id="eventModalLabel">Add New Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="eventId" name="id">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="eventName" class="form-label">Event Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventName" name="EventName" required>
                        </div>

                        <div class="col-md-6">
                            <label for="eventStartDate" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="eventStartDate" name="StartDate" required>
                        </div>

                        <div class="col-md-6">
                            <label for="eventEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="eventEndDate" name="EndDate">
                        </div>

                        <div class="col-md-6">
                            <label for="eventStartTime" class="form-label">Start Time</label>
                            <input type="text" class="form-control" id="eventStartTime" name="StartTime" placeholder="e.g., 7:00 PM CST">
                        </div>

                        <div class="col-md-6">
                            <label for="eventEndTime" class="form-label">End Time</label>
                            <input type="text" class="form-control" id="eventEndTime" name="EndTime" placeholder="e.g., 9:00 PM CST">
                        </div>

                        <div class="col-12">
                            <label for="eventEnteredBy" class="form-label">Entered By</label>
                            <input type="text" class="form-control" id="eventEnteredBy" name="EnteredBy" placeholder="Marketing Team">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveEvent()">
                    <i class="bi bi-save"></i> <span id="saveButtonText">Create Event</span>
                </button>
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
let eventModal;

document.addEventListener('DOMContentLoaded', function() {
    eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    loadEvents();
});

async function loadEvents(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/events?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${status}`;

        const response = await fetch(url);
        const data = await response.json();

        const events = data.data || [];
        const meta = {
            current_page: data.current_page || data.meta?.current_page || 1,
            last_page: data.last_page || data.meta?.last_page || 1,
            from: data.from || data.meta?.from || 1,
            to: data.to || data.meta?.to || events.length,
            total: data.total || data.meta?.total || events.length,
            per_page: data.per_page || data.meta?.per_page || perPage
        };

        renderEvents(events);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading events:', error);
        document.getElementById('eventsTable').innerHTML =
            '<tr><td colspan="6" class="text-center text-danger">Error loading events</td></tr>';
    }
}

function renderEvents(events) {
    const tbody = document.getElementById('eventsTable');

    if (events.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="6" class="text-center py-4 text-muted">No events found</td>
        </tr>`;
        return;
    }

    let html = '';
    events.forEach(event => {
        const startDate = event.StartDate || event.start_date;
        const eventDate = startDate ? new Date(startDate).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'TBD';
        const isPast = startDate && new Date(startDate) < new Date();

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td>${event.EventName || event.title || 'Untitled Event'}</td>`;
        html += `<td>${eventDate}</td>`;
        html += `<td>${event.StartTime || ''} - ${event.EndTime || ''}</td>`;
        html += `<td>${event.EnteredBy || 'N/A'}</td>`;
        html += `<td><span class="status-badge ${isPast ? 'inactive' : 'active'}">${isPast ? 'Past' : 'Upcoming'}</span></td>`;
        html += `<td>
            <button class="btn btn-sm btn-outline-primary" onclick="editEvent(${event.ID || event.id})" title="Edit"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteEvent(${event.ID || event.id}, '${(event.EventName || event.title || 'Untitled').replace(/'/g, "\\'")}')" title="Delete"><i class="bi bi-trash"></i></button>
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
    html += `<a class="page-link" href="#" onclick="loadEvents(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadEvents(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadEvents(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadEvents(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadEvents(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterEvents() {
    loadEvents(1);
}

function newEvent() {
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('eventModalLabel').textContent = 'Add New Event';
    document.getElementById('saveButtonText').textContent = 'Create Event';
    eventModal.show();
}

async function editEvent(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/events/${id}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch event');
        }

        const result = await response.json();
        const event = result.data || result;

        document.getElementById('eventId').value = event.ID || event.id;
        document.getElementById('eventName').value = event.EventName || event.title || '';
        document.getElementById('eventStartDate').value = event.StartDate ? event.StartDate.split('T')[0] : '';
        document.getElementById('eventEndDate').value = event.EndDate ? event.EndDate.split('T')[0] : '';
        document.getElementById('eventStartTime').value = event.StartTime || '';
        document.getElementById('eventEndTime').value = event.EndTime || '';
        document.getElementById('eventEnteredBy').value = event.EnteredBy || '';

        document.getElementById('eventModalLabel').textContent = 'Edit Event';
        document.getElementById('saveButtonText').textContent = 'Update Event';
        eventModal.show();

    } catch (error) {
        console.error('Edit error:', error);
        alert('Error loading event data');
    }
}

async function saveEvent() {
    const eventId = document.getElementById('eventId').value;
    const isEdit = !!eventId;

    const formData = {
        EventName: document.getElementById('eventName').value,
        StartDate: document.getElementById('eventStartDate').value,
        EndDate: document.getElementById('eventEndDate').value || document.getElementById('eventStartDate').value,
        StartTime: document.getElementById('eventStartTime').value,
        EndTime: document.getElementById('eventEndTime').value,
        EnteredBy: document.getElementById('eventEnteredBy').value || 'Admin'
    };

    try {
        const url = isEdit ? `${API_BASE}/admin/events/${eventId}` : `${API_BASE}/admin/events`;
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.ok) {
            alert(isEdit ? 'Event updated successfully' : 'Event created successfully');
            eventModal.hide();
            loadEvents(currentPage);
        } else {
            alert('Error: ' + (data.message || 'Failed to save event'));
        }
    } catch (error) {
        console.error('Save error:', error);
        alert('Error saving event');
    }
}

async function deleteEvent(id, name) {
    if (!confirm('Are you sure you want to delete "' + name + '"?\n\nThis action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/events/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            alert('Event deleted successfully');
            loadEvents(currentPage);
        } else {
            alert('Error: ' + (data.message || 'Failed to delete event'));
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('Error deleting event');
    }
}

// Fix aria-hidden focus issue on modals
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hide.bs.modal', function () {
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });
});
</script>
@endpush
