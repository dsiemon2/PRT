@extends('layouts.admin')

@section('title', 'SMS/Push Notifications')
@section('page-title', 'SMS/Push Notifications')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4" id="stats-row">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">SMS Sent Today</h6>
                    <h2 id="sms-sent-today">0</h2>
                    <small>Delivery Rate: <span id="sms-delivery-rate">0%</span></small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Push Sent Today</h6>
                    <h2 id="push-sent-today">0</h2>
                    <small>Click Rate: <span id="push-click-rate">0%</span></small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Active Automations</h6>
                    <h2 id="active-automations">0</h2>
                    <small>Running automatically</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="card-title">Device Tokens</h6>
                    <h2 id="device-tokens">0</h2>
                    <small>Active subscribers</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="notificationTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="sms-templates-tab" data-bs-toggle="tab" href="#sms-templates" role="tab">SMS Templates</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="push-templates-tab" data-bs-toggle="tab" href="#push-templates" role="tab">Push Templates</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="campaigns-tab" data-bs-toggle="tab" href="#campaigns" role="tab">Campaigns</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="automations-tab" data-bs-toggle="tab" href="#automations" role="tab">Automations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="channels-tab" data-bs-toggle="tab" href="#channels" role="tab">Channels</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab">Message Logs</a>
        </li>
    </ul>

    <div class="tab-content" id="notificationTabContent">
        <!-- SMS Templates Tab -->
        <div class="tab-pane fade show active" id="sms-templates" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">SMS Templates</h5>
                    <button class="btn btn-primary btn-sm" onclick="showSmsTemplateModal()">
                        <i class="fas fa-plus"></i> New SMS Template
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="sms-templates-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Trigger</th>
                                    <th>Preview</th>
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

        <!-- Push Templates Tab -->
        <div class="tab-pane fade" id="push-templates" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Push Notification Templates</h5>
                    <button class="btn btn-primary btn-sm" onclick="showPushTemplateModal()">
                        <i class="fas fa-plus"></i> New Push Template
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="push-templates-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Trigger</th>
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

        <!-- Campaigns Tab -->
        <div class="tab-pane fade" id="campaigns" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notification Campaigns</h5>
                    <button class="btn btn-primary btn-sm" onclick="showCampaignModal()">
                        <i class="fas fa-plus"></i> New Campaign
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="campaigns-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Audience</th>
                                    <th>Sent</th>
                                    <th>Scheduled</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Automations Tab -->
        <div class="tab-pane fade" id="automations" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notification Automations</h5>
                    <button class="btn btn-primary btn-sm" onclick="showAutomationModal()">
                        <i class="fas fa-plus"></i> New Automation
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="automations-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Trigger</th>
                                    <th>Type</th>
                                    <th>Delay</th>
                                    <th>Sent</th>
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

        <!-- Channels Tab -->
        <div class="tab-pane fade" id="channels" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notification Channels</h5>
                    <button class="btn btn-primary btn-sm" onclick="showChannelModal()">
                        <i class="fas fa-plus"></i> Add Channel
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>SMS Providers</h6>
                            <div id="sms-channels-list"></div>
                        </div>
                        <div class="col-md-6">
                            <h6>Push Providers</h6>
                            <div id="push-channels-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Tab -->
        <div class="tab-pane fade" id="logs" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent SMS Messages</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm" id="sms-logs-table">
                                    <thead>
                                        <tr>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Sent</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Push Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm" id="push-logs-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Sent</th>
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
    </div>
</div>

<!-- SMS Template Modal -->
<div class="modal fade" id="smsTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SMS Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sms-template-form">
                    <input type="hidden" id="sms-template-id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="sms-template-name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" id="sms-template-code" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Trigger</label>
                        <select class="form-select" id="sms-template-trigger">
                            <option value="">No trigger (manual only)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message Content</label>
                        <textarea class="form-control" id="sms-template-content" rows="4" required></textarea>
                        <small class="text-muted">Use {variable_name} for dynamic content. Character count: <span id="sms-char-count">0</span>/160</small>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="sms-template-active" checked>
                        <label class="form-check-label" for="sms-template-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSmsTemplate()">Save Template</button>
            </div>
        </div>
    </div>
</div>

