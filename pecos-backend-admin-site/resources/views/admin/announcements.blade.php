@extends('layouts.admin')

@section('title', 'Announcements')

@push('styles')
<style>
/* Row selection styles for list-group */
.list-group-item.row-selected {
    background-color: #e3f2fd !important;
}
.list-group-item {
    cursor: pointer;
}
.list-group-item:hover:not(.row-selected) {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Announcement Bar</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Announcements</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-prt" onclick="showAddAnnouncementModal()">
        <i class="bi bi-plus-lg me-1"></i> Add Announcement
    </button>
</div>

<!-- Global Settings Card -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Announcement Bar Settings</h5>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="announcementEnabled" onchange="toggleAnnouncementBar()">
            <label class="form-check-label" for="announcementEnabled">Enable</label>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Rotation Speed (seconds)</label>
                <input type="number" class="form-control" id="rotationSpeed" min="1" max="30" value="5">
            </div>
            <div class="col-md-4">
                <label class="form-label">Animation Style</label>
                <select class="form-select" id="animationStyle">
                    <option value="fade">Fade</option>
                    <option value="slide">Slide</option>
                    <option value="none">None</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="allowDismiss" checked>
                    <label class="form-check-label" for="allowDismiss">Allow visitors to dismiss</label>
                </div>
            </div>
        </div>
        <button class="btn btn-prt mt-3" onclick="saveAnnouncementSettings()">
            <i class="bi bi-check-lg me-1"></i> Save Settings
        </button>
    </div>
</div>

<!-- Live Preview -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-eye me-2"></i>Live Preview</h5>
    </div>
    <div class="card-body p-0">
        <div id="announcementPreview" class="text-center py-2" style="background-color: #C41E3A; color: #FFFFFF;">
            <small><i class="bi bi-megaphone me-1"></i> Your announcement will appear here</small>
        </div>
    </div>
</div>

<!-- Announcements List -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Announcements</h5>
        <small class="text-muted">Drag to reorder</small>
    </div>
    <div class="card-body">
        <div id="announcementsList">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalTitle">Add Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="announcementForm">
                    <input type="hidden" id="announcementId">

                    <div class="mb-3">
                        <label class="form-label">Announcement Text <span class="text-danger">*</span></label>
                        <textarea class="form-control tinymce-announcement" id="announcementText" rows="2" required
                            placeholder="Free shipping on orders over $50! | Call us: 717-914-8124"></textarea>
                        <small class="text-muted">Use bold, italic, and links for rich formatting</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Icon</label>
                            <select class="form-select" id="announcementIcon">
                                <option value="">No Icon</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <select class="form-select" id="announcementPosition">
                                <option value="left">Left</option>
                                <option value="center" selected>Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Link URL (optional)</label>
                            <input type="url" class="form-control" id="announcementLinkUrl" placeholder="https://example.com/sale">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link Text</label>
                            <input type="text" class="form-control" id="announcementLinkText" placeholder="Shop Now">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="announcementBgColor" value="#C41E3A">
                                <input type="text" class="form-control" id="announcementBgColorText" value="#C41E3A">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Text Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="announcementTextColor" value="#FFFFFF">
                                <input type="text" class="form-control" id="announcementTextColorText" value="#FFFFFF">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date (optional)</label>
                            <input type="datetime-local" class="form-control" id="announcementStartDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date (optional)</label>
                            <input type="datetime-local" class="form-control" id="announcementEndDate">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="announcementActive" checked>
                        <label class="form-check-label" for="announcementActive">Active</label>
                    </div>

                    <!-- Preview -->
                    <div class="mt-4">
                        <label class="form-label">Preview</label>
                        <div id="modalPreview" class="text-center py-2 rounded" style="background-color: #C41E3A; color: #FFFFFF;">
                            <small>Your announcement preview</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveAnnouncement()">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<!-- TinyMCE CDN (self-hosted, no API key required) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let announcementModal;
let iconsList = [];
let announcementEditor = null;

// Sample announcements for demo
const sampleAnnouncements = [
    {
        id: 1,
        text: '<strong>FREE SHIPPING</strong> on all orders over $50! Limited time offer.',
        icon: 'bi bi-truck',
        position: 'center',
        link_url: '/shop',
        link_text: 'Shop Now',
        bg_color: '#C41E3A',
        text_color: '#FFFFFF',
        is_active: true,
        display_order: 0,
        start_date: null,
        end_date: null
    },
    {
        id: 2,
        text: 'New arrivals just dropped! Check out our latest <em>Western Collection</em>.',
        icon: 'bi bi-star-fill',
        position: 'center',
        link_url: '/collections/new-arrivals',
        link_text: 'View Collection',
        bg_color: '#8B4513',
        text_color: '#FFFFFF',
        is_active: true,
        display_order: 1,
        start_date: null,
        end_date: null
    },
    {
        id: 3,
        text: 'Holiday Sale! <strong>20% OFF</strong> sitewide with code HOLIDAY20',
        icon: 'bi bi-gift',
        position: 'center',
        link_url: null,
        link_text: null,
        bg_color: '#228B22',
        text_color: '#FFFFFF',
        is_active: true,
        display_order: 2,
        start_date: null,
        end_date: null
    },
    {
        id: 4,
        text: 'Questions? Call us at <strong>717-914-8124</strong> | Mon-Fri 9am-5pm EST',
        icon: 'bi bi-telephone',
        position: 'center',
        link_url: null,
        link_text: null,
        bg_color: '#1E3A5F',
        text_color: '#FFFFFF',
        is_active: false,
        display_order: 3,
        start_date: null,
        end_date: null
    },
    {
        id: 5,
        text: 'Sign up for our newsletter and get <strong>15% OFF</strong> your first order!',
        icon: 'bi bi-envelope',
        position: 'center',
        link_url: '/newsletter',
        link_text: 'Subscribe',
        bg_color: '#6B21A8',
        text_color: '#FFFFFF',
        is_active: false,
        display_order: 4,
        start_date: null,
        end_date: null
    }
];

// Row selection function
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('.list-group-item');
    if (!row) return;

    // Don't highlight if clicking on interactive elements
    if (target.tagName === 'BUTTON' || target.tagName === 'A' ||
        target.tagName === 'I' || target.closest('button') ||
        target.closest('a') || target.closest('.btn-group')) {
        return;
    }

    // Remove selection from all other rows
    document.querySelectorAll('.list-group-item.row-selected').forEach(function(r) {
        r.classList.remove('row-selected');
    });

    // Add selection to clicked row
    row.classList.add('row-selected');
}

