@extends('layouts.admin')

@section('title', 'Language & Translation Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Language & Translation Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Languages</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#languageModal" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Add Language
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total Languages</h6>
                            <h2 class="mb-0" id="stat-total">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-globe fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Active Languages</h6>
                            <h2 class="mb-0" id="stat-active">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Translation Keys</h6>
                            <h2 class="mb-0" id="stat-keys">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-key fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Unreviewed</h6>
                            <h2 class="mb-0" id="stat-unreviewed">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="languageTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#languages-tab">Languages</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#translations-tab">Translations</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Languages Tab -->
        <div class="tab-pane fade show active" id="languages-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-language me-2"></i>All Languages</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Flag</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Native Name</th>
                                    <th>Direction</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="languages-tbody">
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Translations Tab -->
        <div class="tab-pane fade" id="translations-tab">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label mb-0">Language</label>
                            <select class="form-select" id="trans-language" onchange="loadTranslations()">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-0">Group</label>
                            <select class="form-select" id="trans-group" onchange="loadTranslations()">
                                <option value="">All Groups</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-0">Filter</label>
                            <select class="form-select" id="trans-filter" onchange="loadTranslations()">
                                <option value="">All</option>
                                <option value="unreviewed">Unreviewed Only</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-outline-primary me-2" onclick="showAddKeyModal()">
                                <i class="fas fa-plus"></i> Add Key
                            </button>
                            <button class="btn btn-outline-success" onclick="exportTranslations()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="15%">Group</th>
                                    <th width="20%">Key</th>
                                    <th width="45%">Translation</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="translations-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Language Modal -->
<div class="modal fade" id="languageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="languageModalTitle">Add Language</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="language-form">
                    <input type="hidden" id="lang-id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" id="lang-code" maxlength="10"
                                   placeholder="en" required>
                            <small class="text-muted">ISO 639-1 (e.g., en, es, fr)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Locale *</label>
                            <input type="text" class="form-control" id="lang-locale" maxlength="20"
                                   placeholder="en_US" required>
                            <small class="text-muted">Full locale (e.g., en_US, es_MX)</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" id="lang-name"
                                   placeholder="English" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Native Name *</label>
                            <input type="text" class="form-control" id="lang-native-name"
                                   placeholder="English" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Flag Icon</label>
                            <input type="text" class="form-control" id="lang-flag"
                                   placeholder="🇺🇸">
                            <small class="text-muted">Emoji or icon class</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Direction</label>
                            <select class="form-select" id="lang-direction">
                                <option value="ltr">Left to Right (LTR)</option>
                                <option value="rtl">Right to Left (RTL)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="lang-active" checked>
                            <label class="form-check-label" for="lang-active">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveLanguage()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Translation Key Modal -->
<div class="modal fade" id="keyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Translation Key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="key-form">
                    <div class="mb-3">
                        <label class="form-label">Group *</label>
                        <input type="text" class="form-control" id="key-group" placeholder="general" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Key *</label>
                        <input type="text" class="form-control" id="key-key" placeholder="welcome_message" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="key-description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="key-html">
                            <label class="form-check-label" for="key-html">Allow HTML content</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveKey()">Save Key</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Translation Modal -->
<div class="modal fade" id="editTransModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Translation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="edit-trans-form">
                    <input type="hidden" id="edit-trans-group">
                    <input type="hidden" id="edit-trans-key">
                    <div class="mb-3">
                        <label class="form-label">Key</label>
                        <input type="text" class="form-control" id="edit-trans-key-display" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Translation *</label>
                        <textarea class="form-control" id="edit-trans-value" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTranslation()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url", env("API_URL", "http://localhost:8000")) }}/api';
let languages = [];
let groups = [];

document.addEventListener('DOMContentLoaded', function() {
    loadLanguages();
    loadStats();
    loadGroups();
});

function loadLanguages() {
    fetch(`${API_BASE}/admin/languages`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                languages = data.data;
                renderLanguages();
                populateLanguageSelect();
            }
        })
        .catch(err => console.error('Error loading languages:', err));
}

