@extends('layouts.admin')

@section('title', 'Review Management')

@section('content')
<div class="page-header">
    <h1>Review Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reviews</li>
        </ol>
    </nav>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search reviews...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="ratingFilter">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterReviews()">Filter</button>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="reviewsTable">
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading reviews...
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

<!-- View Review Modal -->
<div class="modal fade" id="viewReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-prt text-white">
                <h5 class="modal-title">Review Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Product:</strong>
                        <p id="modalProductName"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Customer:</strong>
                        <p id="modalCustomerName"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Rating:</strong>
                        <p id="modalRating"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p id="modalStatus"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Review Title:</strong>
                    <p id="modalReviewTitle"></p>
                </div>
                <div class="mb-3">
                    <strong>Review Text:</strong>
                    <p id="modalReviewText" style="white-space: pre-wrap;"></p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date:</strong>
                        <p id="modalDate"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Verified Purchase:</strong>
                        <p id="modalVerified"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
let viewReviewModal;

document.addEventListener('DOMContentLoaded', function() {
    viewReviewModal = new bootstrap.Modal(document.getElementById('viewReviewModal'));
    loadReviews();
});

async function loadReviews(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const rating = document.getElementById('ratingFilter').value;
        const status = document.getElementById('statusFilter').value;

        let url = `${API_BASE}/admin/reviews?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (rating) url += `&rating=${rating}`;
        if (status) url += `&status=${status}`;

        const response = await fetch(url);
        const data = await response.json();

        const reviews = data.data || [];
        const meta = {
            current_page: data.current_page || data.meta?.current_page || 1,
            last_page: data.last_page || data.meta?.last_page || 1,
            from: data.from || data.meta?.from || 1,
            to: data.to || data.meta?.to || reviews.length,
            total: data.total || data.meta?.total || reviews.length,
            per_page: data.per_page || data.meta?.per_page || perPage
        };

        renderReviews(reviews);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading reviews:', error);
        document.getElementById('reviewsTable').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger">Error loading reviews</td></tr>';
    }
}

function renderReviews(reviews) {
    const tbody = document.getElementById('reviewsTable');

    if (reviews.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="7" class="text-center py-4 text-muted">No reviews found</td>
        </tr>`;
        return;
    }

    let html = '';
    reviews.forEach(review => {
        const rating = review.rating || 0;
        const productName = review.product?.ShortDescription || review.product?.short_description || 'Unknown Product';
        const customerName = review.user
            ? (review.user.first_name + ' ' + review.user.last_name)
            : (review.reviewer_name || 'Anonymous');
        const reviewText = review.review_text || review.review_title || '';
        const reviewDate = review.created_at
            ? new Date(review.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})
            : 'N/A';
        const status = review.status || 'pending';
        const statusClass = status === 'approved' ? 'active' : (status === 'rejected' ? 'inactive' : 'pending');

        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="bi bi-star${i <= rating ? '-fill' : ''}"></i>`;
        }

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td>${productName}</td>`;
        html += `<td>${customerName}</td>`;
        html += `<td><span class="text-warning">${stars}</span></td>`;
        html += `<td class="text-truncate" style="max-width: 200px;">${reviewText}</td>`;
        html += `<td>${reviewDate}</td>`;
        html += `<td><span class="status-badge ${statusClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td>`;
        html += `<td>
            <button class="btn btn-sm btn-outline-success" onclick="updateStatus(${review.id}, 'approved')" ${status === 'approved' ? 'disabled' : ''} title="Approve"><i class="bi bi-check"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="updateStatus(${review.id}, 'rejected')" ${status === 'rejected' ? 'disabled' : ''} title="Reject"><i class="bi bi-x"></i></button>
            <button class="btn btn-sm btn-outline-primary" onclick='viewReview(${JSON.stringify(review).replace(/'/g, "&#39;")})' title="View Details"><i class="bi bi-eye"></i></button>
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
    html += `<a class="page-link" href="#" onclick="loadReviews(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadReviews(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadReviews(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadReviews(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadReviews(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterReviews() {
    loadReviews(1);
}

async function updateStatus(reviewId, status) {
    if (!confirm(`Are you sure you want to ${status} this review?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/reviews/${reviewId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });

        const data = await response.json();

        if (data.success || response.ok) {
            alert(`Review ${status} successfully!`);
            loadReviews(currentPage);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating review status');
    }
}

function viewReview(review) {
    const productName = review.product ? (review.product.ShortDescription || review.product.short_description) : 'Unknown Product';
    const customerName = review.user
        ? `${review.user.first_name} ${review.user.last_name}`
        : (review.reviewer_name || 'Anonymous');

    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('modalCustomerName').textContent = customerName;

    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= review.rating ? '★' : '☆';
    }
    document.getElementById('modalRating').innerHTML = `<span class="text-warning">${stars}</span> (${review.rating}/5)`;

    const status = review.status || 'pending';
    document.getElementById('modalStatus').innerHTML = `<span class="badge bg-${status === 'approved' ? 'success' : (status === 'rejected' ? 'danger' : 'warning')}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
    document.getElementById('modalReviewTitle').textContent = review.review_title || 'No title';
    document.getElementById('modalReviewText').textContent = review.review_text || 'No review text';
    document.getElementById('modalDate').textContent = review.created_at
        ? new Date(review.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
        : 'N/A';
    document.getElementById('modalVerified').innerHTML = review.is_verified_purchase
        ? '<span class="badge bg-info"><i class="bi bi-check-circle"></i> Yes</span>'
        : '<span class="badge bg-secondary">No</span>';

    viewReviewModal.show();
}
</script>
@endpush
