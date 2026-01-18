@extends('layouts.admin')

@section('title', 'Events Management')

@section('styles')
<style>
    .event-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .past-event {
        opacity: 0.7;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-calendar-event"></i> Events Management</h1>
            <p class="lead text-muted">Add, edit, and manage store events</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    <li class="breadcrumb-item active">Events Management</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add/Edit Event Form -->
    <div class="card mb-4">
        <div class="card-header" style="background-color: var(--prt-brown); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-{{ isset($editEvent) ? 'pencil-square' : 'plus-circle' }}"></i>
                {{ isset($editEvent) ? 'Edit Event' : 'Add New Event' }}
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ isset($editEvent) ? route('admin.events.update', $editEvent) : route('admin.events.store') }}">
                @csrf
                @if(isset($editEvent))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="event_name" class="form-label">Event Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="event_name" name="EventName"
                               value="{{ old('EventName', $editEvent->EventName ?? '') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="StartDate"
                               value="{{ old('StartDate', isset($editEvent) ? \Carbon\Carbon::parse($editEvent->StartDate)->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="EndDate"
                               value="{{ old('EndDate', isset($editEvent) ? \Carbon\Carbon::parse($editEvent->EndDate)->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="start_time" class="form-label">Start Time (e.g., 7:00 PM)</label>
                        <input type="text" class="form-control" id="start_time" name="StartTime"
                               value="{{ old('StartTime', $editEvent->StartTime ?? '') }}"
                               placeholder="e.g., 2:00 PM CST">
                    </div>

                    <div class="col-md-3">
                        <label for="end_time" class="form-label">End Time (e.g., 9:00 PM)</label>
                        <input type="text" class="form-control" id="end_time" name="EndTime"
                               value="{{ old('EndTime', $editEvent->EndTime ?? '') }}"
                               placeholder="e.g., 5:00 PM CST">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ isset($editEvent) ? 'Save changes to this event' : 'Add new event' }}">
                            <i class="bi bi-{{ isset($editEvent) ? 'save' : 'plus-circle' }}"></i>
                            {{ isset($editEvent) ? 'Update Event' : 'Add Event' }}
                        </button>
                        @if(isset($editEvent))
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Cancel editing and return to list">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        @else
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary" target="_blank" data-bs-toggle="tooltip" title="View public events page">
                                <i class="bi bi-eye"></i> View Public Page
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Events List -->
    <div class="card">
        <div class="card-header" style="background-color: var(--prt-brown); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> All Events ({{ $events->total() }})
            </h5>
        </div>
        <div class="card-body">
            @if($events->count() > 0)
                <div class="row g-3">
                    @foreach($events as $event)
                        @php
                            $isPast = \Carbon\Carbon::parse($event->EndDate)->isPast();
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card event-card h-100 {{ $isPast ? 'past-event' : '' }}">
                                <div class="card-header" style="background-color: var(--prt-red); color: white;">
                                    <h6 class="mb-0">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $event->EventName }}
                                    </h6>
                                    @if($isPast)
                                        <small><i class="bi bi-clock-history"></i> Past Event</small>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-calendar-check"></i> Start:</strong>
                                        {{ \Carbon\Carbon::parse($event->StartDate)->format('F j, Y') }}
                                        @if($event->StartTime)
                                            <br><small class="text-muted ms-4">{{ $event->StartTime }}</small>
                                        @endif
                                    </p>
                                    <p class="mb-2">
                                        <strong><i class="bi bi-calendar-x"></i> End:</strong>
                                        {{ \Carbon\Carbon::parse($event->EndDate)->format('F j, Y') }}
                                        @if($event->EndTime)
                                            <br><small class="text-muted ms-4">{{ $event->EndTime }}</small>
                                        @endif
                                    </p>
                                    @if($event->EnteredBy)
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-person"></i> By: {{ $event->EnteredBy }}
                                        </p>
                                    @endif
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('admin.events.index', ['edit' => $event->ID]) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit this event">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete this event"
                                                onclick="deleteEvent({{ $event->ID }}, '{{ addslashes($event->EventName) }}')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <p class="lead mt-3">No events found</p>
                    <p class="text-muted">Add your first event using the form above</p>
                </div>
            @endif

            <!-- Pagination -->
            @if($events->hasPages())
                <nav class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $events->firstItem() ?? 0 }} to {{ $events->lastItem() ?? 0 }} of {{ $events->total() }} events
                        </div>
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $events->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $events->previousPageUrl() }}" data-bs-toggle="tooltip" title="Go to previous page">Previous</a>
                            </li>

                            @php
                                $currentPage = $events->currentPage();
                                $lastPage = $events->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp

                            @if($startPage > 1)
                                <li class="page-item"><a class="page-link" href="{{ $events->url(1) }}">1</a></li>
                                @if($startPage > 2)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif

                            @for($i = $startPage; $i <= $endPage; $i++)
                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $events->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                                <li class="page-item"><a class="page-link" href="{{ $events->url($lastPage) }}">{{ $lastPage }}</a></li>
                            @endif

                            <li class="page-item {{ !$events->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $events->nextPageUrl() }}" data-bs-toggle="tooltip" title="Go to next page">Next</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function deleteEvent(eventId, eventName) {
    if (confirm('Are you sure you want to delete "' + eventName + '"?\n\nThis action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("admin/events") }}/' + eventId;
        form.submit();
    }
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
