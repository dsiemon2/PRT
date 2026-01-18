@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">FAQ</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <h1 style="color: var(--prt-brown);"><i class="bi bi-question-circle"></i> Frequently Asked Questions</h1>

    {{-- Search --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('faq') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search FAQs..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Search frequently asked questions">
                        <i class="bi bi-search"></i> Search
                    </button>
                    @if($search)
                        <a href="{{ route('faq') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Clear search and show all FAQs">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($search && $faqs)
        {{-- Search Results --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Search Results for "{{ $search }}"</h5>
            </div>
            <div class="card-body">
                @if($faqs->count() > 0)
                    <div class="accordion" id="searchResults">
                        @foreach($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#search-{{ $faq->id }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="search-{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                     data-bs-parent="#searchResults">
                                    <div class="accordion-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No results found for "{{ $search }}".</p>
                @endif
            </div>
        </div>
    @else
        {{-- FAQ Categories --}}
        @foreach($categories as $category)
            @if($category->faqs->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            @if($category->icon)
                                <i class="bi bi-{{ $category->icon }}"></i>
                            @endif
                            {{ $category->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faq-category-{{ $category->id }}">
                            @foreach($category->faqs as $index => $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse"
                                         data-bs-parent="#faq-category-{{ $category->id }}">
                                        <div class="accordion-body">
                                            {!! $faq->answer !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    {{-- Still Have Questions --}}
    <div class="card border-primary">
        <div class="card-body text-center py-4">
            <i class="bi bi-chat-dots text-primary" style="font-size: 3rem;"></i>
            <h4 class="mt-3">Still Have Questions?</h4>
            <p class="text-muted">Can't find what you're looking for? Our team is here to help.</p>
            <a href="{{ route('contact') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Get in touch with our support team">
                <i class="bi bi-envelope"></i> Contact Us
            </a>
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
