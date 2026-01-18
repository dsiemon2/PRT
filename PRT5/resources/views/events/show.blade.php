@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Event Image --}}
        <div class="col-lg-6 mb-4">
            @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}"
                     class="img-fluid rounded shadow"
                     alt="{{ $event->title }}"
                     style="width: 100%; max-height: 400px; object-fit: cover;">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center shadow"
                     style="height: 300px;">
                    <i class="bi bi-calendar-event text-muted" style="font-size: 6rem;"></i>
                </div>
            @endif
        </div>

        {{-- Event Details --}}
        <div class="col-lg-6">
            <div class="mb-3">
                @if($event->start_date->isFuture())
                    <span class="badge bg-success fs-6">Upcoming</span>
                @elseif($event->end_date && $event->end_date->isPast())
                    <span class="badge bg-secondary fs-6">Past Event</span>
                @else
                    <span class="badge bg-primary fs-6">Happening Now</span>
                @endif
            </div>

            <h1 style="color: var(--prt-brown);">{{ $event->title }}</h1>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <h6 class="text-muted mb-1"><i class="bi bi-calendar"></i> Date</h6>
                            <p class="mb-0 fw-bold">
                                {{ $event->start_date->format('l, F j, Y') }}
                                @if($event->end_date && $event->end_date->format('Y-m-d') != $event->start_date->format('Y-m-d'))
                                    <br><small class="text-muted">to {{ $event->end_date->format('l, F j, Y') }}</small>
                                @endif
                            </p>
                        </div>
                        @if($event->start_time)
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-muted mb-1"><i class="bi bi-clock"></i> Time</h6>
                                <p class="mb-0 fw-bold">
                                    {{ $event->start_time }}
                                    @if($event->end_time)
                                        - {{ $event->end_time }}
                                    @endif
                                </p>
                            </div>
                        @endif
                        @if($event->location)
                            <div class="col-12 mb-3">
                                <h6 class="text-muted mb-1"><i class="bi bi-geo-alt"></i> Location</h6>
                                <p class="mb-0 fw-bold">{{ $event->location }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 mb-4">
                @if($event->registration_url && $event->start_date->isFuture())
                    <a href="{{ $event->registration_url }}" target="_blank" class="btn btn-primary btn-lg">
                        <i class="bi bi-ticket"></i> Register Now
                    </a>
                @endif
                <button onclick="shareEvent()" class="btn btn-outline-secondary">
                    <i class="bi bi-share"></i> Share
                </button>
            </div>

            {{-- Add to Calendar --}}
            @if($event->start_date->isFuture())
                <div class="dropdown mb-4">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar-plus"></i> Add to Calendar
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ $event->googleCalendarUrl }}" target="_blank">
                                <i class="bi bi-google"></i> Google Calendar
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ $event->icsDownloadUrl }}" download>
                                <i class="bi bi-apple"></i> Apple Calendar / Outlook
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Event Description --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Event Details</h5>
                </div>
                <div class="card-body">
                    <div class="event-description">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Events --}}
    @if($relatedEvents->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4"><i class="bi bi-calendar-event"></i> More Upcoming Events</h3>
                <div class="row">
                    @foreach($relatedEvents as $related)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                @if($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}"
                                         class="card-img-top"
                                         alt="{{ $related->title }}"
                                         style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                         style="height: 150px;">
                                        <i class="bi bi-calendar-event text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6>
                                        <a href="{{ route('events.show', $related) }}"
                                           class="text-decoration-none" style="color: var(--prt-brown);">
                                            {{ $related->title }}
                                        </a>
                                    </h6>
                                    <p class="card-text text-muted small">
                                        <i class="bi bi-calendar"></i> {{ $related->start_date->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Back to Events --}}
    <div class="mt-4">
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Events
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $event->title }}',
            text: '{{ Str::limit(strip_tags($event->description), 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Event link copied to clipboard!');
        });
    }
}
</script>
@endsection
