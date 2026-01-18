@extends('layouts.admin')

@section('title', 'Section Management')

@push('styles')
<style>
.section-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: grab;
    transition: all 0.2s;
}
.section-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.section-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
}
.section-card .card-content {
    flex: 1;
    padding-left: 15px;
    min-width: 0;
}
.section-card .card-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}
.drag-handle {
    cursor: grab;
    color: #adb5bd;
    padding: 10px;
}
.drag-handle:hover {
    color: #6c757d;
}
.add-card {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #fafafa;
}
.add-card:hover {
    border-color: #8B4513;
    background: #fdf8f4;
}
.add-card i {
    font-size: 2rem;
    color: #8B4513;
}
.bg-style-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}
.bg-style-white { background: #f8f9fa; color: #333; border: 1px solid #dee2e6; }
.bg-style-cream { background: #fdf8f4; color: #8B4513; border: 1px solid #e6ddd5; }
.bg-style-gradient { background: linear-gradient(135deg, #fdf8f4 0%, #fff5eb 100%); color: #8B4513; border: 1px solid #e6ddd5; }
.bg-style-dark { background: #2c3e50; color: white; }
.bg-style-custom { background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); color: white; }
.content-preview {
    max-height: 60px;
    overflow: hidden;
    font-size: 12px;
    color: #6c757d;
    line-height: 1.4;
}
.visibility-badge {
    font-size: 11px;
    padding: 3px 8px;
}
.section-title {
    font-weight: 600;
    color: #333;
}
.admin-label {
    font-size: 12px;
    color: #6c757d;
}
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Section Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Section Management</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
    <div>
        <strong>Custom Homepage Sections</strong><br>
        Create and manage promotional sections, announcements, and custom content blocks for your homepage.
        Sections appear after Featured Categories/Products on the homepage.
    </div>
</div>

<!-- Sections List -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-layers me-2"></i>Homepage Sections</h5>
        <span class="badge bg-secondary" id="sectionCount">0 sections</span>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Drag and drop to reorder sections. Each section can be individually enabled or disabled.</p>

        <div id="sectionsList">
            <!-- Sections will be loaded here -->
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Add New Button -->
        <div class="add-card" onclick="showAddModal()">
            <i class="bi bi-plus-circle"></i>
            <div class="mt-2 text-muted">Add New Section</div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Title <small class="text-muted">(optional - displayed on frontend)</small></label>
                            <input type="text" class="form-control" id="titleInput" placeholder="e.g., Holiday Sale - Up to 40% Off!">
                            <small class="text-muted">Leave empty if you don't want a visible heading</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Admin Label <small class="text-muted">(for your reference)</small></label>
                            <input type="text" class="form-control" id="adminLabelInput" placeholder="e.g., Holiday Promo Banner">
                            <small class="text-muted">Only visible in admin panel for identification</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Background Style</label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="radio" class="btn-check" name="bgStyle" id="bgWhite" value="white" checked>
                            <label class="btn btn-outline-secondary w-100" for="bgWhite">
                                <i class="bi bi-square me-1"></i> White
                            </label>
                        </div>
                        <div class="col">
                            <input type="radio" class="btn-check" name="bgStyle" id="bgCream" value="cream">
                            <label class="btn btn-outline-secondary w-100" for="bgCream" style="background: #fdf8f4;">
                                <i class="bi bi-square-fill me-1" style="color: #d4c4b5;"></i> Cream
                            </label>
                        </div>
                        <div class="col">
                            <input type="radio" class="btn-check" name="bgStyle" id="bgGradient" value="gradient">
                            <label class="btn btn-outline-secondary w-100" for="bgGradient" style="background: linear-gradient(135deg, #fdf8f4 0%, #fff5eb 100%);">
                                <i class="bi bi-palette me-1"></i> Gradient
                            </label>
                        </div>
                        <div class="col">
                            <input type="radio" class="btn-check" name="bgStyle" id="bgDark" value="dark">
                            <label class="btn btn-outline-secondary w-100" for="bgDark" style="background: #2c3e50; color: white;">
                                <i class="bi bi-moon-fill me-1"></i> Dark
                            </label>
                        </div>
                        <div class="col">
                            <input type="radio" class="btn-check" name="bgStyle" id="bgCustom" value="custom">
                            <label class="btn btn-outline-secondary w-100" for="bgCustom">
                                <i class="bi bi-brush me-1"></i> Custom
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3" id="customColorSection" style="display: none;">
                    <label class="form-label">Custom Background Color</label>
                    <div class="input-group" style="max-width: 200px;">
                        <input type="color" class="form-control form-control-color" id="customColorInput" value="#990000">
                        <input type="text" class="form-control" id="customColorText" value="#990000" maxlength="7">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea id="contentEditor" class="form-control"></textarea>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="isVisibleCheck" checked>
                    <label class="form-check-label" for="isVisibleCheck">
                        <strong>Visible on Homepage</strong>
                        <small class="text-muted d-block">Uncheck to hide this section without deleting it</small>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveSection()">
                    <i class="bi bi-check-lg me-1"></i> Save Section
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Section Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="previewContainer">
                    <!-- Preview content will be rendered here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let sections = [];
let sectionModal, previewModal;
let editor = null;

document.addEventListener('DOMContentLoaded', function() {
    sectionModal = new bootstrap.Modal(document.getElementById('sectionModal'));
    previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

    loadSections();
    initTinyMCE();
    initSortable();

    // Background style change handler
    document.querySelectorAll('input[name="bgStyle"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('customColorSection').style.display =
                this.value === 'custom' ? 'block' : 'none';
        });
    });

    // Sync color inputs
    document.getElementById('customColorInput').addEventListener('input', function() {
        document.getElementById('customColorText').value = this.value;
    });
    document.getElementById('customColorText').addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            document.getElementById('customColorInput').value = this.value;
        }
    });
});

