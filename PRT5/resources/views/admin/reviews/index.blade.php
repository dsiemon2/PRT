@extends('layouts.admin')

@section('title', 'Reviews Management')

@section('styles')
<style>
    .review-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .rating-stars {
        color: #ffc107;
    }
    .review-text {
        white-space: pre-wrap;
        max-height: 150px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-star"></i> Reviews Management</h1>
            <p class="lead text-muted">Manage customer reviews and ratings</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    <li class="breadcrumb-item active">Reviews Management</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Flash Messages --}}
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

    {{-- Summary Stats --}}
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                    <small class="text-muted">Total Reviews</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h3 class="mb-0 text-warning">{{ number_format($stats['pending']) }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h3 class="mb-0 text-success">{{ number_format($stats['approved']) }}</h3>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h3 class="mb-0 text-danger">{{ number_format($stats['rejected']) }}</h3>
                    <small class="text-muted">Rejected</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <h3 class="mb-0 text-secondary">{{ number_format($stats['spam']) }}</h3>
                    <small class="text-muted">Spam</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center" style="background-color: #fff3cd;">
                <div class="card-body">
                    <h3 class="mb-0">
                        <span class="rating-stars">
                            {{ number_format($stats['avg_rating'], 1) }} &#9733;
                        </span>
                    </h3>
                    <small class="text-muted">Avg Rating</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $filters['status'] === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $filters['status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="spam" {{ $filters['status'] === 'spam' ? 'selected' : '' }}>Spam</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ $filters['rating'] === 'all' ? 'selected' : '' }}>All Ratings</option>
                        <option value="5" {{ $filters['rating'] === '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ $filters['rating'] === '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ $filters['rating'] === '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ $filters['rating'] === '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ $filters['rating'] === '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search reviews, products, customers..."
                           value="{{ $filters['search'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Apply filter criteria to reviews list">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reviews List (Card Grid like prt4) --}}
    <div class="row">
        @forelse($reviews as $review)
            @php
                $statusColors = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'spam' => 'secondary',
                ];
                $statusColor = $statusColors[$review->status] ?? 'secondary';
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card review-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        &#9733;
                                    @else
                                        &#9734;
                                    @endif
                                @endfor
                            </span>
                        </div>
                        <span class="badge bg-{{ $statusColor }}">
                            {{ ucfirst($review->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="{{ route('products.show', $review->product_id) }}" target="_blank">
                                {{ Str::limit($review->product->ShortDescription ?? 'Unknown Product', 40) }}
                            </a>
                        </h6>
                        <div class="mb-2">
                            <span class="rating-stars" style="font-size: 1.1rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        &#9733;
                                    @else
                                        &#9734;
                                    @endif
                                @endfor
                            </span>
                            <span class="text-muted small">({{ $review->rating }}/5)</span>
                        </div>
                        @if($review->review_title)
                            <strong>{{ Str::limit($review->review_title, 30) }}</strong><br>
                        @endif
                        <div class="review-text mb-3">
                            {{ $review->review_text }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-person"></i>
                            @if($review->user)
                                {{ $review->user->first_name }} {{ $review->user->last_name }}
                            @else
                                Guest
                            @endif
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar"></i>
                            {{ $review->created_at->format('M j, Y g:i A') }}
                        </div>
                        @if($review->is_verified_purchase)
                            <span class="badge bg-info mt-2">
                                <i class="bi bi-check-circle"></i> Verified Purchase
                            </span>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            @if($review->status !== 'approved')
                                <button class="btn btn-sm btn-success" onclick="updateReview({{ $review->id }}, 'approve')" data-bs-toggle="tooltip" title="Approve this review for public display">
                                    <i class="bi bi-check"></i> Approve
                                </button>
                            @endif
                            @if($review->status !== 'rejected')
                                <button class="btn btn-sm btn-warning" onclick="updateReview({{ $review->id }}, 'reject')" data-bs-toggle="tooltip" title="Reject this review">
                                    <i class="bi bi-x"></i> Reject
                                </button>
                            @endif
                            @if($review->status !== 'spam')
                                <button class="btn btn-sm btn-secondary" onclick="updateReview({{ $review->id }}, 'spam')" data-bs-toggle="tooltip" title="Mark this review as spam">
                                    <i class="bi bi-trash"></i> Spam
                                </button>
                            @endif
                            <button class="btn btn-sm btn-danger" onclick="deleteReview({{ $review->id }})" data-bs-toggle="tooltip" title="Permanently delete this review">
                                <i class="bi bi-trash3"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-star display-1 text-muted"></i>
                    <p class="lead mt-3">No reviews found</p>
                    <p class="text-muted">Reviews will appear here once customers start rating products</p>
                </div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="col-12 mt-4">
                <nav>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
                        </div>
                        {{ $reviews->links() }}
                    </div>
                </nav>
            </div>
        @endif
    </div>
</div>

{{-- Hidden form for review actions (same as prt4) --}}
<form id="reviewActionForm" action="" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="reviewStatus">
</form>

<form id="reviewDeleteForm" action="" method="POST" style="display: none;">
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

function updateReview(reviewId, action) {
    const statusMap = {
        'approve': 'approved',
        'reject': 'rejected',
        'spam': 'spam'
    };

    if (confirm('Are you sure you want to ' + action + ' this review?')) {
        const form = document.getElementById('reviewActionForm');
        form.action = '{{ url("admin/reviews") }}/' + reviewId;
        document.getElementById('reviewStatus').value = statusMap[action];
        form.submit();
    }
}

function deleteReview(reviewId) {
    if (confirm('Are you sure you want to permanently delete this review? This cannot be undone.')) {
        const form = document.getElementById('reviewDeleteForm');
        form.action = '{{ url("admin/reviews") }}/' + reviewId;
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
