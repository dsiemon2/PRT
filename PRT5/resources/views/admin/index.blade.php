@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    .admin-card {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid var(--prt-red);
    }
    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .admin-card-icon {
        font-size: 3rem;
        color: var(--prt-red);
        margin-bottom: 15px;
    }
    .admin-card h3 {
        color: var(--prt-brown);
        margin-bottom: 15px;
    }
    .admin-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .admin-card-link:hover {
        color: inherit;
    }
    .category-section {
        margin-bottom: 40px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
            <p class="lead text-muted">Welcome, {{ auth()->user()->first_name ?? 'Admin' }}! Manage your store from here.</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Admin Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Inventory Management Section -->
    <div class="category-section">
        <h2 class="mb-4" style="color: var(--prt-brown);">
            <i class="bi bi-box-seam"></i> Inventory Management
        </h2>
        <div class="row">
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.index') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="View all products with stock levels, search and filter">
                    <div class="admin-card">
                        <i class="bi bi-grid-3x3-gap admin-card-icon"></i>
                        <h3>Inventory Dashboard</h3>
                        <p class="text-muted">View all products, stock levels, search and filter inventory.</p>
                        <span class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Open Inventory Dashboard">
                            <i class="bi bi-arrow-right"></i> Open Dashboard
                        </span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.stock-alerts.index') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="View and manage low stock and out of stock alerts">
                    <div class="admin-card">
                        <i class="bi bi-bell admin-card-icon"></i>
                        <h3>Stock Alerts</h3>
                        <p class="text-muted">Manage low stock and out of stock alerts.</p>
                        <span class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="View Stock Alerts">
                            <i class="bi bi-arrow-right"></i> View Alerts
                        </span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.reports') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate inventory valuation, stock status, and movement reports">
                    <div class="admin-card">
                        <i class="bi bi-graph-up admin-card-icon"></i>
                        <h3>Inventory Reports</h3>
                        <p class="text-muted">Generate valuation, stock status, movement, and reorder reports.</p>
                        <span class="btn btn-outline-info btn-sm" data-bs-toggle="tooltip" title="View Inventory Reports">
                            <i class="bi bi-arrow-right"></i> View Reports
                        </span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.inventory.bulk-update') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload CSV or select multiple products for bulk stock adjustments">
                    <div class="admin-card">
                        <i class="bi bi-box-arrow-in-down admin-card-icon"></i>
                        <h3>Bulk Stock Update</h3>
                        <p class="text-muted">Upload CSV or select multiple products for bulk adjustments.</p>
                        <span class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" title="Perform Bulk Stock Update">
                            <i class="bi bi-arrow-right"></i> Bulk Update
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Engagement Section -->
    <div class="category-section">
        <h2 class="mb-4" style="color: var(--prt-brown);">
            <i class="bi bi-chat-heart"></i> Customer Engagement
        </h2>
        <div class="row">
            <div class="col-md-4">
                <a href="{{ route('admin.reviews.index') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage product reviews, approve or reject customer feedback">
                    <div class="admin-card">
                        <i class="bi bi-star admin-card-icon"></i>
                        <h3>Customer Reviews</h3>
                        <p class="text-muted">Manage product reviews, ratings, and customer feedback.</p>
                        <span class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Manage Customer Reviews">
                            <i class="bi bi-arrow-right"></i> Manage Reviews
                        </span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.messages.index') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="View and respond to customer contact messages">
                    <div class="admin-card">
                        <i class="bi bi-envelope admin-card-icon"></i>
                        <h3>Messages</h3>
                        <p class="text-muted">View and manage customer contact messages and inquiries.</p>
                        <span class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="View All Messages">
                            <i class="bi bi-arrow-right"></i> View Messages
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Content Management Section -->
    <div class="category-section">
        <h2 class="mb-4" style="color: var(--prt-brown);">
            <i class="bi bi-file-earmark-text"></i> Content Management
        </h2>
        <div class="row">
            <div class="col-md-4">
                <a href="{{ route('admin.blog.index') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Create, edit, and publish blog posts and articles">
                    <div class="admin-card">
                        <i class="bi bi-journal-text admin-card-icon"></i>
                        <h3>Blog Management</h3>
                        <p class="text-muted">Create, edit, and manage blog posts and articles.</p>
                        <span class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Manage Blog Posts">
                            <i class="bi bi-arrow-right"></i> Manage Blog
                        </span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.faq.statistics') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="View FAQ performance, helpful ratings, and engagement metrics">
                    <div class="admin-card">
                        <i class="bi bi-question-circle admin-card-icon"></i>
                        <h3>FAQ Statistics</h3>
                        <p class="text-muted">View FAQ performance, helpful ratings, and engagement metrics.</p>
                        <span class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="View FAQ Statistics">
                            <i class="bi bi-arrow-right"></i> View Stats
                        </span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.events.index') }}" class="admin-card-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Add, edit, and manage store events and calendar">
                    <div class="admin-card">
                        <i class="bi bi-calendar-event admin-card-icon"></i>
                        <h3>Events Management</h3>
                        <p class="text-muted">Add, edit, and manage store events and calendar.</p>
                        <span class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Manage Store Events">
                            <i class="bi bi-arrow-right"></i> Manage Events
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="category-section">
        <h2 class="mb-4" style="color: var(--prt-brown);">
            <i class="bi bi-lightning"></i> Quick Links
        </h2>
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <div class="list-group-item list-group-item-action disabled">
                        <strong>Inventory Actions</strong>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View complete product inventory list">
                        <i class="bi bi-list-ul"></i> View All Products
                    </a>
                    <a href="{{ route('admin.inventory.bulk-update') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="Upload CSV file to update multiple products">
                        <i class="bi bi-upload"></i> Bulk Upload CSV
                    </a>
                    <a href="{{ route('admin.inventory.export') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="Download inventory data as CSV file">
                        <i class="bi bi-download"></i> Export to CSV
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <div class="list-group-item list-group-item-action disabled">
                        <strong>Reports</strong>
                    </div>
                    <a href="{{ route('admin.inventory.reports', ['report' => 'valuation']) }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View total inventory value at cost">
                        <i class="bi bi-currency-dollar"></i> Inventory Valuation
                    </a>
                    <a href="{{ route('admin.inventory.reports', ['report' => 'stock_status']) }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View stock status breakdown by category">
                        <i class="bi bi-box-seam"></i> Stock Status
                    </a>
                    <a href="{{ route('admin.inventory.reports', ['report' => 'low_stock']) }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View products below reorder threshold">
                        <i class="bi bi-exclamation-triangle"></i> Low Stock Report
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <div class="list-group-item list-group-item-action disabled">
                        <strong>Content</strong>
                    </div>
                    <a href="{{ route('faq') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View public FAQ page">
                        <i class="bi bi-eye"></i> View FAQ (Public)
                    </a>
                    <a href="{{ route('faq') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View FAQ engagement analytics">
                        <i class="bi bi-graph-up"></i> FAQ Analytics
                    </a>
                    <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View public events calendar">
                        <i class="bi bi-eye"></i> View Events (Public)
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <div class="list-group-item list-group-item-action disabled">
                        <strong>Store Front</strong>
                    </div>
                    <a href="{{ route('home') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View store homepage as customers see it">
                        <i class="bi bi-house-door"></i> View Homepage
                    </a>
                    <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View product catalog as customers see it">
                        <i class="bi bi-grid"></i> View Products
                    </a>
                    <a href="{{ route('cart.index') }}" class="list-group-item list-group-item-action" data-bs-toggle="tooltip" data-bs-placement="right" title="View shopping cart page">
                        <i class="bi bi-cart"></i> View Cart
                    </a>
                </div>
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
