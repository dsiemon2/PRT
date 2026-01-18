@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1>Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Home</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="value">{{ number_format($inventoryStats['total_products'] ?? 0) }}</div>
            <div class="label">Total Products</div>
            <div class="trend">
                <i class="bi bi-box"></i> In catalog
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-cart-check"></i>
            </div>
            <div class="value">{{ number_format($orderStats['total_orders'] ?? 0) }}</div>
            <div class="label">Total Orders</div>
            <div class="trend up">
                <i class="bi bi-arrow-up"></i> All time
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="value">{{ number_format($inventoryStats['low_stock_count'] ?? 0) }}</div>
            <div class="label">Low Stock Items</div>
            <div class="trend {{ ($inventoryStats['out_of_stock_count'] ?? 0) > 0 ? 'down' : '' }}">
                <i class="bi bi-exclamation-circle"></i> {{ $inventoryStats['out_of_stock_count'] ?? 0 }} out of stock
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-people"></i>
            </div>
            <div class="value">{{ number_format($customerStats['total_customers'] ?? 0) }}</div>
            <div class="label">Total Customers</div>
            <div class="trend up">
                <i class="bi bi-person-plus"></i> {{ $customerStats['new_this_month'] ?? 0 }} new this month
            </div>
        </div>
    </div>
</div>

<!-- Revenue Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value">${{ number_format($orderStats['total_revenue'] ?? 0, 2) }}</div>
            <div class="label">Total Revenue</div>
            <div class="trend up">
                <i class="bi bi-currency-dollar"></i> All time
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="value">${{ number_format($orderStats['avg_order_value'] ?? 0, 2) }}</div>
            <div class="label">Avg Order Value</div>
            <div class="trend">
                <i class="bi bi-calculator"></i> Per order
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-truck"></i>
            </div>
            <div class="value">{{ number_format($orderStats['shipped_orders'] ?? 0) }}</div>
            <div class="label">Shipped Orders</div>
            <div class="trend">
                <i class="bi bi-check-circle"></i> Delivered
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon danger">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="value">{{ number_format($orderStats['pending_orders'] ?? 0) }}</div>
            <div class="label">Pending Orders</div>
            <div class="trend {{ ($orderStats['pending_orders'] ?? 0) > 0 ? 'down' : '' }}">
                <i class="bi bi-exclamation-circle"></i> Needs attention
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-container">
            <h5>Revenue Overview</h5>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-container">
            <h5>Sales by Category</h5>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Management Section Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-4 col-md-6">
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-box-seam me-2"></i> Inventory Management
            </div>
            <div class="card-body">
                <div class="card-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <h5 class="card-title">Manage Stock</h5>
                <p class="card-text">Track inventory levels, set reorder points, and manage product stock across all categories.</p>
                <a href="{{ route('admin.inventory') }}" class="btn btn-prt">View Inventory</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-cart3 me-2"></i> Order Management
            </div>
            <div class="card-body">
                <div class="card-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <h5 class="card-title">Process Orders</h5>
                <p class="card-text">View, process, and fulfill customer orders. Track shipments and manage returns.</p>
                <a href="{{ route('admin.orders') }}" class="btn btn-prt">View Orders</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-people me-2"></i> User Management
            </div>
            <div class="card-body">
                <div class="card-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <h5 class="card-title">Manage Users</h5>
                <p class="card-text">Add, edit, and manage user accounts. Assign roles and permissions to staff members.</p>
                <a href="{{ route('admin.users') }}" class="btn btn-prt">Manage Users</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-file-earmark-richtext me-2"></i> Blog Management
            </div>
            <div class="card-body">
                <div class="card-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h5 class="card-title">Create Content</h5>
                <p class="card-text">Write and publish blog posts, manage categories, and schedule content publication.</p>
                <a href="{{ route('admin.blog') }}" class="btn btn-prt">Manage Blog</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-calendar-event me-2"></i> Event Management
            </div>
            <div class="card-body">
                <div class="card-icon">
                    <i class="bi bi-calendar2-week"></i>
                </div>
                <h5 class="card-title">Manage Events</h5>
                <p class="card-text">Create and manage store events, promotions, and special occasions for customers.</p>
                <a href="{{ route('admin.events') }}" class="btn btn-prt">View Events</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="section-card">
            <div class="card-header">
                <i class="bi bi-star me-2"></i> Review Management
            </div>
            <div class="card-body">
                <div class="card-icon">
                    <i class="bi bi-chat-quote"></i>
                </div>
                <h5 class="card-title">Moderate Reviews</h5>
                <p class="card-text">View, approve, and respond to customer reviews. Manage product feedback.</p>
                <a href="{{ route('admin.reviews') }}" class="btn btn-prt">View Reviews</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links & Recent Activity -->
