@extends('layouts.admin')

@section('title', 'API Logs')

@section('content')
<div class="page-header">
    <h1>API Logs</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dropshippers') }}">Drop Shippers</a></li>
            <li class="breadcrumb-item active">API Logs</li>
        </ol>
    </nav>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon primary">
                <i class="bi bi-activity"></i>
            </div>
            <div class="value" id="stat-total">-</div>
            <div class="label">Total Requests (24h)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value" id="stat-success">-</div>
            <div class="label">Success Rate</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon info">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div class="value" id="stat-response">-</div>
            <div class="label">Avg Response</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="value" id="stat-errors">-</div>
            <div class="label">Errors (24h)</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" onsubmit="return filterLogs(event)">
            <div class="col-md-2">
                <select class="form-select" id="dropshipperFilter">
                    <option value="">All Shippers</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="endpointFilter">
                    <option value="">All Endpoints</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="200">200 OK</option>
                    <option value="201">201 Created</option>
                    <option value="400">400 Bad Request</option>
                    <option value="401">401 Unauthorized</option>
                    <option value="404">404 Not Found</option>
                    <option value="429">429 Rate Limited</option>
                    <option value="500">500 Server Error</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" id="dateFilter">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-prt w-100" data-bs-toggle="tooltip" title="Apply Filters">Filter</button>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="exportLogs()" data-bs-toggle="tooltip" title="Export Logs">
                    <i class="bi bi-download"></i>
                </button>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger w-100" onclick="clearAllLogs()" data-bs-toggle="tooltip" title="Clear All Logs">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="admin-table">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th>Time</th>
                <th>Drop Shipper</th>
                <th>Method</th>
                <th>Endpoint</th>
                <th>Status</th>
                <th>Response Time</th>
                <th>IP / Country</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="logsTable">
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading logs...
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

<!-- Log Detail Modal -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailContent">
                Loading...
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
let perPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, calling loadLogs');
    initTooltips();
    loadStats();
    loadFilters();
    loadLogs();
});

