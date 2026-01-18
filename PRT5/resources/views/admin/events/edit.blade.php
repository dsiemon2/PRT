@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-pencil"></i> Edit Event</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5">{{ old('description', $event->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date) }}" required data-bs-toggle="tooltip" title="Select the event start date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date) }}" data-bs-toggle="tooltip" title="Select the event end date (optional)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $event->start_time) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $event->end_time) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">Options</div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ $event->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" {{ $event->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label" for="isFeatured">Featured</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Save changes to this event">
                            <i class="bi bi-check"></i> Update Event
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Event Image</div>
                    <div class="card-body">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" class="img-fluid mb-2 rounded">
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*" data-bs-toggle="tooltip" title="Upload a new image to replace the current one">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
