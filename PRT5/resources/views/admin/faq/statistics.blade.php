@extends('layouts.admin')

@section('title', 'FAQ Statistics')

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--prt-brown);
    }
    .progress-helpful {
        height: 25px;
    }
    .helpful-good {
        background-color: #28a745;
    }
    .helpful-ok {
        background-color: #ffc107;
    }
    .helpful-bad {
        background-color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-graph-up-arrow"></i> FAQ Statistics & Analytics</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('faq') }}">FAQ</a></li>
                    <li class="breadcrumb-item active">Statistics</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats->total_faqs) }}</div>
                <div class="text-muted">Total FAQs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats->total_views) }}</div>
                <div class="text-muted">Total Views</div>
                <small class="text-muted">Avg: {{ number_format($stats->avg_views, 1) }} per FAQ</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-success">{{ number_format($stats->total_helpful) }}</div>
                <div class="text-muted">Helpful Votes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value {{ $stats->overall_helpful_percentage >= 70 ? 'text-success' : ($stats->overall_helpful_percentage >= 50 ? 'text-warning' : 'text-danger') }}">
                    {{ $stats->overall_helpful_percentage }}%
                </div>
                <div class="text-muted">Overall Helpful Rate</div>
                <small class="text-muted">{{ number_format($stats->total_not_helpful) }} not helpful</small>
            </div>
        </div>
    </div>

    {{-- Sort Options --}}
    <div class="stat-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-sort-down"></i> Sort By</h4>
            <div class="btn-group" role="group">
                <a href="?sort=views" class="btn btn-sm {{ $sortBy == 'views' ? 'btn-primary' : 'btn-outline-primary' }}" data-bs-toggle="tooltip" title="Sort by most viewed FAQs">
                    Most Viewed
                </a>
                <a href="?sort=helpful_ratio" class="btn btn-sm {{ $sortBy == 'helpful_ratio' ? 'btn-primary' : 'btn-outline-primary' }}" data-bs-toggle="tooltip" title="Sort by helpful percentage">
                    Helpful %
                </a>
                <a href="?sort=total_votes" class="btn btn-sm {{ $sortBy == 'total_votes' ? 'btn-primary' : 'btn-outline-primary' }}" data-bs-toggle="tooltip" title="Sort by total votes received">
                    Most Voted
                </a>
                <a href="?sort=helpful" class="btn btn-sm {{ $sortBy == 'helpful' ? 'btn-primary' : 'btn-outline-primary' }}" data-bs-toggle="tooltip" title="Sort by most helpful votes">
                    Most Helpful
                </a>
                <a href="?sort=not_helpful" class="btn btn-sm {{ $sortBy == 'not_helpful' ? 'btn-primary' : 'btn-outline-primary' }}" data-bs-toggle="tooltip" title="Sort by least helpful votes">
                    Least Helpful
                </a>
            </div>
        </div>
    </div>

    {{-- FAQ Statistics Table --}}
    <div class="stat-card">
        <h4 class="mb-4"><i class="bi bi-table"></i> FAQ Performance</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background-color: var(--prt-brown, #8B4513); color: white;">
                    <tr>
                        <th style="width: 40%;">Question</th>
                        <th>Category</th>
                        <th class="text-center">Views</th>
                        <th class="text-center">👍 Helpful</th>
                        <th class="text-center">👎 Not Helpful</th>
                        <th class="text-center">Total Votes</th>
                        <th style="width: 20%;">Helpful Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faqs as $faq)
                        <tr>
                            <td>
                                <strong>{{ $faq->question }}</strong>
                                <br>
                                <small class="text-muted">ID: {{ $faq->id }}</small>
                            </td>
                            <td>
                                @if($faq->category_icon)
                                    <i class="{{ $faq->category_icon }}"></i>
                                @endif
                                {{ $faq->category_name }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ number_format($faq->views) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ number_format($faq->helpful_count) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ number_format($faq->not_helpful_count) }}</span>
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($faq->total_votes) }}</strong>
                            </td>
                            <td>
                                @if($faq->total_votes > 0)
                                    <div class="progress progress-helpful">
                                        <div class="progress-bar {{ $faq->helpful_percentage >= 70 ? 'helpful-good' : ($faq->helpful_percentage >= 50 ? 'helpful-ok' : 'helpful-bad') }}"
                                             role="progressbar"
                                             style="width: {{ $faq->helpful_percentage }}%"
                                             aria-valuenow="{{ $faq->helpful_percentage }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                            {{ $faq->helpful_percentage }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No votes yet</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <nav class="mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $totalFaqs > 0 ? (($page - 1) * $perPage) + 1 : 0 }} to {{ min($page * $perPage, $totalFaqs) }} of {{ $totalFaqs }} FAQs
                </div>
                <ul class="pagination mb-0">
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge(request()->query(), ['page' => $page - 1])) }}" data-bs-toggle="tooltip" title="Go to previous page">Previous</a>
                    </li>

                    @php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                    @endphp

                    @if($startPage > 1)
                        <li class="page-item"><a class="page-link" href="?{{ http_build_query(array_merge(request()->query(), ['page' => 1])) }}">1</a></li>
                        @if($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for($i = $startPage; $i <= $endPage; $i++)
                        <li class="page-item {{ $i == $page ? 'active' : '' }}">
                            <a class="page-link" href="?{{ http_build_query(array_merge(request()->query(), ['page' => $i])) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if($endPage < $totalPages)
                        @if($endPage < $totalPages - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="?{{ http_build_query(array_merge(request()->query(), ['page' => $totalPages])) }}">{{ $totalPages }}</a></li>
                    @endif

                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge(request()->query(), ['page' => $page + 1])) }}" data-bs-toggle="tooltip" title="Go to next page">Next</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    {{-- Insights --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="stat-card">
                <h5><i class="bi bi-trophy"></i> Best Performing FAQs</h5>
                <p class="text-muted">FAQs with highest helpful percentage (min 3 votes)</p>
                <ol>
                    @forelse($bestFaqs as $faq)
                        <li>
                            {{ $faq->question }}
                            <span class="badge bg-success">{{ $faq->helpful_percentage }}% helpful</span>
                        </li>
                    @empty
                        <li class="text-muted">Not enough votes yet</li>
                    @endforelse
                </ol>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <h5><i class="bi bi-exclamation-triangle"></i> Needs Improvement</h5>
                <p class="text-muted">FAQs with lowest helpful percentage (min 3 votes)</p>
                <ol>
                    @forelse($worstFaqs as $faq)
                        <li>
                            {{ $faq->question }}
                            <span class="badge bg-danger">{{ $faq->helpful_percentage }}% helpful</span>
                        </li>
                    @empty
                        <li class="text-muted">Not enough votes yet</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('faq') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to public FAQ page">
            <i class="bi bi-arrow-left"></i> Back to FAQ
        </a>
    </div>
</div>
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
</script>
@endpush
