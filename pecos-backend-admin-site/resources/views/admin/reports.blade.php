@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="page-header">
    <h1>Reports & Analytics</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select class="form-select">
                    <option>Sales Report</option>
                    <option>Inventory Report</option>
                    <option>Customer Report</option>
                    <option>Product Performance</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-prt me-2">Generate</button>
                <button type="button" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="value">${{ number_format($orderStats['total_revenue'] ?? 0, 2) }}</div>
            <div class="label">Total Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-cart"></i>
            </div>
            <div class="value">{{ number_format($orderStats['total_orders'] ?? 0) }}</div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-person-plus"></i>
            </div>
            <div class="value">{{ number_format($customerStats['new_this_month'] ?? 0) }}</div>
            <div class="label">New Customers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-cart3"></i>
            </div>
            <div class="value">${{ number_format($orderStats['avg_order_value'] ?? 0, 2) }}</div>
            <div class="label">Avg Order Value</div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-container">
            <h5>Sales Trend</h5>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-container">
            <h5>Top Categories</h5>
            <canvas id="categoriesChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Top Products Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Top Selling Products</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Units Sold</th>
                    <th>Revenue</th>
                    <th>Growth</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Handcrafted Leather Belt</td>
                    <td>145</td>
                    <td>$8,698.55</td>
                    <td class="text-success"><i class="bi bi-arrow-up"></i> 24%</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Embroidered Western Shirt</td>
                    <td>98</td>
                    <td>$7,839.02</td>
                    <td class="text-success"><i class="bi bi-arrow-up"></i> 18%</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Vintage Cowboy Hat</td>
                    <td>76</td>
                    <td>$6,839.24</td>
                    <td class="text-success"><i class="bi bi-arrow-up"></i> 15%</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Silver Concho Necklace</td>
                    <td>52</td>
                    <td>$6,759.48</td>
                    <td class="text-danger"><i class="bi bi-arrow-down"></i> 3%</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Rustic Photo Frame</td>
                    <td>89</td>
                    <td>$3,114.11</td>
                    <td class="text-success"><i class="bi bi-arrow-up"></i> 8%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: ['Nov 1', 'Nov 5', 'Nov 10', 'Nov 15', 'Nov 20'],
            datasets: [{
                label: 'Revenue',
                data: [8500, 9200, 11500, 9800, 9250],
                backgroundColor: '#8B4513'
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

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'pie',
        data: {
            labels: ['Clothing', 'Accessories', 'Jewelry', 'Home Decor'],
            datasets: [{
                data: [42, 28, 18, 12],
                backgroundColor: ['#8B4513', '#D2B48C', '#228B22', '#6B3410']
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