function initTinyMCE() {
    tinymce.init({
        selector: '#contentEditor',
        height: 400,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'link image media | code fullscreen | removeformat help',
        // Load Bootstrap CSS and Icons for accurate preview in editor
        content_css: [
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css'
        ],
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; padding: 15px; }',
        setup: function(ed) {
            editor = ed;
        }
    });
}

function initSortable() {
    const container = document.getElementById('sectionsList');
    new Sortable(container, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'dragging',
        onEnd: function() {
            saveOrder();
        }
    });
}

async function loadSections() {
    try {
        const response = await fetch(`${API_BASE}/admin/sections`);
        const data = await response.json();

        if (data.success) {
            sections = data.data;
            renderSections();
            updateCount();
        }
    } catch (error) {
        console.error('Error loading sections:', error);
        document.getElementById('sectionsList').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error loading sections. Please refresh the page.
            </div>
        `;
    }
}

function renderSections() {
    const container = document.getElementById('sectionsList');

    if (sections.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="bi bi-layers display-4 d-block mb-2"></i>
                No sections yet. Click "Add New Section" to create one.
            </div>
        `;
        return;
    }

    container.innerHTML = sections.map(section => {
        const bgStyleClass = `bg-style-${section.background_style}`;
        const isVisible = section.is_visible == 1 || section.is_visible === true;

        // Strip HTML for preview
        const contentPreview = section.content.replace(/<[^>]*>/g, '').substring(0, 150);

        return `
            <div class="section-card d-flex align-items-center ${!isVisible ? 'opacity-50' : ''}" data-id="${section.id}">
                <div class="drag-handle">
                    <i class="bi bi-grip-vertical fs-4"></i>
                </div>
                <div class="card-content">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        ${section.title ? `<span class="section-title">${escapeHtml(section.title)}</span>` : ''}
                        ${section.admin_label ? `<span class="admin-label">[${escapeHtml(section.admin_label)}]</span>` : ''}
                        <span class="bg-style-badge ${bgStyleClass}">${section.background_style}</span>
                        ${isVisible
                            ? '<span class="badge bg-success visibility-badge"><i class="bi bi-eye"></i> Visible</span>'
                            : '<span class="badge bg-secondary visibility-badge"><i class="bi bi-eye-slash"></i> Hidden</span>'
                        }
                    </div>
                    <div class="content-preview">${escapeHtml(contentPreview)}${contentPreview.length >= 150 ? '...' : ''}</div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline-info" onclick="previewSection(${section.id})" title="Preview">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="editSection(${section.id})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-${isVisible ? 'warning' : 'success'}" onclick="toggleVisibility(${section.id})" title="${isVisible ? 'Hide' : 'Show'}">
                        <i class="bi bi-${isVisible ? 'eye-slash' : 'eye'}"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSection(${section.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function updateCount() {
    const visible = sections.filter(s => s.is_visible == 1 || s.is_visible === true).length;
    document.getElementById('sectionCount').textContent =
        `${sections.length} section${sections.length !== 1 ? 's' : ''} (${visible} visible)`;
}

function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Section';
    document.getElementById('editId').value = '';
    document.getElementById('titleInput').value = '';
    document.getElementById('adminLabelInput').value = '';
    document.getElementById('bgWhite').checked = true;
    document.getElementById('customColorSection').style.display = 'none';
    document.getElementById('customColorInput').value = '#990000';
    document.getElementById('customColorText').value = '#990000';
    document.getElementById('isVisibleCheck').checked = true;

    if (editor) {
        editor.setContent('');
    }

    sectionModal.show();
}

function editSection(id) {
    const section = sections.find(s => s.id === id);
    if (!section) return;

    document.getElementById('modalTitle').textContent = 'Edit Section';
    document.getElementById('editId').value = id;
    document.getElementById('titleInput').value = section.title || '';
    document.getElementById('adminLabelInput').value = section.admin_label || '';

    // Set background style
    const bgRadio = document.querySelector(`input[name="bgStyle"][value="${section.background_style}"]`);
    if (bgRadio) bgRadio.checked = true;

    // Show/hide custom color section
    document.getElementById('customColorSection').style.display =
        section.background_style === 'custom' ? 'block' : 'none';

    if (section.background_color) {
        document.getElementById('customColorInput').value = section.background_color;
        document.getElementById('customColorText').value = section.background_color;
    }

    document.getElementById('isVisibleCheck').checked =
        section.is_visible == 1 || section.is_visible === true;

    if (editor) {
        editor.setContent(section.content || '');
    }

    sectionModal.show();
}

async function saveSection() {
    const editId = document.getElementById('editId').value;
    const title = document.getElementById('titleInput').value.trim();
    const adminLabel = document.getElementById('adminLabelInput').value.trim();
    const bgStyle = document.querySelector('input[name="bgStyle"]:checked').value;
    const bgColor = bgStyle === 'custom' ? document.getElementById('customColorInput').value : null;
    const content = editor ? editor.getContent() : '';
    const isVisible = document.getElementById('isVisibleCheck').checked;

    if (!content.trim()) {
        showToast('Please enter content for the section', 'error');
        return;
    }

    const body = {
        title: title || null,
        admin_label: adminLabel || null,
        content: content,
        background_style: bgStyle,
        background_color: bgColor,
        is_visible: isVisible
    };

    try {
        const url = editId
            ? `${API_BASE}/admin/sections/${editId}`
            : `${API_BASE}/admin/sections`;

        const response = await fetch(url, {
            method: editId ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            sectionModal.hide();
            loadSections();
        } else {
            showToast(data.message || 'Error saving section', 'error');
        }
    } catch (error) {
        console.error('Error saving section:', error);
        showToast('Error saving section', 'error');
    }
}

async function toggleVisibility(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/sections/${id}/toggle`, {
            method: 'PUT'
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            loadSections();
        } else {
            showToast(data.message || 'Error toggling visibility', 'error');
        }
    } catch (error) {
        console.error('Error toggling visibility:', error);
        showToast('Error toggling visibility', 'error');
    }
}

async function deleteSection(id) {
    if (!confirm('Are you sure you want to delete this section? This action cannot be undone.')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/sections/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            showToast('Section deleted', 'success');
            loadSections();
        } else {
            showToast(data.message || 'Error deleting section', 'error');
        }
    } catch (error) {
        console.error('Error deleting section:', error);
        showToast('Error deleting section', 'error');
    }
}