<div class="row g-4">
    <div class="col-lg-4">
        <div class="chart-container">
            <h5>Quick Links</h5>
            <a href="{{ route('admin.inventory.alerts') }}" class="quick-link">
                <i class="bi bi-exclamation-triangle text-warning" style="background: rgba(255,193,7,0.1);"></i>
                <div class="link-text">
                    <div class="title">Low Stock Alerts</div>
                    <div class="subtitle">{{ $inventoryStats['low_stock_count'] ?? 0 }} items need attention</div>
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ route('admin.orders') }}?status=pending" class="quick-link">
                <i class="bi bi-clock text-primary" style="background: rgba(13,110,253,0.1);"></i>
                <div class="link-text">
                    <div class="title">Pending Orders</div>
                    <div class="subtitle">{{ $orderStats['pending_orders'] ?? 0 }} orders to process</div>
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ route('admin.reviews') }}?filter=pending" class="quick-link">
                <i class="bi bi-chat-dots text-info" style="background: rgba(13,202,240,0.1);"></i>
                <div class="link-text">
                    <div class="title">New Reviews</div>
                    <div class="subtitle">8 reviews to moderate</div>
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ route('admin.coupons') }}" class="quick-link">
                <i class="bi bi-ticket-perforated text-success" style="background: rgba(25,135,84,0.1);"></i>
                <div class="link-text">
                    <div class="title">Active Coupons</div>
                    <div class="subtitle">5 promotions running</div>
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ route('admin.reports') }}" class="quick-link">
                <i class="bi bi-graph-up text-danger" style="background: rgba(220,53,69,0.1);"></i>
                <div class="link-text">
                    <div class="title">View Reports</div>
                    <div class="subtitle">Sales & analytics</div>
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="chart-container">
            <h5>Recent Activity</h5>
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba(25,135,84,0.1); color: #198754;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="activity-content">
                    <div class="title">New order #1234 from John Smith</div>
                    <div class="time">2 minutes ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba(13,202,240,0.1); color: #0dcaf0;">
                    <i class="bi bi-star"></i>
                </div>
                <div class="activity-content">
                    <div class="title">New 5-star review on "Handcrafted Leather Belt"</div>
                    <div class="time">15 minutes ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba(255,193,7,0.1); color: #ffc107;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="activity-content">
                    <div class="title">Low stock alert: "Vintage Cowboy Hat" - 3 units left</div>
                    <div class="time">1 hour ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba(13,110,253,0.1); color: #0d6efd;">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="activity-content">
                    <div class="title">New customer registration: maria.garcia@email.com</div>
                    <div class="time">2 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba(111,66,193,0.1); color: #6f42c1;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="activity-content">
                    <div class="title">Blog post "Summer Collection Preview" published</div>
                    <div class="time">3 hours ago</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [12500, 15200, 18400, 16800, 21000, 19500, 23100, 22800, 24580, 0, 0, 0],
                borderColor: '#8B4513',
                backgroundColor: 'rgba(139, 69, 19, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Clothing', 'Accessories', 'Home Decor', 'Jewelry', 'Other'],
            datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                    '#8B4513',
                    '#D2B48C',
                    '#228B22',
                    '#6B3410',
                    '#FFF8DC'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