// TinyMCE initialization for announcement text
function initAnnouncementTinyMCE() {
    // Destroy existing instance if any
    if (tinymce.get('announcementText')) {
        tinymce.get('announcementText').destroy();
    }

    tinymce.init({
        selector: '#announcementText',
        height: 120,
        menubar: false,
        inline: false,
        plugins: 'link',
        toolbar: 'bold italic underline | link | removeformat',
        content_style: `
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
                font-size: 14px;
                line-height: 1.4;
                margin: 8px;
            }
            p { margin: 0; }
        `,
        branding: false,
        promotion: false,
        statusbar: false,
        placeholder: 'Free shipping on orders over $50!',
        setup: function(editor) {
            announcementEditor = editor;
            editor.on('change keyup', function() {
                editor.save();
                updateModalPreview();
            });
        }
    });
}

function destroyAnnouncementTinyMCE() {
    if (tinymce.get('announcementText')) {
        tinymce.get('announcementText').destroy();
    }
    announcementEditor = null;
}

function getAnnouncementContent() {
    var editor = tinymce.get('announcementText');
    return editor ? editor.getContent() : document.getElementById('announcementText').value;
}

function setAnnouncementContent(content) {
    var editor = tinymce.get('announcementText');
    if (editor) {
        editor.setContent(content || '');
    } else {
        document.getElementById('announcementText').value = content || '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
    loadIcons();
    loadAnnouncements();
    setupColorSync();
    setupPreviewUpdate();

    // Initialize TinyMCE when modal opens
    document.getElementById('announcementModal').addEventListener('shown.bs.modal', function() {
        setTimeout(initAnnouncementTinyMCE, 100);
    });

    // Destroy TinyMCE when modal closes
    document.getElementById('announcementModal').addEventListener('hidden.bs.modal', function() {
        destroyAnnouncementTinyMCE();
    });
});

function setupColorSync() {
    // Sync color pickers with text inputs
    ['Bg', 'Text'].forEach(type => {
        const colorInput = document.getElementById(`announcement${type}Color`);
        const textInput = document.getElementById(`announcement${type}ColorText`);

        colorInput.addEventListener('input', () => {
            textInput.value = colorInput.value;
            updateModalPreview();
        });

        textInput.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
                colorInput.value = textInput.value;
                updateModalPreview();
            }
        });
    });
}

function setupPreviewUpdate() {
    ['announcementText', 'announcementIcon', 'announcementLinkText'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateModalPreview);
    });
}

