@extends('layouts.admin')

@section('title', 'My Profile')

@php
    $adminUser = session('admin_user', [
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@pecosrivertraders.com',
        'role' => 'administrator',
        'phone' => '',
        'created_at' => now()->subMonths(6)->format('Y-m-d')
    ]);
@endphp

@section('content')
<div class="page-header">
    <h1>My Profile</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; color: white;">
                        {{ strtoupper(substr($adminUser['first_name'] ?? 'A', 0, 1) . substr($adminUser['last_name'] ?? 'D', 0, 1)) }}
                    </div>
                </div>
                <h4>{{ $adminUser['first_name'] ?? 'Admin' }} {{ $adminUser['last_name'] ?? 'User' }}</h4>
                <p class="text-muted mb-1">{{ ucfirst($adminUser['role'] ?? 'Administrator') }}</p>
                <p class="text-muted mb-3"><i class="bi bi-envelope"></i> {{ $adminUser['email'] ?? 'admin@pecosrivertraders.com' }}</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="bi bi-key"></i> Change Password
                    </button>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-activity"></i> Account Activity</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Member Since</span>
                        <strong>{{ \Carbon\Carbon::parse($adminUser['created_at'] ?? now()->subMonths(6))->format('M d, Y') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Last Login</span>
                        <strong>{{ now()->format('M d, Y g:i A') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Role</span>
                        <span class="badge bg-primary">{{ ucfirst($adminUser['role'] ?? 'Administrator') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Status</span>
                        <span class="badge bg-success">Active</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="{{ $adminUser['first_name'] ?? 'Admin' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="{{ $adminUser['last_name'] ?? 'User' }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="{{ $adminUser['email'] ?? 'admin@pecosrivertraders.com' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="{{ $adminUser['phone'] ?? '(555) 123-4567' }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($adminUser['role'] ?? 'Administrator') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" value="Management" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bell"></i> Notification Preferences</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                    <label class="form-check-label" for="emailNotifications">
                        <strong>Email Notifications</strong>
                        <p class="text-muted mb-0 small">Receive email alerts for important updates</p>
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="orderNotifications" checked>
                    <label class="form-check-label" for="orderNotifications">
                        <strong>New Order Alerts</strong>
                        <p class="text-muted mb-0 small">Get notified when new orders are placed</p>
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="stockNotifications" checked>
                    <label class="form-check-label" for="stockNotifications">
                        <strong>Low Stock Alerts</strong>
                        <p class="text-muted mb-0 small">Get notified when products are running low</p>
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="reviewNotifications">
                    <label class="form-check-label" for="reviewNotifications">
                        <strong>New Review Alerts</strong>
                        <p class="text-muted mb-0 small">Get notified when new reviews are submitted</p>
                    </label>
                </div>
                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Preferences
                </button>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                                <strong>Logged in</strong>
                            </div>
                            <small class="text-muted">{{ now()->format('M d, Y g:i A') }}</small>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-pencil text-primary me-2"></i>
                                <strong>Updated product inventory</strong>
                            </div>
                            <small class="text-muted">{{ now()->subHours(2)->format('M d, Y g:i A') }}</small>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-cart-check text-info me-2"></i>
                                <strong>Processed order #1234</strong>
                            </div>
                            <small class="text-muted">{{ now()->subHours(5)->format('M d, Y g:i A') }}</small>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-gear text-secondary me-2"></i>
                                <strong>Updated store settings</strong>
                            </div>
                            <small class="text-muted">{{ now()->subDays(1)->format('M d, Y g:i A') }}</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" value="{{ $adminUser['first_name'] ?? 'Admin' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" value="{{ $adminUser['last_name'] ?? 'User' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $adminUser['email'] ?? 'admin@pecosrivertraders.com' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="{{ $adminUser['phone'] ?? '(555) 123-4567' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <!-- Hidden username field for accessibility -->
                    <input type="text" name="username" value="{{ $adminUser['email'] ?? 'admin@pecosrivertraders.com' }}" autocomplete="username" class="d-none" aria-hidden="true">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" autocomplete="new-password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Update Password</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fix aria-hidden focus issue on modals
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hide.bs.modal', function () {
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });
});
</script>
@endpush
@endsection
