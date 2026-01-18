@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="page-header">
    <h1>User Management</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-people"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="value" id="stat-active">-</div>
            <div class="label">Active Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon warning">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="value" id="stat-admin">-</div>
            <div class="label">Admin Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-person-plus"></i>
            </div>
            <div class="value" id="stat-new">-</div>
            <div class="label">New This Month</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchFilter" placeholder="Search users...">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="staff">Staff</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="activeFilter">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-prt w-100" onclick="filterUsers()">Filter</button>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus"></i> Add User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Orders</th>
                <th>Joined</th>
                <th>Last Login</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTable">
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading users...
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" class="admin-form">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" id="addFirstName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="addLastName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="addEmail" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="addPassword" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="addRole" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="createUser()">Create User</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" class="admin-form">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" id="editFirstName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="editLastName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="editRole" required>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editIsActive">
                            <label class="form-check-label" for="editIsActive">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="updateUser()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteUserId">
                <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteUser()">Delete</button>
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
.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: var(--prt-brown, #8B4513);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: bold;
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

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadUsers();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/users/stats`);
        const data = await response.json();

        if (data.success || data.data) {
            const stats = data.data || data;
            document.getElementById('stat-total').textContent = stats.total_users || 0;
            document.getElementById('stat-active').textContent = stats.active_users || 0;
            document.getElementById('stat-admin').textContent = stats.admin_users || 0;
            document.getElementById('stat-new').textContent = stats.new_this_month || 0;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadUsers(page = 1) {
    currentPage = page;

    try {
        const search = document.getElementById('searchFilter').value;
        const role = document.getElementById('roleFilter').value;
        const active = document.getElementById('activeFilter').value;

        let url = `${API_BASE}/admin/users?page=${page}&per_page=${perPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (role) url += `&role=${role}`;
        if (active !== '') url += `&active=${active}`;

        const response = await fetch(url);
        const data = await response.json();

        const users = data.data || [];
        const meta = {
            current_page: data.current_page || data.meta?.current_page || 1,
            last_page: data.last_page || data.meta?.last_page || 1,
            from: data.from || data.meta?.from || 1,
            to: data.to || data.meta?.to || users.length,
            total: data.total || data.meta?.total || users.length,
            per_page: data.per_page || data.meta?.per_page || perPage
        };

        renderUsers(users);
        renderPagination(meta);
    } catch (error) {
        console.error('Error loading users:', error);
        document.getElementById('usersTable').innerHTML =
            '<tr><td colspan="8" class="text-center text-danger">Error loading users</td></tr>';
    }
}

function renderUsers(users) {
    const tbody = document.getElementById('usersTable');

    if (users.length === 0) {
        tbody.innerHTML = `<tr>
            <td colspan="8" class="text-center py-4 text-muted">No users found</td>
        </tr>`;
        return;
    }

    let html = '';
    users.forEach(user => {
        const initials = (user.first_name?.charAt(0) || '') + (user.last_name?.charAt(0) || '') || '?';
        const fullName = ((user.first_name || '') + ' ' + (user.last_name || '')).trim() || 'Unknown';
        const isActive = user.is_active || false;
        const joinDate = user.created_at ? new Date(user.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'N/A';
        const lastLogin = user.last_login ? new Date(user.last_login).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'Never';
        const role = user.role || 'customer';

        let roleClass = 'secondary';
        if (role === 'admin') roleClass = 'danger';
        else if (role === 'manager') roleClass = 'warning';

        html += `<tr onclick="highlightRow(event)" style="cursor: pointer;">`;
        html += `<td>
            <div class="d-flex align-items-center">
                <div class="user-avatar me-2">${initials.toUpperCase()}</div>
                <div>${fullName}</div>
            </div>
        </td>`;
        html += `<td>${user.email || 'N/A'}</td>`;
        html += `<td><span class="badge bg-${roleClass}">${role.charAt(0).toUpperCase() + role.slice(1)}</span></td>`;
        html += `<td>${user.orders_count || 0}</td>`;
        html += `<td>${joinDate}</td>`;
        html += `<td>${lastLogin}</td>`;
        html += `<td><span class="status-badge ${isActive ? 'active' : 'inactive'}">${isActive ? 'Active' : 'Inactive'}</span></td>`;
        html += `<td>
            <button class="btn btn-sm btn-outline-primary" onclick="editUser(${JSON.stringify(user).replace(/"/g, '&quot;')})"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id}, '${fullName.replace(/'/g, "\\'")}')"><i class="bi bi-trash"></i></button>
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
    html += `<a class="page-link" href="#" onclick="loadUsers(${meta.current_page - 1}); return false;">Previous</a>`;
    html += `</li>`;

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, meta.current_page + 2);

    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadUsers(1); return false;">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="loadUsers(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadUsers(${meta.last_page}); return false;">${meta.last_page}</a></li>`;
    }

    html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="loadUsers(${meta.current_page + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function filterUsers() {
    loadUsers(1);
}

function editUser(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editFirstName').value = user.first_name || '';
    document.getElementById('editLastName').value = user.last_name || '';
    document.getElementById('editEmail').value = user.email || '';
    document.getElementById('editRole').value = user.role || 'customer';
    document.getElementById('editIsActive').checked = user.is_active || false;

    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function deleteUser(id, name) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserName').textContent = name;

    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}

async function createUser() {
    const data = {
        first_name: document.getElementById('addFirstName').value,
        last_name: document.getElementById('addLastName').value,
        email: document.getElementById('addEmail').value,
        password: document.getElementById('addPassword').value,
        role: document.getElementById('addRole').value
    };

    try {
        const response = await fetch(API_BASE + '/admin/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
            loadUsers();
            loadStats();
        } else {
            alert(result.message || 'Error creating user');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error creating user');
    }
}

async function updateUser() {
    const id = document.getElementById('editUserId').value;
    const data = {
        first_name: document.getElementById('editFirstName').value,
        last_name: document.getElementById('editLastName').value,
        email: document.getElementById('editEmail').value,
        role: document.getElementById('editRole').value,
        is_active: document.getElementById('editIsActive').checked
    };

    try {
        const response = await fetch(API_BASE + '/admin/users/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            loadUsers();
            loadStats();
        } else {
            alert(result.message || 'Error updating user');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating user');
    }
}

async function confirmDeleteUser() {
    const id = document.getElementById('deleteUserId').value;

    try {
        const response = await fetch(API_BASE + '/admin/users/' + id, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
            loadUsers();
            loadStats();
        } else {
            alert(result.message || 'Error deleting user');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting user');
    }
}
</script>
@endpush