function updateModalPreview() {
    const text = getAnnouncementContent() || 'Your announcement preview';
    const icon = document.getElementById('announcementIcon').value;
    const linkText = document.getElementById('announcementLinkText').value;
    const bgColor = document.getElementById('announcementBgColor').value;
    const textColor = document.getElementById('announcementTextColor').value;

    const preview = document.getElementById('modalPreview');
    preview.style.backgroundColor = bgColor;
    preview.style.color = textColor;

    let html = '<small>';
    if (icon) html += `<i class="${icon} me-1"></i> `;
    html += text;
    if (linkText) html += ` <a href="#" style="color: ${textColor}; text-decoration: underline;">${linkText}</a>`;
    html += '</small>';

    preview.innerHTML = html;
}

async function loadIcons() {
    try {
        const response = await fetch(`${API_BASE}/announcements/icons`);
        const data = await response.json();
        iconsList = data.data;

        const select = document.getElementById('announcementIcon');
        select.innerHTML = '<option value="">No Icon</option>';
        iconsList.forEach(icon => {
            select.innerHTML += `<option value="${icon.value}"><i class="${icon.value}"></i> ${icon.label}</option>`;
        });
    } catch (error) {
        console.error('Error loading icons:', error);
    }
}

async function loadAnnouncements() {
    try {
        const response = await fetch(`${API_BASE}/admin/announcements`);
        const data = await response.json();

        // Update settings
        if (data.settings) {
            document.getElementById('announcementEnabled').checked = data.settings.enabled;
            document.getElementById('rotationSpeed').value = data.settings.rotation_speed;
            document.getElementById('animationStyle').value = data.settings.animation;
            document.getElementById('allowDismiss').checked = data.settings.allow_dismiss;
        }

        // Use sample data if API returns empty
        const announcements = (data.data && data.data.length > 0) ? data.data : sampleAnnouncements;
        renderAnnouncementsList(announcements);
        updateLivePreview(announcements);
    } catch (error) {
        console.error('Error loading announcements:', error);
        // Use sample data on error
        renderAnnouncementsList(sampleAnnouncements);
        updateLivePreview(sampleAnnouncements);
    }
}