async function saveOrder() {
    const cards = document.querySelectorAll('#sectionsList .section-card');
    const order = Array.from(cards).map(card => parseInt(card.dataset.id));

    try {
        const response = await fetch(`${API_BASE}/admin/sections/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Order updated', 'success');
        }
    } catch (error) {
        console.error('Error saving order:', error);
    }
}

function previewSection(id) {
    const section = sections.find(s => s.id === id);
    if (!section) return;

    let bgStyle = '';
    switch (section.background_style) {
        case 'white':
            bgStyle = 'background: white;';
            break;
        case 'cream':
            bgStyle = 'background: #fdf8f4;';
            break;
        case 'gradient':
            bgStyle = 'background: linear-gradient(135deg, #fdf8f4 0%, #fff5eb 100%);';
            break;
        case 'dark':
            bgStyle = 'background: #2c3e50; color: white;';
            break;
        case 'custom':
            bgStyle = `background: ${section.background_color || '#990000'}; color: white;`;
            break;
    }

    const previewHtml = `
        <section style="${bgStyle} padding: 3rem 0;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
                ${section.title ? `<h2 style="text-align: center; margin-bottom: 1.5rem;">${escapeHtml(section.title)}</h2>` : ''}
                ${section.content}
            </div>
        </section>
    `;

    document.getElementById('previewContainer').innerHTML = previewHtml;
    previewModal.show();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showToast(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle';

    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `<i class="bi bi-${icon} me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
