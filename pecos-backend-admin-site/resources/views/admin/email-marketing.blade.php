@extends('layouts.admin')

@section('title', 'Email Marketing')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Email Marketing</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Email Marketing</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" onclick="showCampaignModal()">
            <i class="fas fa-plus"></i> New Campaign
        </button>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-2"><div class="card bg-primary text-white"><div class="card-body py-2 text-center"><h5 class="mb-0" id="stat-lists">0</h5><small>Lists</small></div></div></div>
        <div class="col-md-2"><div class="card bg-success text-white"><div class="card-body py-2 text-center"><h5 class="mb-0" id="stat-subscribers">0</h5><small>Subscribers</small></div></div></div>
        <div class="col-md-2"><div class="card bg-info text-white"><div class="card-body py-2 text-center"><h5 class="mb-0" id="stat-campaigns">0</h5><small>Campaigns</small></div></div></div>
        <div class="col-md-2"><div class="card bg-warning text-dark"><div class="card-body py-2 text-center"><h5 class="mb-0" id="stat-sent">0</h5><small>Emails Sent</small></div></div></div>
        <div class="col-md-2"><div class="card bg-secondary text-white"><div class="card-body py-2 text-center"><h5 class="mb-0" id="stat-open-rate">0%</h5><small>Avg Open Rate</small></div></div></div>
        <div class="col-md-2"><div class="card bg-dark text-white"><div class="card-body py-2 text-center"><h5 class="mb-0" id="stat-click-rate">0%</h5><small>Avg Click Rate</small></div></div></div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#campaigns-tab">Campaigns</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#lists-tab">Lists</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#automations-tab">Automations</a></li>
    </ul>

    <div class="tab-content">
        <!-- Campaigns Tab -->
        <div class="tab-pane fade show active" id="campaigns-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Campaigns</h5>
                    <select class="form-select form-select-sm w-auto" id="campaign-filter" onchange="loadCampaigns()">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="sent">Sent</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Name</th><th>Subject</th><th>List</th><th>Status</th><th>Sent</th><th>Opens</th><th>Clicks</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="campaigns-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lists Tab -->
        <div class="tab-pane fade" id="lists-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Email Lists</h5>
                    <button class="btn btn-sm btn-primary" onclick="showListModal()"><i class="fas fa-plus"></i> New List</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Name</th><th>Description</th><th>Subscribers</th><th>Double Opt-in</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody id="lists-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Automations Tab -->
        <div class="tab-pane fade" id="automations-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Email Automations</h5>
                    <button class="btn btn-sm btn-primary" onclick="showAutomationModal()"><i class="fas fa-plus"></i> New Automation</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Name</th><th>Trigger</th><th>Steps</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody id="automations-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Campaign Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Create Campaign</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="campaign-form">
                    <input type="hidden" id="campaign-id">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Name *</label><input type="text" class="form-control" id="campaign-name" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Subject *</label><input type="text" class="form-control" id="campaign-subject" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">From Name *</label><input type="text" class="form-control" id="campaign-from-name" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">From Email *</label><input type="email" class="form-control" id="campaign-from-email" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Email List *</label><select class="form-select" id="campaign-list" required></select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Preview Text</label><input type="text" class="form-control" id="campaign-preview"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">HTML Content</label><textarea class="form-control" id="campaign-html" rows="6"></textarea></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCampaign()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- List Modal -->
<div class="modal fade" id="listModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Email List</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="list-form">
                    <input type="hidden" id="list-id">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" class="form-control" id="list-name" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" id="list-description" rows="2"></textarea></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="list-double-optin"><label class="form-check-label" for="list-double-optin">Require Double Opt-in</label></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveList()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url", env("API_URL", "http://localhost:8000")) }}/api';
let lists = [], campaigns = [], automations = [];

document.addEventListener('DOMContentLoaded', function() { loadStats(); loadLists(); loadCampaigns(); loadAutomations(); });