function loadStats() {
    fetch(`${API_BASE}/admin/languages/stats`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('stat-total').textContent = data.data.languages || 0;
                document.getElementById('stat-active').textContent = data.data.active_languages || 0;
                document.getElementById('stat-keys').textContent = data.data.total_keys || 0;
                document.getElementById('stat-unreviewed').textContent = data.data.unreviewed_translations || 0;
            }
        });
}

function loadGroups() {
    fetch(`${API_BASE}/admin/translations/groups`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                groups = data.data;
                const select = document.getElementById('trans-group');
                select.innerHTML = '<option value="">All Groups</option>';
                groups.forEach(g => {
                    select.innerHTML += `<option value="${g}">${g}</option>`;
                });
            }
        });
}

function renderLanguages() {
    const tbody = document.getElementById('languages-tbody');

    if (languages.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No languages found</td></tr>';
        return;
    }

    tbody.innerHTML = languages.map(l => `
        <tr>
            <td class="fs-4">${l.flag_icon || ''}</td>
            <td><code>${l.code}</code></td>
            <td>${l.name}</td>
            <td>${l.native_name}</td>
            <td><span class="badge bg-secondary">${l.direction.toUpperCase()}</span></td>
            <td>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: ${l.translation_progress || 0}%">
                        ${l.translation_progress || 0}%
                    </div>
                </div>
                <small class="text-muted">${l.translated_keys || 0}/${l.total_keys || 0} keys</small>
            </td>
            <td>
                <span class="badge ${l.is_active ? 'bg-success' : 'bg-danger'}">
                    ${l.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>
                ${l.is_default ? '<i class="fas fa-star text-warning" title="Default"></i>' :
                  `<button class="btn btn-sm btn-outline-warning" onclick="setDefault(${l.id})" title="Set Default">
                      <i class="far fa-star"></i>
                   </button>`}
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editLanguage(${l.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${!l.is_default ? `<button class="btn btn-outline-danger" onclick="deleteLanguage(${l.id}, '${l.name}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

function populateLanguageSelect() {
    const select = document.getElementById('trans-language');
    select.innerHTML = languages.map(l =>
        `<option value="${l.id}" ${l.is_default ? 'selected' : ''}>${l.flag_icon || ''} ${l.name}</option>`
    ).join('');

    loadTranslations();
}

function loadTranslations() {
    const languageId = document.getElementById('trans-language').value;
    const group = document.getElementById('trans-group').value;
    const filter = document.getElementById('trans-filter').value;

    if (!languageId) return;

    let url = `${API_BASE}/admin/translations/${languageId}`;
    const params = new URLSearchParams();
    if (group) params.append('group', group);
    if (filter === 'unreviewed') params.append('unreviewed', 'true');
    if (params.toString()) url += '?' + params.toString();

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderTranslations(data.data);
            }
        });
}

