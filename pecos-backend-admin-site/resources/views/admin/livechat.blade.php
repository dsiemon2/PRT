@extends('layouts.admin')

@section('title', 'Live Chat')
@section('page-title', 'Live Chat Management')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="card-title">Waiting Chats</h6>
                    <h2 id="waiting-chats">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Active Chats</h6>
                    <h2 id="active-chats">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Agents Online</h6>
                    <h2 id="online-agents">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Offline Messages</h6>
                    <h2 id="offline-messages">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="chatTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="agents-tab" data-bs-toggle="tab" href="#agents" role="tab">Agents</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="departments-tab" data-bs-toggle="tab" href="#departments" role="tab">Departments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="responses-tab" data-bs-toggle="tab" href="#responses" role="tab">Canned Responses</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="triggers-tab" data-bs-toggle="tab" href="#triggers" role="tab">Triggers</a>
        </li>
    </ul>

    <div class="tab-content" id="chatTabContent">
        <!-- Agents Tab -->
        <div class="tab-pane fade show active" id="agents" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chat Agents</h5>
                    <button class="btn btn-primary btn-sm" onclick="showAgentModal()">
                        <i class="fas fa-plus"></i> Add Agent
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="agents-table">
                            <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>Status</th>
                                    <th>Active Chats</th>
                                    <th>Max Chats</th>
                                    <th>Departments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments Tab -->
        <div class="tab-pane fade" id="departments" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chat Departments</h5>
                    <button class="btn btn-primary btn-sm" onclick="showDepartmentModal()">
                        <i class="fas fa-plus"></i> New Department
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="departments-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Email</th>
                                    <th>Agents</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canned Responses Tab -->
        <div class="tab-pane fade" id="responses" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Canned Responses</h5>
                    <button class="btn btn-primary btn-sm" onclick="showResponseModal()">
                        <i class="fas fa-plus"></i> New Response
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="responses-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Shortcut</th>
                                    <th>Category</th>
                                    <th>Usage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Triggers Tab -->
        <div class="tab-pane fade" id="triggers" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chat Triggers</h5>
                    <button class="btn btn-primary btn-sm" onclick="showTriggerModal()">
                        <i class="fas fa-plus"></i> New Trigger
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="triggers-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Triggered</th>
                                    <th>Accepted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chat Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="department-form">
                    <input type="hidden" id="dept-id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="dept-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="dept-code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="dept-description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="dept-email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="dept-order" value="0">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="dept-active" checked>
                        <label class="form-check-label" for="dept-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveDepartment()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Canned Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="response-form">
                    <input type="hidden" id="resp-id">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="resp-title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shortcut</label>
                        <input type="text" class="form-control" id="resp-shortcut" required placeholder="/hello">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" id="resp-category">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" id="resp-content" rows="4" required></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="resp-active" checked>
                        <label class="form-check-label" for="resp-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveResponse()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Trigger Modal -->
<div class="modal fade" id="triggerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chat Trigger</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="trigger-form">
                    <input type="hidden" id="trigger-id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="trigger-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trigger Type</label>
                        <select class="form-select" id="trigger-type" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" id="trigger-message" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delay (seconds)</label>
                        <input type="number" class="form-control" id="trigger-delay" value="0" min="0">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="trigger-active" checked>
                        <label class="form-check-label" for="trigger-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTrigger()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url") }}/api/v1';
