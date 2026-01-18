@extends('layouts.admin')

@section('title', 'FAQ Statistics')

@section('content')
<div class="page-header">
    <h1>FAQ Statistics</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">FAQ Statistics</li>
        </ol>
    </nav>
</div>

<!-- Stats Overview -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-question-circle"></i>
            </div>
            <div class="value">45</div>
            <div class="label">Total FAQs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-eye"></i>
            </div>
            <div class="value">12,456</div>
            <div class="label">Total Views</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-hand-thumbs-up"></i>
            </div>
            <div class="value">89%</div>
            <div class="label">Helpful Rate</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-tags"></i>
            </div>
            <div class="value">8</div>
            <div class="label">Categories</div>
        </div>
    </div>
</div>

<!-- FAQ Performance Table -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">FAQ Performance</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Category</th>
                    <th>Views</th>
                    <th>Helpful</th>
                    <th>Not Helpful</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>What are your shipping options?</td>
                    <td>Shipping</td>
                    <td>2,345</td>
                    <td><span class="text-success">456</span></td>
                    <td><span class="text-danger">23</span></td>
                    <td><span class="badge bg-success">95%</span></td>
                </tr>
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>How do I track my order?</td>
                    <td>Orders</td>
                    <td>1,892</td>
                    <td><span class="text-success">389</span></td>
                    <td><span class="text-danger">34</span></td>
                    <td><span class="badge bg-success">92%</span></td>
                </tr>
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>What is your return policy?</td>
                    <td>Returns</td>
                    <td>1,567</td>
                    <td><span class="text-success">298</span></td>
                    <td><span class="text-danger">45</span></td>
                    <td><span class="badge bg-success">87%</span></td>
                </tr>
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>Do you offer gift wrapping?</td>
                    <td>Services</td>
                    <td>987</td>
                    <td><span class="text-success">145</span></td>
                    <td><span class="text-danger">12</span></td>
                    <td><span class="badge bg-success">92%</span></td>
                </tr>
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>How do I use a coupon code?</td>
                    <td>Payment</td>
                    <td>876</td>
                    <td><span class="text-success">134</span></td>
                    <td><span class="text-danger">56</span></td>
                    <td><span class="badge bg-warning text-dark">71%</span></td>
                </tr>
                <tr onclick="highlightRow(event)" style="cursor: pointer;">
                    <td>Are your products authentic?</td>
                    <td>Products</td>
                    <td>654</td>
                    <td><span class="text-success">178</span></td>
                    <td><span class="text-danger">8</span></td>
                    <td><span class="badge bg-success">96%</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Breakdown -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="chart-container">
            <h5>Views by Category</h5>
            <canvas id="categoryViewsChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Category Performance</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>FAQs</th>
                            <th>Views</th>
                            <th>Avg Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr onclick="highlightRow(event)" style="cursor: pointer;">
                            <td>Shipping</td>
                            <td>8</td>
                            <td>4,234</td>
                            <td>93%</td>
                        </tr>
                        <tr onclick="highlightRow(event)" style="cursor: pointer;">
                            <td>Orders</td>
                            <td>7</td>
                            <td>3,456</td>
                            <td>91%</td>
                        </tr>
                        <tr onclick="highlightRow(event)" style="cursor: pointer;">
                            <td>Returns</td>
                            <td>6</td>
                            <td>2,876</td>
                            <td>88%</td>
                        </tr>
                        <tr onclick="highlightRow(event)" style="cursor: pointer;">
                            <td>Payment</td>
                            <td>5</td>
                            <td>1,234</td>
                            <td>85%</td>
                        </tr>
                        <tr onclick="highlightRow(event)" style="cursor: pointer;">
                            <td>Products</td>
                            <td>10</td>
                            <td>2,345</td>
                            <td>94%</td>
                        </tr>
                    </tbody>
                </table>
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
    const ctx = document.getElementById('categoryViewsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Shipping', 'Orders', 'Returns', 'Payment', 'Products', 'Services', 'Account', 'Other'],
            datasets: [{
                label: 'Views',
                data: [4234, 3456, 2876, 1234, 2345, 987, 654, 432],
                backgroundColor: '#8B4513'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