function renderAnnouncementsList(announcements) {
    const container = document.getElementById('announcementsList');

    if (!announcements || announcements.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="bi bi-megaphone" style="font-size: 3rem;"></i>
                <p class="mt-2">No announcements yet. Click "Add Announcement" to create one.</p>
            </div>
        `;
        return;
    }

    let html = '<div class="list-group" id="sortableAnnouncements">';
    announcements.forEach((ann, index) => {
        const statusBadge = ann.is_active
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';

        const iconHtml = ann.icon ? `<i class="${ann.icon} me-1"></i>` : '';

        html += `
            <div class="list-group-item" data-id="${ann.id}" onclick="highlightRow(event)">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-grip-vertical me-3 text-muted" style="cursor: grab;"></i>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge" style="background-color: ${ann.bg_color}; color: ${ann.text_color};">
                                    ${iconHtml}Preview
                                </span>
                                ${statusBadge}
                                <small class="text-muted">Position: ${ann.position}</small>
                            </div>
                            <div>${ann.text}</div>
                            ${ann.link_url ? `<small class="text-muted"><i class="bi bi-link-45deg"></i> ${ann.link_url}</small>` : ''}
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-warning" onclick="editAnnouncement(${ann.id})" title="Edit this announcement">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteAnnouncement(${ann.id})" title="Delete this announcement">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    container.innerHTML = html;

    // Initialize sortable
    new Sortable(document.getElementById('sortableAnnouncements'), {
        animation: 150,
        handle: '.bi-grip-vertical',
        onEnd: function(evt) {
            saveOrder();
        }
    });
}

function updateLivePreview(announcements) {
    const preview = document.getElementById('announcementPreview');
    const activeAnnouncements = announcements.filter(a => a.is_active);

    if (activeAnnouncements.length === 0) {
        preview.style.backgroundColor = '#C41E3A';
        preview.style.color = '#FFFFFF';
        preview.innerHTML = '<small><i class="bi bi-megaphone me-1"></i> Your announcement will appear here</small>';
        return;
    }

    const first = activeAnnouncements[0];
    preview.style.backgroundColor = first.bg_color;
    preview.style.color = first.text_color;

    let html = '<small>';
    if (first.icon) html += `<i class="${first.icon} me-1"></i>`;
    html += first.text;
    if (first.link_url && first.link_text) {
        html += ` <a href="${first.link_url}" style="color: ${first.text_color}; text-decoration: underline;">${first.link_text}</a>`;
    }
    html += '</small>';

    preview.innerHTML = html;
}

function showAddAnnouncementModal() {
    document.getElementById('announcementModalTitle').textContent = 'Add Announcement';
    document.getElementById('announcementForm').reset();
    document.getElementById('announcementId').value = '';
    document.getElementById('announcementBgColor').value = '#C41E3A';
    document.getElementById('announcementBgColorText').value = '#C41E3A';
    document.getElementById('announcementTextColor').value = '#FFFFFF';
    document.getElementById('announcementTextColorText').value = '#FFFFFF';
    document.getElementById('announcementActive').checked = true;
    updateModalPreview();
    announcementModal.show();
}

async function editAnnouncement(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/announcements`);
        const data = await response.json();
        const ann = data.data.find(a => a.id === id);

        if (!ann) return;

        document.getElementById('announcementModalTitle').textContent = 'Edit Announcement';
        document.getElementById('announcementId').value = ann.id;
        document.getElementById('announcementText').value = ann.text;
        document.getElementById('announcementIcon').value = ann.icon || '';
        document.getElementById('announcementPosition').value = ann.position;
        document.getElementById('announcementLinkUrl').value = ann.link_url || '';
        document.getElementById('announcementLinkText').value = ann.link_text || '';
        document.getElementById('announcementBgColor').value = ann.bg_color;
        document.getElementById('announcementBgColorText').value = ann.bg_color;
        document.getElementById('announcementTextColor').value = ann.text_color;
        document.getElementById('announcementTextColorText').value = ann.text_color;
        document.getElementById('announcementActive').checked = ann.is_active;

        if (ann.start_date) {
            document.getElementById('announcementStartDate').value = ann.start_date.slice(0, 16);
        }
        if (ann.end_date) {
            document.getElementById('announcementEndDate').value = ann.end_date.slice(0, 16);
        }

        announcementModal.show();

        // Set TinyMCE content after editor initializes
        setTimeout(function() {
            setAnnouncementContent(ann.text);
            updateModalPreview();
        }, 200);
    } catch (error) {
        console.error('Error loading announcement:', error);
        alert('Error loading announcement');
    }
}

async function saveAnnouncement() {
    const id = document.getElementById('announcementId').value;
    const data = {
        text: getAnnouncementContent(),
        icon: document.getElementById('announcementIcon').value || null,
        position: document.getElementById('announcementPosition').value,
        link_url: document.getElementById('announcementLinkUrl').value || null,
        link_text: document.getElementById('announcementLinkText').value || null,
        bg_color: document.getElementById('announcementBgColor').value,
        text_color: document.getElementById('announcementTextColor').value,
        is_active: document.getElementById('announcementActive').checked,
        start_date: document.getElementById('announcementStartDate').value || null,
        end_date: document.getElementById('announcementEndDate').value || null,
    };

    try {
        const url = id ? `${API_BASE}/admin/announcements/${id}` : `${API_BASE}/admin/announcements`;
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            announcementModal.hide();
            loadAnnouncements();
            showToast('Announcement saved successfully', 'success');
        } else {
            const error = await response.json();
            alert('Error: ' + (error.message || 'Failed to save'));
        }
    } catch (error) {
        console.error('Error saving announcement:', error);
        alert('Error saving announcement');
    }
}

async function deleteAnnouncement(id) {
    if (!confirm('Are you sure you want to delete this announcement?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/announcements/${id}`, {
            method: 'DELETE'
        });

        if (response.ok) {
            loadAnnouncements();
            showToast('Announcement deleted', 'success');
        }
    } catch (error) {
        console.error('Error deleting announcement:', error);
        alert('Error deleting announcement');
    }
}

async function saveOrder() {
    const items = document.querySelectorAll('#sortableAnnouncements .list-group-item');
    const order = Array.from(items).map((item, index) => ({
        id: parseInt(item.dataset.id),
        display_order: index
    }));

    try {
        await fetch(`${API_BASE}/admin/announcements/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });
    } catch (error) {
        console.error('Error saving order:', error);
    }
}

async function toggleAnnouncementBar() {
    await saveAnnouncementSettings();
}

async function saveAnnouncementSettings() {
    const data = {
        enabled: document.getElementById('announcementEnabled').checked,
        rotation_speed: parseInt(document.getElementById('rotationSpeed').value),
        animation: document.getElementById('animationStyle').value,
        allow_dismiss: document.getElementById('allowDismiss').checked
    };

    try {
        const response = await fetch(`${API_BASE}/admin/announcements/settings`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            showToast('Settings saved', 'success');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        alert('Error saving settings');
    }
}

function showToast(message, type = 'info') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    toast.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
