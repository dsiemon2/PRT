@extends('layouts.app')

@section('title', 'Events Calendar')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3" style="color: var(--prt-brown);">
            <i class="bi bi-calendar-event"></i> Events Calendar
        </h1>
        <p class="lead" style="color: var(--prt-brown);">Stay updated with upcoming events at Pecos River Trading Company</p>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="content-card">
                {{-- Filters --}}
                <div class="mb-4">
                    <div class="btn-group" role="group">
                        <a href="{{ route('events.index') }}"
                           class="btn btn-{{ !request('type') ? 'primary' : 'outline-primary' }}" data-bs-toggle="tooltip" title="View upcoming events">
                            <i class="bi bi-calendar-check"></i> Upcoming
                        </a>
                        <a href="{{ route('events.index', ['type' => 'past']) }}"
                           class="btn btn-{{ request('type') === 'past' ? 'primary' : 'outline-primary' }}" data-bs-toggle="tooltip" title="View past events">
                            <i class="bi bi-calendar-x"></i> Past Events
                        </a>
                    </div>
                </div>

                @if($events->count() > 0)
                    @php
                        $lastMonth = 0;
                        $monthPrintFlag = false;
                    @endphp

                    @foreach($events as $event)
                        @php
                            $currentMonth = (int)$event->start_date->format('n');

                            if ($lastMonth != $currentMonth) {
                                $lastMonth = $currentMonth;
                                $monthPrintFlag = false;
                            }
                        @endphp

                        {{-- Print month header if not already printed --}}
                        @if(!$monthPrintFlag)
                            <div class="month-header mb-4 {{ !$loop->first ? 'mt-5' : '' }}">
                                <h2 class="fw-bold" style="color: var(--prt-brown);">
                                    <i class="bi bi-calendar-month"></i> {{ $event->start_date->format('F Y') }}
                                </h2>
                            </div>
                            @php $monthPrintFlag = true; @endphp
                        @endif

                        {{-- Event Card --}}
                        <div class="event-card mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header" style="background-color: var(--prt-red); color: white;">
                                    <h5 class="mb-0">
                                        <i class="bi bi-calendar-event"></i> {{ $event->title ?? $event->EventName ?? 'Untitled Event' }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="event-detail">
                                                <strong style="color: var(--prt-red);">
                                                    <i class="bi bi-calendar-check"></i> Start Date:
                                                </strong>
                                                <span class="ms-2">{{ $event->start_date->format('F j, Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="event-detail">
                                                <strong style="color: var(--prt-red);">
                                                    <i class="bi bi-calendar-x"></i> End Date:
                                                </strong>
                                                <span class="ms-2">
                                                    {{ $event->end_date ? $event->end_date->format('F j, Y') : 'Same Day' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="event-detail">
                                                <strong style="color: var(--prt-red);">
                                                    <i class="bi bi-clock"></i> Time:
                                                </strong>
                                                <span class="ms-2">
                                                    @if($event->start_time && $event->end_time)
                                                        {{ $event->start_time }} - {{ $event->end_time }}
                                                    @elseif($event->start_time)
                                                        {{ $event->start_time }}
                                                    @else
                                                        Time TBA
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @if($event->location)
                                        <div class="col-md-6">
                                            <div class="event-detail">
                                                <strong style="color: var(--prt-red);">
                                                    <i class="bi bi-geo-alt"></i> Location:
                                                </strong>
                                                <span class="ms-2">{{ $event->location }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @if($event->description)
                                    <div class="mt-3">
                                        <p class="mb-0">{{ Str::limit(strip_tags($event->description), 200) }}</p>
                                    </div>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="View full event details">
                                            <i class="bi bi-info-circle"></i> View Details
                                        </a>
                                        <a href="{{ route('events.ics', $event) }}" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Download event to your calendar">
                                            <i class="bi bi-calendar-plus"></i> Add to Calendar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">No Events Found</h3>
                        <p class="text-muted">
                            @if(request('type') === 'past')
                                There are no past events to display.
                            @else
                                Check back soon for upcoming events!
                            @endif
                        </p>
                        @if(request('type'))
                            <a href="{{ route('events.index') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Switch to upcoming events view">View Upcoming Events</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
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