function loadStats() {
    fetch(`${API_BASE}/admin/email-marketing/stats`).then(r => r.json()).then(d => {
        if (d.success) {
            document.getElementById('stat-lists').textContent = d.data.total_lists || 0;
            document.getElementById('stat-subscribers').textContent = d.data.total_subscribers || 0;
            document.getElementById('stat-campaigns').textContent = d.data.total_campaigns || 0;
            document.getElementById('stat-sent').textContent = d.data.total_emails_sent || 0;
            document.getElementById('stat-open-rate').textContent = (d.data.avg_open_rate || 0) + '%';
            document.getElementById('stat-click-rate').textContent = (d.data.avg_click_rate || 0) + '%';
        }
    });
}

function loadLists() {
    fetch(`${API_BASE}/admin/email-lists`).then(r => r.json()).then(d => {
        if (d.success) { lists = d.data; renderLists(); populateListSelect(); }
    });
}

function renderLists() {
    const tbody = document.getElementById('lists-tbody');
    if (lists.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center">No lists</td></tr>'; return; }
    tbody.innerHTML = lists.map(l => `<tr>
        <td><strong>${l.name}</strong></td>
        <td>${l.description || '-'}</td>
        <td><span class="badge bg-info">${l.subscribers_count || 0}</span></td>
        <td>${l.double_optin ? '<i class="fas fa-check text-success"></i>' : '-'}</td>
        <td><span class="badge ${l.is_active ? 'bg-success' : 'bg-secondary'}">${l.is_active ? 'Active' : 'Inactive'}</span></td>
        <td><button class="btn btn-sm btn-outline-primary" onclick="editList(${l.id})"><i class="fas fa-edit"></i></button></td>
    </tr>`).join('');
}

function populateListSelect() {
    document.getElementById('campaign-list').innerHTML = lists.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
}

function loadCampaigns() {
    const status = document.getElementById('campaign-filter').value;
    let url = `${API_BASE}/admin/campaigns`;
    if (status) url += `?status=${status}`;
    fetch(url).then(r => r.json()).then(d => { if (d.success) { campaigns = d.data.data || d.data; renderCampaigns(); }});
}

function renderCampaigns() {
    const tbody = document.getElementById('campaigns-tbody');
    if (campaigns.length === 0) { tbody.innerHTML = '<tr><td colspan="8" class="text-center">No campaigns</td></tr>'; return; }
    tbody.innerHTML = campaigns.map(c => `<tr>
        <td><strong>${c.name}</strong></td>
        <td>${c.subject}</td>
        <td>${c.email_list?.name || '-'}</td>
        <td><span class="badge bg-${getStatusColor(c.status)}">${c.status}</span></td>
        <td>${c.sent_count || 0}</td>
        <td>${c.open_count || 0}</td>
        <td>${c.click_count || 0}</td>
        <td>
            <div class="btn-group btn-group-sm">
                ${c.status === 'draft' ? `<button class="btn btn-outline-success" onclick="sendCampaign(${c.id})" title="Send"><i class="fas fa-paper-plane"></i></button>` : ''}
                <button class="btn btn-outline-primary" onclick="editCampaign(${c.id})" title="Edit"><i class="fas fa-edit"></i></button>
                <button class="btn btn-outline-secondary" onclick="duplicateCampaign(${c.id})" title="Duplicate"><i class="fas fa-copy"></i></button>
            </div>
        </td>
    </tr>`).join('');
}

function getStatusColor(status) {
    return { draft: 'secondary', scheduled: 'warning', sending: 'info', sent: 'success', paused: 'warning', cancelled: 'danger' }[status] || 'secondary';
}

function loadAutomations() {
    fetch(`${API_BASE}/admin/automations`).then(r => r.json()).then(d => { if (d.success) { automations = d.data; renderAutomations(); }});
}

function renderAutomations() {
    const tbody = document.getElementById('automations-tbody');
    if (automations.length === 0) { tbody.innerHTML = '<tr><td colspan="5" class="text-center">No automations</td></tr>'; return; }
    tbody.innerHTML = automations.map(a => `<tr>
        <td><strong>${a.name}</strong></td>
        <td><span class="badge bg-info">${a.trigger_type}</span></td>
        <td>${a.steps_count || 0}</td>
        <td><span class="badge ${a.is_active ? 'bg-success' : 'bg-secondary'}">${a.is_active ? 'Active' : 'Inactive'}</span></td>
        <td>
            <button class="btn btn-sm btn-outline-${a.is_active ? 'warning' : 'success'}" onclick="toggleAutomation(${a.id})">${a.is_active ? 'Pause' : 'Activate'}</button>
        </td>
    </tr>`).join('');
}

function showCampaignModal() { document.getElementById('campaign-form').reset(); document.getElementById('campaign-id').value = ''; new bootstrap.Modal(document.getElementById('campaignModal')).show(); }
function showListModal() { document.getElementById('list-form').reset(); document.getElementById('list-id').value = ''; new bootstrap.Modal(document.getElementById('listModal')).show(); }

function saveCampaign() {
    const id = document.getElementById('campaign-id').value;
    const data = {
        name: document.getElementById('campaign-name').value,
        subject: document.getElementById('campaign-subject').value,
        from_name: document.getElementById('campaign-from-name').value,
        from_email: document.getElementById('campaign-from-email').value,
        email_list_id: document.getElementById('campaign-list').value,
        preview_text: document.getElementById('campaign-preview').value,
        html_content: document.getElementById('campaign-html').value,
    };
    fetch(id ? `${API_BASE}/admin/campaigns/${id}` : `${API_BASE}/admin/campaigns`, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
    .then(r => r.json()).then(d => { if (d.success) { bootstrap.Modal.getInstance(document.getElementById('campaignModal')).hide(); loadCampaigns(); loadStats(); }});
}

function saveList() {
    const id = document.getElementById('list-id').value;
    const data = { name: document.getElementById('list-name').value, description: document.getElementById('list-description').value, double_optin: document.getElementById('list-double-optin').checked };
    fetch(id ? `${API_BASE}/admin/email-lists/${id}` : `${API_BASE}/admin/email-lists`, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
    .then(r => r.json()).then(d => { if (d.success) { bootstrap.Modal.getInstance(document.getElementById('listModal')).hide(); loadLists(); loadStats(); }});
}

function editList(id) {
    const list = lists.find(l => l.id === id);
    if (!list) return;
    document.getElementById('list-id').value = id;
    document.getElementById('list-name').value = list.name;
    document.getElementById('list-description').value = list.description || '';
    document.getElementById('list-double-optin').checked = list.double_optin;
    new bootstrap.Modal(document.getElementById('listModal')).show();
}

function editCampaign(id) { fetch(`${API_BASE}/admin/campaigns/${id}`).then(r => r.json()).then(d => {
    if (d.success) {
        const c = d.data;
        document.getElementById('campaign-id').value = c.id;
        document.getElementById('campaign-name').value = c.name;
        document.getElementById('campaign-subject').value = c.subject;
        document.getElementById('campaign-from-name').value = c.from_name;
        document.getElementById('campaign-from-email').value = c.from_email;
        document.getElementById('campaign-list').value = c.email_list_id;
        document.getElementById('campaign-preview').value = c.preview_text || '';
        document.getElementById('campaign-html').value = c.html_content || '';
        new bootstrap.Modal(document.getElementById('campaignModal')).show();
    }
});}

function sendCampaign(id) { if (!confirm('Send this campaign now?')) return; fetch(`${API_BASE}/admin/campaigns/${id}/send`, { method: 'POST' }).then(r => r.json()).then(d => { if (d.success) loadCampaigns(); }); }
function duplicateCampaign(id) { fetch(`${API_BASE}/admin/campaigns/${id}/duplicate`, { method: 'POST' }).then(r => r.json()).then(d => { if (d.success) loadCampaigns(); }); }
function toggleAutomation(id) { fetch(`${API_BASE}/admin/automations/${id}/toggle`, { method: 'POST' }).then(r => r.json()).then(d => { if (d.success) loadAutomations(); }); }
</script>
@endsection