function renderTranslations(translations) {
    const tbody = document.getElementById('translations-tbody');

    if (translations.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No translations found</td></tr>';
        return;
    }

    tbody.innerHTML = translations.map(t => `
        <tr>
            <td><span class="badge bg-secondary">${t.group}</span></td>
            <td><code>${t.key}</code></td>
            <td class="text-truncate" style="max-width: 300px;" title="${escapeHtml(t.value)}">${escapeHtml(t.value)}</td>
            <td>
                ${t.is_reviewed ?
                    '<span class="badge bg-success"><i class="fas fa-check"></i> Reviewed</span>' :
                    '<span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>'}
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editTranslation('${t.group}', '${t.key}', '${escapeHtml(t.value)}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${!t.is_reviewed ? `<button class="btn btn-outline-success" onclick="markReviewed(${t.id})" title="Mark Reviewed">
                        <i class="fas fa-check"></i>
                    </button>` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function openCreateModal() {
    document.getElementById('languageModalTitle').textContent = 'Add Language';
    document.getElementById('language-form').reset();
    document.getElementById('lang-id').value = '';
    document.getElementById('lang-active').checked = true;
}

function editLanguage(id) {
    const lang = languages.find(l => l.id === id);
    if (!lang) return;

    document.getElementById('languageModalTitle').textContent = 'Edit Language';
    document.getElementById('lang-id').value = id;
    document.getElementById('lang-code').value = lang.code;
    document.getElementById('lang-locale').value = lang.locale;
    document.getElementById('lang-name').value = lang.name;
    document.getElementById('lang-native-name').value = lang.native_name;
    document.getElementById('lang-flag').value = lang.flag_icon || '';
    document.getElementById('lang-direction').value = lang.direction;
    document.getElementById('lang-active').checked = lang.is_active;

    new bootstrap.Modal(document.getElementById('languageModal')).show();
}

function saveLanguage() {
    const id = document.getElementById('lang-id').value;
    const data = {
        code: document.getElementById('lang-code').value.toLowerCase(),
        locale: document.getElementById('lang-locale').value,
        name: document.getElementById('lang-name').value,
        native_name: document.getElementById('lang-native-name').value,
        flag_icon: document.getElementById('lang-flag').value,
        direction: document.getElementById('lang-direction').value,
        is_active: document.getElementById('lang-active').checked,
    };

    const url = id ? `${API_BASE}/admin/languages/${id}` : `${API_BASE}/admin/languages`;
    const method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('languageModal')).hide();
            loadLanguages();
            loadStats();
            showToast('Language saved successfully', 'success');
        } else {
            showToast(data.error || 'Failed to save language', 'danger');
        }
    });
}

function setDefault(id) {
    if (!confirm('Set this language as the default?')) return;

    fetch(`${API_BASE}/admin/languages/${id}/default`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadLanguages();
                showToast('Default language updated', 'success');
            }
        });
}

function deleteLanguage(id, name) {
    if (!confirm(`Delete language "${name}"? This will also delete all its translations.`)) return;

    fetch(`${API_BASE}/admin/languages/${id}`, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadLanguages();
                loadStats();
                showToast('Language deleted', 'success');
            } else {
                showToast(data.error || 'Failed to delete', 'danger');
            }
        });
}

function showAddKeyModal() {
    document.getElementById('key-form').reset();
    new bootstrap.Modal(document.getElementById('keyModal')).show();
}

function saveKey() {
    const data = {
        group: document.getElementById('key-group').value,
        key: document.getElementById('key-key').value,
        description: document.getElementById('key-description').value,
        is_html: document.getElementById('key-html').checked,
    };

    fetch(`${API_BASE}/admin/translations/keys`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('keyModal')).hide();
            loadGroups();
            loadTranslations();
            loadStats();
            showToast('Translation key added', 'success');
        }
    });
}

function editTranslation(group, key, value) {
    document.getElementById('edit-trans-group').value = group;
    document.getElementById('edit-trans-key').value = key;
    document.getElementById('edit-trans-key-display').value = `${group}.${key}`;
    document.getElementById('edit-trans-value').value = value;

    new bootstrap.Modal(document.getElementById('editTransModal')).show();
}

function saveTranslation() {
    const languageId = document.getElementById('trans-language').value;
    const data = {
        language_id: languageId,
        group: document.getElementById('edit-trans-group').value,
        key: document.getElementById('edit-trans-key').value,
        value: document.getElementById('edit-trans-value').value,
    };

    fetch(`${API_BASE}/admin/translations`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editTransModal')).hide();
            loadTranslations();
            showToast('Translation saved', 'success');
        }
    });
}

function markReviewed(id) {
    fetch(`${API_BASE}/admin/translations/${id}/reviewed`, { method: 'PUT' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadTranslations();
                loadStats();
                showToast('Translation marked as reviewed', 'success');
            }
        });
}

function exportTranslations() {
    const languageId = document.getElementById('trans-language').value;
    const lang = languages.find(l => l.id == languageId);
    if (!lang) return;

    fetch(`${API_BASE}/languages/${lang.locale}/translations`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const blob = new Blob([JSON.stringify(data.translations, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `translations_${lang.code}.json`;
                a.click();
                URL.revokeObjectURL(url);
            }
        });
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    new bootstrap.Toast(toast, { delay: 3000 }).show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}
</script>
@endsection
