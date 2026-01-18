@extends('layouts.admin')

@section('title', 'Sales Dashboard')

@section('content')
<div class="page-header">
    <h1>Sales Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sales Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-center">
            <div class="col-auto">
                <label class="form-label mb-0">Date Range:</label>
            </div>
            <div class="col-auto">
                <select class="form-select">
                    <option>Today</option>
                    <option selected>This Week</option>
                    <option>This Month</option>
                    <option>This Quarter</option>
                    <option>This Year</option>
                    <option>Custom Range</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
            </div>
            <div class="col-auto">
                <span>to</span>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-prt">Apply</button>
            </div>
        </form>
    </div>
</div>

<!-- Main Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value">${{ number_format($orderStats['total_revenue'] ?? 0, 2) }}</div>
            <div class="label">Total Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-bag-check"></i>
            </div>
            <div class="value">{{ number_format($orderStats['total_orders'] ?? 0) }}</div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-cart3"></i>
            </div>
            <div class="value">${{ number_format($orderStats['avg_order_value'] ?? $orderStats['average_order_value'] ?? 0, 2) }}</div>
            <div class="label">Avg Order Value</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-people"></i>
            </div>
            <div class="value">{{ number_format($customerStats['total_customers'] ?? 0) }}</div>
            <div class="label">Total Customers</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="chart-container">
            <h5>Revenue Over Time</h5>
            <canvas id="revenueChart" height="300"></canvas>
        </div>
    </div>

    <!-- Sales by Category -->
    <div class="col-lg-4">
        <div class="chart-container">
            <h5>Sales by Category</h5>
            <canvas id="categoryChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Top Products -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Units</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Handcrafted Leather Belt</td>
                            <td>45</td>
                            <td>$2,249.55</td>
                        </tr>
                        <tr>
                            <td>Vintage Cowboy Hat</td>
                            <td>32</td>
                            <td>$2,879.68</td>
                        </tr>
                        <tr>
                            <td>Silver Concho Necklace</td>
                            <td>28</td>
                            <td>$2,239.72</td>
                        </tr>
                        <tr>
                            <td>Embroidered Western Shirt</td>
                            <td>25</td>
                            <td>$1,624.75</td>
                        </tr>
                        <tr>
                            <td>Leather Wallet</td>
                            <td>23</td>
                            <td>$689.77</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sales by Region -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sales by Region</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>% of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Texas</td>
                            <td>42</td>
                            <td>$3,607.80</td>
                            <td>29%</td>
                        </tr>
                        <tr>
                            <td>California</td>
                            <td>28</td>
                            <td>$2,404.80</td>
                            <td>19%</td>
                        </tr>
                        <tr>
                            <td>Pennsylvania</td>
                            <td>23</td>
                            <td>$1,975.68</td>
                            <td>16%</td>
                        </tr>
                        <tr>
                            <td>Arizona</td>
                            <td>18</td>
                            <td>$1,546.08</td>
                            <td>12%</td>
                        </tr>
                        <tr>
                            <td>Other</td>
                            <td>34</td>
                            <td>$2,921.64</td>
                            <td>24%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Additional Metrics -->
<div class="row g-4 mt-2">
    <!-- Payment Methods -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Payment Methods</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Credit Card</span>
                    <span class="fw-bold">78%</span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: 78%"></div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>PayPal</span>
                    <span class="fw-bold">15%</span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-info" style="width: 15%"></div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Gift Cards</span>
                    <span class="fw-bold">7%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: 7%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coupon Usage -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Coupon Usage</h5>
            </div>
            <div class="card-body">
                <p><strong>Total Discounts:</strong> $876.45</p>
                <p><strong>Orders with Coupons:</strong> 38 (26%)</p>
                <hr>
                <table class="table table-sm">
                    <tr>
                        <td>SAVE10</td>
                        <td>23 uses</td>
                        <td>$456.78</td>
                    </tr>
                    <tr>
                        <td>WELCOME15</td>
                        <td>12 uses</td>
                        <td>$345.67</td>
                    </tr>
                    <tr>
                        <td>FREESHIP</td>
                        <td>3 uses</td>
                        <td>$74.00</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Refunds -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Returns & Refunds</h5>
            </div>
            <div class="card-body">
                <p><strong>Total Refunds:</strong> $234.56</p>
                <p><strong>Refund Rate:</strong> 1.9%</p>
                <p><strong>Orders Refunded:</strong> 3</p>
                <hr>
                <p class="text-muted small mb-0">Top reason: Wrong size (67%)</p>
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
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Revenue',
            data: [1456, 1890, 1567, 2345, 1987, 2456, 1755],
            borderColor: '#8B4513',
            backgroundColor: 'rgba(139, 69, 19, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '$' + value
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
        labels: ['Clothing', 'Accessories', 'Jewelry', 'Home Decor'],
        datasets: [{
            data: [35, 30, 25, 10],
            backgroundColor: ['#8B4513', '#D2B48C', '#228B22', '#B8860B']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endpush
