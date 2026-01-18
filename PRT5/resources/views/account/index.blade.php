@extends('layouts.app')

@section('title', 'My Account')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">My Account</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Account Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                    <a href="{{ route('account.orders.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam"></i> My Orders
                    </a>
                    <a href="{{ route('account.wishlist.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-heart"></i> Wishlist
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-geo-alt"></i> Addresses
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-gear"></i> Account Settings
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            <h1 style="color: var(--prt-brown);">Welcome, {{ $user->first_name ?? $user->name }}!</h1>
            <p class="text-muted">Manage your account, view orders, and more.</p>

            {{-- Quick Stats --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
                            <h3 class="mt-2">{{ $orderStats['total_orders'] }}</h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                            <h3 class="mt-2">{{ $orderStats['pending_orders'] }}</h3>
                            <p class="text-muted mb-0">Pending Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-heart text-danger" style="font-size: 2rem;"></i>
                            <h3 class="mt-2">{{ $wishlistCount }}</h3>
                            <p class="text-muted mb-0">Wishlist Items</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Recent Orders</h5>
                    <a href="{{ route('account.orders.index') }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View all your orders">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->order_date->format('M j, Y') }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'processing' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="View order details">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">No orders yet.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3" data-bs-toggle="tooltip" title="Browse our product catalog">Start Shopping</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Account Info --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-person"></i> Account Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p class="mb-0"><strong>Member Since:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Update your profile information">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-wallet2"></i> Account Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Spent:</strong> ${{ number_format($orderStats['total_spent'], 2) }}</p>
                            <p><strong>Orders:</strong> {{ $orderStats['total_orders'] }}</p>
                            <p class="mb-0"><strong>Wishlist Items:</strong> {{ $wishlistCount }}</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('account.wishlist.index') }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View your saved items">
                                <i class="bi bi-heart"></i> View Wishlist
                            </a>
                        </div>
                    </div>
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
