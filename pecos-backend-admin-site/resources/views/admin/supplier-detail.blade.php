@extends('layouts.admin')

@section('title', 'Supplier Details')

@section('content')
<div class="page-header">
    <h1>Western Supply Co.</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.Suppliers') }}">Suppliers</a></li>
            <li class="breadcrumb-item active">Western Supply Co.</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <!-- Main Info -->
    <div class="col-lg-8">
        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="value">456</div>
                    <div class="label">Total Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="value">$18,234</div>
                    <div class="label">Revenue</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="value">$911</div>
                    <div class="label">Commission</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="value">98.5%</div>
                    <div class="label">Success Rate</div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('admin.supplier.orders') }}?shipper=1" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DS-2024-0456</td>
                            <td>Nov 20, 2024</td>
                            <td>3</td>
                            <td>$89.97</td>
                            <td><span class="status-badge active">Shipped</span></td>
                        </tr>
                        <tr>
                            <td>DS-2024-0455</td>
                            <td>Nov 19, 2024</td>
                            <td>1</td>
                            <td>$34.99</td>
                            <td><span class="status-badge active">Delivered</span></td>
                        </tr>
                        <tr>
                            <td>DS-2024-0454</td>
                            <td>Nov 18, 2024</td>
                            <td>5</td>
                            <td>$156.45</td>
                            <td><span class="status-badge active">Delivered</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- API Activity -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">API Activity (Last 7 Days)</h5>
                <a href="{{ route('admin.api.logs') }}?shipper=1" class="btn btn-sm btn-outline-primary">View Logs</a>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Endpoint</th>
                            <th>Requests</th>
                            <th>Errors</th>
                            <th>Avg Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>GET /products</code></td>
                            <td>1,234</td>
                            <td>0</td>
                            <td>45ms</td>
                        </tr>
                        <tr>
                            <td><code>GET /inventory</code></td>
                            <td>856</td>
                            <td>2</td>
                            <td>32ms</td>
                        </tr>
                        <tr>
                            <td><code>POST /orders</code></td>
                            <td>45</td>
                            <td>1</td>
                            <td>156ms</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status & Actions -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Status & Actions</h5>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="status-badge active">Active</span></p>
                <p><strong>Member Since:</strong> Jan 15, 2024</p>
                <p><strong>Last Activity:</strong> 2 hours ago</p>
                <hr>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Edit Details</button>
                    <button class="btn btn-outline-warning"><i class="bi bi-pause-circle"></i> Suspend Account</button>
                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete Account</button>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <p><i class="bi bi-person me-2"></i> John Smith</p>
                <p><i class="bi bi-envelope me-2"></i> john@westernsupply.com</p>
                <p><i class="bi bi-telephone me-2"></i> 555-0123</p>
                <p><i class="bi bi-globe me-2"></i> westernsupply.com</p>
            </div>
        </div>

        <!-- API Credentials -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">API Credentials</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">API Key</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-sm" value="ws_live_1234567890abcdef" readonly>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">API Secret</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-sm" value="************************" readonly>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-arrow-repeat"></i> Regenerate Keys</button>
            </div>
        </div>

        <!-- Commission Settings -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Commission & Pricing</h5>
            </div>
            <div class="card-body">
                <p><strong>Commission Rate:</strong> 5%</p>
                <p><strong>Markup:</strong> 0%</p>
                <p><strong>Pricing Tier:</strong> Wholesale</p>
                <p><strong>Rate Limit:</strong> 1,000/hr</p>
            </div>
        </div>
    </div>
</div>
@endsection