<!-- Push Template Modal -->
<div class="modal fade" id="pushTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Push Notification Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="push-template-form">
                    <input type="hidden" id="push-template-id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="push-template-name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" id="push-template-code" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="push-template-title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <textarea class="form-control" id="push-template-body" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icon URL</label>
                            <input type="text" class="form-control" id="push-template-icon">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Click URL</label>
                            <input type="text" class="form-control" id="push-template-url">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Trigger</label>
                        <select class="form-select" id="push-template-trigger">
                            <option value="">No trigger (manual only)</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="push-template-active" checked>
                        <label class="form-check-label" for="push-template-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePushTemplate()">Save Template</button>
            </div>
        </div>
    </div>
</div>

<!-- Campaign Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Campaign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="campaign-form">
                    <input type="hidden" id="campaign-id">
                    <div class="mb-3">
                        <label class="form-label">Campaign Name</label>
                        <input type="text" class="form-control" id="campaign-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="campaign-description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Campaign Type</label>
                        <select class="form-select" id="campaign-type" required>
                            <option value="sms">SMS Only</option>
                            <option value="push">Push Only</option>
                            <option value="both">Both SMS & Push</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMS Template</label>
                            <select class="form-select" id="campaign-sms-template"></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Push Template</label>
                            <select class="form-select" id="campaign-push-template"></select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Schedule</label>
                        <input type="datetime-local" class="form-control" id="campaign-scheduled">
                        <small class="text-muted">Leave empty to send immediately</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCampaign()">Save Campaign</button>
            </div>
        </div>
    </div>
</div>

<!-- Automation Modal -->
<div class="modal fade" id="automationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Automation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="automation-form">
                    <input type="hidden" id="automation-id">
                    <div class="mb-3">
                        <label class="form-label">Automation Name</label>
                        <input type="text" class="form-control" id="automation-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="automation-description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trigger Event</label>
                            <select class="form-select" id="automation-trigger" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Delay (minutes)</label>
                            <input type="number" class="form-control" id="automation-delay" value="0" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notification Type</label>
                        <select class="form-select" id="automation-type" required>
                            <option value="sms">SMS Only</option>
                            <option value="push">Push Only</option>
                            <option value="both">Both SMS & Push</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMS Template</label>
                            <select class="form-select" id="automation-sms-template"></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Push Template</label>
                            <select class="form-select" id="automation-push-template"></select>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="automation-active">
                        <label class="form-check-label" for="automation-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAutomation()">Save Automation</button>
            </div>
        </div>
    </div>
</div>

<!-- Channel Modal -->
<div class="modal fade" id="channelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Channel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="channel-form">
                    <input type="hidden" id="channel-id">
                    <div class="mb-3">
                        <label class="form-label">Channel Type</label>
                        <select class="form-select" id="channel-type" required onchange="loadProviders()">
                            <option value="sms">SMS</option>
                            <option value="push">Push</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <select class="form-select" id="channel-provider" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="channel-name" required>
                    </div>
                    <div id="credentials-fields"></div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="channel-active">
                        <label class="form-check-label" for="channel-active">Active</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="channel-default">
                        <label class="form-check-label" for="channel-default">Set as Default</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" onclick="testChannel()">Test Connection</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveChannel()">Save Channel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url") }}/api/v1';