let triggerTypes = {};

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadTriggerTypes();
    loadAgents();
    loadDepartments();
    loadResponses();
    loadTriggers();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/chat/stats`);
        const data = await response.json();
        const stats = data.data;

        document.getElementById('waiting-chats').textContent = stats.sessions?.waiting || 0;
        document.getElementById('active-chats').textContent = stats.sessions?.active || 0;
        document.getElementById('online-agents').textContent = stats.agents?.online || 0;
        document.getElementById('offline-messages').textContent = stats.offline_messages?.new || 0;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadTriggerTypes() {
    try {
        const response = await fetch(`${API_BASE}/admin/chat/trigger-types`);
        const data = await response.json();
        triggerTypes = data.data;

        const select = document.getElementById('trigger-type');
        select.innerHTML = '';
        Object.entries(triggerTypes).forEach(([key, label]) => {
            select.innerHTML += `<option value="${key}">${label}</option>`;
        });
    } catch (error) {
        console.error('Error loading trigger types:', error);
    }
}

async function loadAgents() {
    try {
        const response = await fetch(`${API_BASE}/admin/chat/agents`);
        const data = await response.json();
        const agents = data.data || [];

        const tbody = document.querySelector('#agents-table tbody');
        tbody.innerHTML = '';

        agents.forEach(agent => {
            const statusClass = agent.status === 'online' ? 'success' : (agent.status === 'away' ? 'warning' : 'secondary');
            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong>${agent.display_name}</strong>
                        <br><small class="text-muted">${agent.user?.email || ''}</small>
                    </td>
                    <td><span class="badge bg-${statusClass}">${agent.status}</span></td>
                    <td>${agent.current_chat_count}</td>
                    <td>${agent.max_concurrent_chats}</td>
                    <td>${(agent.departments || []).map(d => d.name).join(', ') || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editAgent(${agent.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAgent(${agent.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading agents:', error);
    }
}

async function loadDepartments() {
    try {
        const response = await fetch(`${API_BASE}/admin/chat/departments`);
        const data = await response.json();
        const departments = data.data || [];

        const tbody = document.querySelector('#departments-table tbody');
        tbody.innerHTML = '';

        departments.forEach(dept => {
            tbody.innerHTML += `
                <tr>
                    <td>${dept.sort_order}</td>
                    <td><strong>${dept.name}</strong></td>
                    <td><code>${dept.code}</code></td>
                    <td>${dept.email || '-'}</td>
                    <td><span class="badge bg-info">${(dept.agents || []).length}</span></td>
                    <td><span class="badge bg-${dept.is_active ? 'success' : 'secondary'}">${dept.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editDepartment(${dept.id}, '${dept.name}', '${dept.code}', '${dept.description || ''}', '${dept.email || ''}', ${dept.sort_order}, ${dept.is_active})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDepartment(${dept.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading departments:', error);
    }
}

async function loadResponses() {
    try {
        const response = await fetch(`${API_BASE}/admin/chat/canned-responses`);
        const data = await response.json();
        const responses = data.data || [];

        const tbody = document.querySelector('#responses-table tbody');
        tbody.innerHTML = '';

        responses.forEach(resp => {
            tbody.innerHTML += `
                <tr>
                    <td><strong>${resp.title}</strong></td>
                    <td><code>${resp.shortcut}</code></td>
                    <td>${resp.category || '-'}</td>
                    <td>${resp.usage_count}</td>
                    <td><span class="badge bg-${resp.is_active ? 'success' : 'secondary'}">${resp.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editResponse(${resp.id}, '${resp.title}', '${resp.shortcut}', '${resp.category || ''}', '${resp.content.replace(/'/g, "\\'")}', ${resp.is_active})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteResponse(${resp.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading responses:', error);
    }
}

async function loadTriggers() {
    try {
        const response = await fetch(`${API_BASE}/admin/chat/triggers`);
        const data = await response.json();
        const triggers = data.data || [];

        const tbody = document.querySelector('#triggers-table tbody');
        tbody.innerHTML = '';

        triggers.forEach(t => {
            const rate = t.triggered_count > 0 ? Math.round((t.accepted_count / t.triggered_count) * 100) : 0;
            tbody.innerHTML += `
                <tr>
                    <td><strong>${t.name}</strong></td>
                    <td>${triggerTypes[t.trigger_type] || t.trigger_type}</td>
                    <td>${t.triggered_count}</td>
                    <td>${t.accepted_count} (${rate}%)</td>
                    <td><span class="badge bg-${t.is_active ? 'success' : 'secondary'}">${t.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editTrigger(${t.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTrigger(${t.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading triggers:', error);
    }
}

// Department CRUD
function showDepartmentModal() {
    document.getElementById('department-form').reset();
    document.getElementById('dept-id').value = '';
    new bootstrap.Modal(document.getElementById('departmentModal')).show();
}

function editDepartment(id, name, code, desc, email, order, active) {
    document.getElementById('dept-id').value = id;
    document.getElementById('dept-name').value = name;
    document.getElementById('dept-code').value = code;
    document.getElementById('dept-description').value = desc;
    document.getElementById('dept-email').value = email;
    document.getElementById('dept-order').value = order;
    document.getElementById('dept-active').checked = active;
    new bootstrap.Modal(document.getElementById('departmentModal')).show();
}

async function saveDepartment() {
    const id = document.getElementById('dept-id').value;
    const data = {
        name: document.getElementById('dept-name').value,
        code: document.getElementById('dept-code').value,
        description: document.getElementById('dept-description').value || null,
        email: document.getElementById('dept-email').value || null,
        sort_order: parseInt(document.getElementById('dept-order').value),
        is_active: document.getElementById('dept-active').checked
    };

    const url = id ? `${API_BASE}/admin/chat/departments/${id}` : `${API_BASE}/admin/chat/departments`;
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
    bootstrap.Modal.getInstance(document.getElementById('departmentModal')).hide();
    loadDepartments();
}

async function deleteDepartment(id) {
    if (!confirm('Delete this department?')) return;
    await fetch(`${API_BASE}/admin/chat/departments/${id}`, { method: 'DELETE' });
    loadDepartments();
}

// Response CRUD
function showResponseModal() {
    document.getElementById('response-form').reset();
    document.getElementById('resp-id').value = '';
    new bootstrap.Modal(document.getElementById('responseModal')).show();
}

function editResponse(id, title, shortcut, category, content, active) {
    document.getElementById('resp-id').value = id;
    document.getElementById('resp-title').value = title;
    document.getElementById('resp-shortcut').value = shortcut;
    document.getElementById('resp-category').value = category;
    document.getElementById('resp-content').value = content;
    document.getElementById('resp-active').checked = active;
    new bootstrap.Modal(document.getElementById('responseModal')).show();
}

async function saveResponse() {
    const id = document.getElementById('resp-id').value;
    const data = {
        title: document.getElementById('resp-title').value,
        shortcut: document.getElementById('resp-shortcut').value,
        category: document.getElementById('resp-category').value || null,
        content: document.getElementById('resp-content').value,
        is_active: document.getElementById('resp-active').checked
    };

    const url = id ? `${API_BASE}/admin/chat/canned-responses/${id}` : `${API_BASE}/admin/chat/canned-responses`;
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
    bootstrap.Modal.getInstance(document.getElementById('responseModal')).hide();
    loadResponses();
}

async function deleteResponse(id) {
    if (!confirm('Delete this response?')) return;
    await fetch(`${API_BASE}/admin/chat/canned-responses/${id}`, { method: 'DELETE' });
    loadResponses();
}

// Trigger CRUD
function showTriggerModal() {
    document.getElementById('trigger-form').reset();
    document.getElementById('trigger-id').value = '';
    new bootstrap.Modal(document.getElementById('triggerModal')).show();
}

async function editTrigger(id) {
    // Fetch trigger details - simplified for now
    document.getElementById('trigger-id').value = id;
    new bootstrap.Modal(document.getElementById('triggerModal')).show();
}

async function saveTrigger() {
    const id = document.getElementById('trigger-id').value;
    const data = {
        name: document.getElementById('trigger-name').value,
        trigger_type: document.getElementById('trigger-type').value,
        conditions: {},
        message: document.getElementById('trigger-message').value,
        delay_seconds: parseInt(document.getElementById('trigger-delay').value),
        is_active: document.getElementById('trigger-active').checked
    };

    const url = id ? `${API_BASE}/admin/chat/triggers/${id}` : `${API_BASE}/admin/chat/triggers`;
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
    bootstrap.Modal.getInstance(document.getElementById('triggerModal')).hide();
    loadTriggers();
}

async function deleteTrigger(id) {
    if (!confirm('Delete this trigger?')) return;
    await fetch(`${API_BASE}/admin/chat/triggers/${id}`, { method: 'DELETE' });
    loadTriggers();
}

function showAgentModal() { alert('Agent modal - select user to add as agent'); }
function editAgent(id) { alert('Edit agent ' + id); }
async function deleteAgent(id) {
    if (!confirm('Delete this agent?')) return;
    await fetch(`${API_BASE}/admin/chat/agents/${id}`, { method: 'DELETE' });
    loadAgents();
}
</script>
@endsection