function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
}

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/api-logs/stats`);
        const data = await response.json();

        if (data.success) {
            document.getElementById('stat-total').textContent = data.data.total_requests.toLocaleString();
            document.getElementById('stat-success').textContent = data.data.success_rate + '%';
            document.getElementById('stat-response').textContent = data.data.avg_response_time + 'ms';
            document.getElementById('stat-errors').textContent = data.data.errors.toLocaleString();
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadFilters() {
    try {
        // Load dropshippers
        const dsResponse = await fetch(`${API_BASE}/admin/api-logs/dropshippers`);
        const dsData = await dsResponse.json();

        if (dsData.success) {
            const select = document.getElementById('dropshipperFilter');
            dsData.data.forEach(ds => {
                const option = document.createElement('option');
                option.value = ds.id;
                option.textContent = ds.company_name;
                select.appendChild(option);
            });
        }

        // Load endpoints
        const epResponse = await fetch(`${API_BASE}/admin/api-logs/endpoints`);
        const epData = await epResponse.json();

        if (epData.success) {
            const select = document.getElementById('endpointFilter');
            epData.data.forEach(ep => {
                const option = document.createElement('option');
                option.value = ep;
                option.textContent = ep;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading filters:', error);
    }
}

async function loadLogs(page = 1) {
    currentPage = page;

    try {
        const dropshipperId = document.getElementById('dropshipperFilter').value;
        const endpoint = document.getElementById('endpointFilter').value;
        const statusCode = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;

        let url = API_BASE + '/admin/api-logs?page=' + page + '&per_page=' + perPage;
        if (dropshipperId) url += '&dropshipper_id=' + dropshipperId;
        if (endpoint) url += '&endpoint=' + encodeURIComponent(endpoint);
        if (statusCode) url += '&status_code=' + statusCode;
        if (date) url += '&date=' + date;

        console.log('Fetching:', url);
        const response = await fetch(url);
        const data = await response.json();
        console.log('Response:', data);

        if (data.success) {
            renderLogs(data.data);
            renderPagination(data.meta);
        }
    } catch (error) {
        console.error('Error loading logs:', error);
        document.getElementById('logsTable').innerHTML =
            '<tr><td colspan="8" class="text-center text-danger">Error loading logs: ' + error.message + '</td></tr>';
    }
}

function renderLogs(logs) {
    const tbody = document.getElementById('logsTable');

    if (logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No logs found</td></tr>';
        return;
    }

    let html = '';
    logs.forEach(function(log) {
        const date = new Date(log.created_at);
        const formattedTime = date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });

        let methodClass = 'secondary';
        if (log.method === 'GET') methodClass = 'primary';
        else if (log.method === 'POST') methodClass = 'success';
        else if (log.method === 'PUT') methodClass = 'warning';

        let statusClass = 'success';
        if (log.status_code >= 500) statusClass = 'danger';
        else if (log.status_code >= 400) statusClass = 'warning';
        else if (log.status_code >= 300) statusClass = 'info';

        let rowClass = '';
        if (log.status_code >= 500) rowClass = 'table-danger';
        else if (log.status_code >= 400) rowClass = 'table-warning';

        html += '<tr class="' + rowClass + '" onclick="highlightRow(event)" style="cursor: pointer;">';
        html += '<td><small>' + formattedTime + '</small></td>';
        html += '<td>' + (log.dropshipper_name || 'Unknown') + '</td>';
        html += '<td><span class="badge bg-' + methodClass + '">' + log.method + '</span></td>';
        html += '<td><code>' + log.endpoint + '</code></td>';
        html += '<td><span class="badge bg-' + statusClass + '">' + log.status_code + '</span></td>';
        html += '<td>' + log.response_time + 'ms</td>';
        html += '<td>' + (log.ip_address || '-') + (log.country ? ' <span class="badge bg-secondary">' + log.country + '</span>' : '') + '</td>';
        html += '<td><button class="btn btn-sm btn-outline-primary" onclick="viewLogDetail(' + log.id + ')" data-bs-toggle="tooltip" title="View Details"><i class="bi bi-eye"></i></button></td>';
        html += '</tr>';
    });

    tbody.innerHTML = html;
    initTooltips();
}

function renderPagination(meta) {
    // Update info
    document.getElementById('paginationInfo').textContent =
        'Showing ' + meta.from + ' to ' + meta.to + ' of ' + meta.total + ' entries';

    // Build pagination
    const pagination = document.getElementById('pagination');
    let html = '';

    // Previous button
    html += '<li class="page-item ' + (meta.current_page === 1 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="loadLogs(' + (meta.current_page - 1) + '); return false;">Previous</a>';
    html += '</li>';

    // Page numbers
    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.total_pages, meta.current_page + 2);

    if (startPage > 1) {
        html += '<li class="page-item"><a class="page-link" href="#" onclick="loadLogs(1); return false;">1</a></li>';
        if (startPage > 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += '<li class="page-item ' + (i === meta.current_page ? 'active' : '') + '">';
        html += '<a class="page-link" href="#" onclick="loadLogs(' + i + '); return false;">' + i + '</a>';
        html += '</li>';
    }

    if (endPage < meta.total_pages) {
        if (endPage < meta.total_pages - 1) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        html += '<li class="page-item"><a class="page-link" href="#" onclick="loadLogs(' + meta.total_pages + '); return false;">' + meta.total_pages + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (meta.current_page === meta.total_pages ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="loadLogs(' + (meta.current_page + 1) + '); return false;">Next</a>';
    html += '</li>';

    pagination.innerHTML = html;
}

function filterLogs(event) {
    event.preventDefault();
    loadLogs(1);
    return false;
}

async function viewLogDetail(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/api-logs/${id}`);
        const data = await response.json();

        if (data.success) {
            const log = data.data;
            const date = new Date(log.created_at);
            const formattedTime = date.toLocaleString();

            document.getElementById('logDetailContent').innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Request Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Time:</strong></td><td>${formattedTime}</td></tr>
                            <tr><td><strong>Method:</strong></td><td><span class="badge bg-primary">${log.method}</span></td></tr>
                            <tr><td><strong>Endpoint:</strong></td><td><code>${log.endpoint}</code></td></tr>
                            <tr><td><strong>IP Address:</strong></td><td>${log.ip_address || '-'}</td></tr>
                            <tr><td><strong>Country:</strong></td><td>${log.country || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Response Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Status Code:</strong></td><td><span class="badge bg-${log.status_code < 300 ? 'success' : log.status_code < 400 ? 'info' : log.status_code < 500 ? 'warning' : 'danger'}">${log.status_code}</span></td></tr>
                            <tr><td><strong>Response Time:</strong></td><td>${log.response_time}ms</td></tr>
                            <tr><td><strong>Drop Shipper:</strong></td><td>${log.dropshipper_name || 'Unknown'}</td></tr>
                            ${log.dropshipper_email ? `<tr><td><strong>Email:</strong></td><td>${log.dropshipper_email}</td></tr>` : ''}
                        </table>
                    </div>
                </div>
                ${log.error_message ? `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${log.error_message}
                    </div>
                ` : ''}
                ${log.request_body ? `
                    <h6>Request Body</h6>
                    <pre class="bg-light p-2 rounded"><code>${log.request_body}</code></pre>
                ` : ''}
                ${log.response_body ? `
                    <h6>Response Body</h6>
                    <pre class="bg-light p-2 rounded"><code>${log.response_body}</code></pre>
                ` : ''}
            `;

            new bootstrap.Modal(document.getElementById('logDetailModal')).show();
        }
    } catch (error) {
        console.error('Error loading log details:', error);
        alert('Error loading log details');
    }
}

function exportLogs() {
    alert('Export functionality coming soon');
}

async function clearAllLogs() {
    if (!confirm('Are you sure you want to clear ALL API logs? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/api-logs/clear`, {
            method: 'DELETE'
        });
        const data = await response.json();

        if (data.success) {
            alert(data.message);
            loadStats();
            loadLogs(1);
        } else {
            alert('Error: ' + (data.message || 'Failed to clear logs'));
        }
    } catch (error) {
        console.error('Error clearing logs:', error);
        alert('Error clearing logs: ' + error.message);
    }
}
</script>
@endpush