let triggerEvents = {};
let smsTemplates = [];
let pushTemplates = [];

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadTriggerEvents();
    loadSmsTemplates();
    loadPushTemplates();
    loadCampaigns();
    loadAutomations();
    loadChannels();
    loadLogs();

    document.getElementById('sms-template-content').addEventListener('input', function() {
        document.getElementById('sms-char-count').textContent = this.value.length;
    });
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/notifications/stats`);
        const data = await response.json();
        const stats = data.data;

        document.getElementById('sms-sent-today').textContent = stats.sms.sent_today || 0;
        document.getElementById('sms-delivery-rate').textContent = (stats.sms.delivery_rate || 0) + '%';
        document.getElementById('push-sent-today').textContent = stats.push.sent_today || 0;
        document.getElementById('push-click-rate').textContent = (stats.push.click_rate || 0) + '%';
        document.getElementById('active-automations').textContent = stats.automations.active || 0;
        document.getElementById('device-tokens').textContent = stats.device_tokens.total || 0;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadTriggerEvents() {
    try {
        const response = await fetch(`${API_BASE}/admin/notifications/trigger-events`);
        const data = await response.json();
        triggerEvents = data.data;

        const selects = ['sms-template-trigger', 'push-template-trigger', 'automation-trigger'];
        selects.forEach(id => {
            const select = document.getElementById(id);
            if (select) {
                const firstOption = id === 'automation-trigger' ? '' : '<option value="">No trigger (manual only)</option>';
                select.innerHTML = firstOption;
                Object.entries(triggerEvents).forEach(([key, label]) => {
                    select.innerHTML += `<option value="${key}">${label}</option>`;
                });
            }
        });
    } catch (error) {
        console.error('Error loading trigger events:', error);
    }
}

async function loadSmsTemplates() {
    try {
        const response = await fetch(`${API_BASE}/admin/sms-templates`);
        const data = await response.json();
        smsTemplates = data.data || [];

        const tbody = document.querySelector('#sms-templates-table tbody');
        tbody.innerHTML = '';

        smsTemplates.forEach(template => {
            tbody.innerHTML += `
                <tr>
                    <td>${template.name}</td>
                    <td><code>${template.code}</code></td>
                    <td>${template.event_trigger || '-'}</td>
                    <td><small class="text-muted">${(template.content || '').substring(0, 50)}...</small></td>
                    <td><span class="badge bg-${template.is_active ? 'success' : 'secondary'}">${template.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editSmsTemplate(${template.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSmsTemplate(${template.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        updateTemplateSelects();
    } catch (error) {
        console.error('Error loading SMS templates:', error);
    }
}

async function loadPushTemplates() {
    try {
        const response = await fetch(`${API_BASE}/admin/push-templates`);
        const data = await response.json();
        pushTemplates = data.data || [];

        const tbody = document.querySelector('#push-templates-table tbody');
        tbody.innerHTML = '';

        pushTemplates.forEach(template => {
            tbody.innerHTML += `
                <tr>
                    <td>${template.name}</td>
                    <td><code>${template.code}</code></td>
                    <td>${template.title}</td>
                    <td>${template.event_trigger || '-'}</td>
                    <td><span class="badge bg-${template.is_active ? 'success' : 'secondary'}">${template.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editPushTemplate(${template.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePushTemplate(${template.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        updateTemplateSelects();
    } catch (error) {
        console.error('Error loading push templates:', error);
    }
}

function updateTemplateSelects() {
    const smsSelects = ['campaign-sms-template', 'automation-sms-template'];
    const pushSelects = ['campaign-push-template', 'automation-push-template'];

    smsSelects.forEach(id => {
        const select = document.getElementById(id);
        if (select) {
            select.innerHTML = '<option value="">Select SMS Template</option>';
            smsTemplates.forEach(t => {
                select.innerHTML += `<option value="${t.id}">${t.name}</option>`;
            });
        }
    });

    pushSelects.forEach(id => {
        const select = document.getElementById(id);
        if (select) {
            select.innerHTML = '<option value="">Select Push Template</option>';
            pushTemplates.forEach(t => {
                select.innerHTML += `<option value="${t.id}">${t.name}</option>`;
            });
        }
    });
}

async function loadCampaigns() {
    try {
        const response = await fetch(`${API_BASE}/admin/notification-campaigns`);
        const data = await response.json();
        const campaigns = data.data || [];

        const tbody = document.querySelector('#campaigns-table tbody');
        tbody.innerHTML = '';

        campaigns.forEach(campaign => {
            const statusColors = {
                draft: 'secondary', scheduled: 'info', sending: 'primary', sent: 'success', paused: 'warning', cancelled: 'danger'
            };
            tbody.innerHTML += `
                <tr>
                    <td>${campaign.name}</td>
                    <td><span class="badge bg-info">${campaign.type.toUpperCase()}</span></td>
                    <td><span class="badge bg-${statusColors[campaign.status]}">${campaign.status}</span></td>
                    <td>${campaign.audience_count || 0}</td>
                    <td>${(campaign.sms_sent || 0) + (campaign.push_sent || 0)}</td>
                    <td>${campaign.scheduled_at ? new Date(campaign.scheduled_at).toLocaleString() : '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editCampaign(${campaign.id})"><i class="fas fa-edit"></i></button>
                        ${campaign.status === 'draft' ? `<button class="btn btn-sm btn-outline-success" onclick="sendCampaign(${campaign.id})"><i class="fas fa-paper-plane"></i></button>` : ''}
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCampaign(${campaign.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading campaigns:', error);
    }
}

async function loadAutomations() {
    try {
        const response = await fetch(`${API_BASE}/admin/notification-automations`);
        const data = await response.json();
        const automations = data.data || [];

        const tbody = document.querySelector('#automations-table tbody');
        tbody.innerHTML = '';

        automations.forEach(auto => {
            tbody.innerHTML += `
                <tr>
                    <td>${auto.name}</td>
                    <td>${triggerEvents[auto.trigger_event] || auto.trigger_event}</td>
                    <td><span class="badge bg-info">${auto.notification_type.toUpperCase()}</span></td>
                    <td>${auto.delay_minutes} min</td>
                    <td>${auto.sent_count || 0}</td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" ${auto.is_active ? 'checked' : ''} onchange="toggleAutomation(${auto.id})">
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editAutomation(${auto.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAutomation(${auto.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading automations:', error);
    }
}

async function loadChannels() {
    try {
        const response = await fetch(`${API_BASE}/admin/notification-channels`);
        const data = await response.json();
        const channels = data.data || [];

        const smsList = document.getElementById('sms-channels-list');
        const pushList = document.getElementById('push-channels-list');
        smsList.innerHTML = '';
        pushList.innerHTML = '';

        channels.forEach(channel => {
            const html = `
                <div class="card mb-2">
                    <div class="card-body p-2 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${channel.name}</strong>
                            ${channel.is_default ? '<span class="badge bg-primary ms-2">Default</span>' : ''}
                            <span class="badge bg-${channel.is_active ? 'success' : 'secondary'} ms-1">${channel.is_active ? 'Active' : 'Inactive'}</span>
                            <br><small class="text-muted">${channel.provider}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="editChannel(${channel.id})"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteChannel(${channel.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
            if (channel.type === 'sms') {
                smsList.innerHTML += html;
            } else {
                pushList.innerHTML += html;
            }
        });

        if (smsList.innerHTML === '') smsList.innerHTML = '<p class="text-muted">No SMS channels configured</p>';
        if (pushList.innerHTML === '') pushList.innerHTML = '<p class="text-muted">No push channels configured</p>';
    } catch (error) {
        console.error('Error loading channels:', error);
    }
}

async function loadLogs() {
    try {
        const [smsResponse, pushResponse] = await Promise.all([
            fetch(`${API_BASE}/admin/sms-messages?per_page=10`),
            fetch(`${API_BASE}/admin/push-notifications?per_page=10`)
        ]);
        const smsData = await smsResponse.json();
        const pushData = await pushResponse.json();

        const smsLogs = smsData.data || [];
        const pushLogs = pushData.data || [];

        const smsTbody = document.querySelector('#sms-logs-table tbody');
        smsTbody.innerHTML = '';
        smsLogs.forEach(log => {
            const statusColors = { pending: 'warning', sent: 'info', delivered: 'success', failed: 'danger' };
            smsTbody.innerHTML += `
                <tr>
                    <td>${log.phone_number}</td>
                    <td><span class="badge bg-${statusColors[log.status] || 'secondary'}">${log.status}</span></td>
                    <td>${new Date(log.created_at).toLocaleString()}</td>
                </tr>
            `;
        });

        const pushTbody = document.querySelector('#push-logs-table tbody');
        pushTbody.innerHTML = '';
        pushLogs.forEach(log => {
            const statusColors = { pending: 'warning', sent: 'info', delivered: 'success', clicked: 'primary', failed: 'danger' };
            pushTbody.innerHTML += `
                <tr>
                    <td>${log.title}</td>
                    <td><span class="badge bg-${statusColors[log.status] || 'secondary'}">${log.status}</span></td>
                    <td>${new Date(log.created_at).toLocaleString()}</td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading logs:', error);
    }
}

// SMS Template functions
function showSmsTemplateModal() {
    document.getElementById('sms-template-form').reset();
    document.getElementById('sms-template-id').value = '';
    document.getElementById('sms-char-count').textContent = '0';
    new bootstrap.Modal(document.getElementById('smsTemplateModal')).show();
}

async function editSmsTemplate(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/sms-templates/${id}`);
        const data = await response.json();
        const template = data.data;

        document.getElementById('sms-template-id').value = template.id;
        document.getElementById('sms-template-name').value = template.name;
        document.getElementById('sms-template-code').value = template.code;
        document.getElementById('sms-template-trigger').value = template.event_trigger || '';
        document.getElementById('sms-template-content').value = template.content;
        document.getElementById('sms-template-active').checked = template.is_active;
        document.getElementById('sms-char-count').textContent = template.content.length;

        new bootstrap.Modal(document.getElementById('smsTemplateModal')).show();
    } catch (error) {
        alert('Error loading template');
    }
}

async function saveSmsTemplate() {
    const id = document.getElementById('sms-template-id').value;
    const data = {
        name: document.getElementById('sms-template-name').value,
        code: document.getElementById('sms-template-code').value,
        content: document.getElementById('sms-template-content').value,
        event_trigger: document.getElementById('sms-template-trigger').value || null,
        is_active: document.getElementById('sms-template-active').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/sms-templates/${id}` : `${API_BASE}/admin/sms-templates`;
        const method = id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('smsTemplateModal')).hide();
            loadSmsTemplates();
        } else {
            const error = await response.json();
            alert(error.message || 'Error saving template');
        }
    } catch (error) {
        alert('Error saving template');
    }
}

async function deleteSmsTemplate(id) {
    if (!confirm('Delete this SMS template?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/sms-templates/${id}`, { method: 'DELETE' });
        if (response.ok) {
            loadSmsTemplates();
        }
    } catch (error) {
        alert('Error deleting template');
    }
}

// Push Template functions
function showPushTemplateModal() {
    document.getElementById('push-template-form').reset();
    document.getElementById('push-template-id').value = '';
    new bootstrap.Modal(document.getElementById('pushTemplateModal')).show();
}

async function editPushTemplate(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/push-templates/${id}`);
        const data = await response.json();
        const template = data.data;

        document.getElementById('push-template-id').value = template.id;
        document.getElementById('push-template-name').value = template.name;
        document.getElementById('push-template-code').value = template.code;
        document.getElementById('push-template-title').value = template.title;
        document.getElementById('push-template-body').value = template.body;
        document.getElementById('push-template-icon').value = template.icon || '';
        document.getElementById('push-template-url').value = template.url || '';
        document.getElementById('push-template-trigger').value = template.event_trigger || '';
        document.getElementById('push-template-active').checked = template.is_active;

        new bootstrap.Modal(document.getElementById('pushTemplateModal')).show();
    } catch (error) {
        alert('Error loading template');
    }
}

async function savePushTemplate() {
    const id = document.getElementById('push-template-id').value;
    const data = {
        name: document.getElementById('push-template-name').value,
        code: document.getElementById('push-template-code').value,
        title: document.getElementById('push-template-title').value,
        body: document.getElementById('push-template-body').value,
        icon: document.getElementById('push-template-icon').value || null,
        url: document.getElementById('push-template-url').value || null,
        event_trigger: document.getElementById('push-template-trigger').value || null,
        is_active: document.getElementById('push-template-active').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/push-templates/${id}` : `${API_BASE}/admin/push-templates`;
        const method = id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('pushTemplateModal')).hide();
            loadPushTemplates();
        } else {
            const error = await response.json();
            alert(error.message || 'Error saving template');
        }
    } catch (error) {
        alert('Error saving template');
    }
}

async function deletePushTemplate(id) {
    if (!confirm('Delete this push template?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/push-templates/${id}`, { method: 'DELETE' });
        if (response.ok) {
            loadPushTemplates();
        }
    } catch (error) {
        alert('Error deleting template');
    }
}

// Campaign functions
function showCampaignModal() {
    document.getElementById('campaign-form').reset();
    document.getElementById('campaign-id').value = '';
    new bootstrap.Modal(document.getElementById('campaignModal')).show();
}

async function saveCampaign() {
    const id = document.getElementById('campaign-id').value;
    const data = {
        name: document.getElementById('campaign-name').value,
        description: document.getElementById('campaign-description').value,
        type: document.getElementById('campaign-type').value,
        sms_template_id: document.getElementById('campaign-sms-template').value || null,
        push_template_id: document.getElementById('campaign-push-template').value || null,
        scheduled_at: document.getElementById('campaign-scheduled').value || null
    };

    try {
        const url = id ? `${API_BASE}/admin/notification-campaigns/${id}` : `${API_BASE}/admin/notification-campaigns`;
        const method = id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('campaignModal')).hide();
            loadCampaigns();
        } else {
            const error = await response.json();
            alert(error.message || 'Error saving campaign');
        }
    } catch (error) {
        alert('Error saving campaign');
    }
}

async function sendCampaign(id) {
    if (!confirm('Send this campaign now?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/notification-campaigns/${id}/send`, { method: 'POST' });
        if (response.ok) {
            loadCampaigns();
            loadStats();
        }
    } catch (error) {
        alert('Error sending campaign');
    }
}

async function deleteCampaign(id) {
    if (!confirm('Delete this campaign?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/notification-campaigns/${id}`, { method: 'DELETE' });
        if (response.ok) {
            loadCampaigns();
        }
    } catch (error) {
        alert('Error deleting campaign');
    }
}

// Automation functions
function showAutomationModal() {
    document.getElementById('automation-form').reset();
    document.getElementById('automation-id').value = '';
    new bootstrap.Modal(document.getElementById('automationModal')).show();
}

async function saveAutomation() {
    const id = document.getElementById('automation-id').value;
    const data = {
        name: document.getElementById('automation-name').value,
        description: document.getElementById('automation-description').value,
        trigger_event: document.getElementById('automation-trigger').value,
        delay_minutes: parseInt(document.getElementById('automation-delay').value) || 0,
        notification_type: document.getElementById('automation-type').value,
        sms_template_id: document.getElementById('automation-sms-template').value || null,
        push_template_id: document.getElementById('automation-push-template').value || null,
        is_active: document.getElementById('automation-active').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/notification-automations/${id}` : `${API_BASE}/admin/notification-automations`;
        const method = id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('automationModal')).hide();
            loadAutomations();
            loadStats();
        } else {
            const error = await response.json();
            alert(error.message || 'Error saving automation');
        }
    } catch (error) {
        alert('Error saving automation');
    }
}

async function toggleAutomation(id) {
    try {
        await fetch(`${API_BASE}/admin/notification-automations/${id}/toggle`, { method: 'POST' });
        loadAutomations();
        loadStats();
    } catch (error) {
        alert('Error toggling automation');
    }
}

async function deleteAutomation(id) {
    if (!confirm('Delete this automation?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/notification-automations/${id}`, { method: 'DELETE' });
        if (response.ok) {
            loadAutomations();
        }
    } catch (error) {
        alert('Error deleting automation');
    }
}

// Channel functions
function showChannelModal() {
    document.getElementById('channel-form').reset();
    document.getElementById('channel-id').value = '';
    loadProviders();
    new bootstrap.Modal(document.getElementById('channelModal')).show();
}

async function loadProviders() {
    const type = document.getElementById('channel-type').value;
    try {
        const response = await fetch(`${API_BASE}/admin/notifications/providers?type=${type}`);
        const data = await response.json();
        const providers = data.data;

        const select = document.getElementById('channel-provider');
        select.innerHTML = '<option value="">Select Provider</option>';
        Object.entries(providers).forEach(([key, name]) => {
            select.innerHTML += `<option value="${key}">${name}</option>`;
        });
    } catch (error) {
        console.error('Error loading providers:', error);
    }
}

async function saveChannel() {
    const id = document.getElementById('channel-id').value;
    const data = {
        type: document.getElementById('channel-type').value,
        provider: document.getElementById('channel-provider').value,
        name: document.getElementById('channel-name').value,
        credentials: {},
        is_active: document.getElementById('channel-active').checked,
        is_default: document.getElementById('channel-default').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/notification-channels/${id}` : `${API_BASE}/admin/notification-channels`;
        const method = id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('channelModal')).hide();
            loadChannels();
        } else {
            const error = await response.json();
            alert(error.message || 'Error saving channel');
        }
    } catch (error) {
        alert('Error saving channel');
    }
}

async function deleteChannel(id) {
    if (!confirm('Delete this channel?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/notification-channels/${id}`, { method: 'DELETE' });
        if (response.ok) {
            loadChannels();
        }
    } catch (error) {
        alert('Error deleting channel');
    }
}
</script>
@endsection
