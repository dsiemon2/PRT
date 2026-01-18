@extends('layouts.admin')

@section('title', 'Email Templates')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Email Templates</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Email Templates</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#templateModal">
        <i class="bi bi-plus-lg me-1"></i> Create Template
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Template Stats -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-envelope-paper-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Templates</h6>
                                <h3 class="mb-0">{{ count($templates) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Active</h6>
                                <h3 class="mb-0">{{ collect($templates)->where('is_active', true)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-robot text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Transactional</h6>
                                <h3 class="mb-0">{{ collect($templates)->where('category', 'transactional')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-megaphone-fill text-warning fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Marketing</h6>
                                <h3 class="mb-0">{{ collect($templates)->where('category', 'marketing')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="col-12 mb-3">
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="categoryFilter" id="filterAllCat" value="all" checked>
            <label class="btn btn-outline-secondary" for="filterAllCat">All Categories</label>

            <input type="radio" class="btn-check" name="categoryFilter" id="filterTransactional" value="transactional">
            <label class="btn btn-outline-secondary" for="filterTransactional">Transactional</label>

            <input type="radio" class="btn-check" name="categoryFilter" id="filterMarketing" value="marketing">
            <label class="btn btn-outline-secondary" for="filterMarketing">Marketing</label>

            <input type="radio" class="btn-check" name="categoryFilter" id="filterPersonal" value="personal">
            <label class="btn btn-outline-secondary" for="filterPersonal">Personal</label>

            <input type="radio" class="btn-check" name="categoryFilter" id="filterService" value="service">
            <label class="btn btn-outline-secondary" for="filterService">Service</label>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="col-12">
        <div class="row g-4" id="templatesContainer">
            @forelse($templates as $template)
            <div class="col-md-6 col-lg-4 template-card" data-category="{{ $template['category'] ?? '' }}">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge {{ getCategoryBadgeClass($template['category'] ?? '') }} mb-2">
                                {{ ucfirst($template['category'] ?? 'general') }}
                            </span>
                            <h6 class="mb-0">{{ $template['name'] ?? 'Untitled' }}</h6>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active-{{ $template['id'] ?? '' }}"
                                   {{ ($template['is_active'] ?? false) ? 'checked' : '' }}
                                   onchange="toggleTemplateStatus({{ $template['id'] ?? 0 }}, this.checked)">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Subject Line</label>
                            <div class="bg-light rounded p-2">
                                <code class="text-dark small">{{ $template['subject'] ?? 'No subject' }}</code>
                            </div>
                        </div>
                        <div class="template-preview mb-3" style="height: 100px; overflow: hidden;">
                            <label class="form-label text-muted small mb-1">Preview</label>
                            <div class="bg-light rounded p-2 small text-muted" style="max-height: 80px; overflow: hidden;">
                                {!! Str::limit(strip_tags($template['body_html'] ?? ''), 150) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Updated {{ isset($template['updated_at']) ? \Carbon\Carbon::parse($template['updated_at'])->diffForHumans() : 'N/A' }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="previewTemplate({{ json_encode($template) }})" title="Preview">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="editTemplate({{ json_encode($template) }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="duplicateTemplate({{ json_encode($template) }})" title="Duplicate">
                                    <i class="bi bi-copy"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteTemplate({{ $template['id'] ?? 0 }})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-envelope-paper fs-1 text-muted d-block mb-3"></i>
                    <h5>No Email Templates</h5>
                    <p class="text-muted">Create your first email template to start communicating with customers</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#templateModal">
                        <i class="bi bi-plus-lg me-1"></i> Create Template
                    </button>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Variable Reference -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">
                    <i class="bi bi-code-square me-2"></i>
                    Available Template Variables
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <h6 class="text-primary mb-2">Customer</h6>
                        <ul class="list-unstyled small">
                            <li><code>@{{customer.first_name}}</code> - First name</li>
                            <li><code>@{{customer.last_name}}</code> - Last name</li>
                            <li><code>@{{customer.email}}</code> - Email address</li>
                            <li><code>@{{customer.loyalty_tier}}</code> - Loyalty tier</li>
                            <li><code>@{{customer.loyalty_points}}</code> - Points balance</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-success mb-2">Order</h6>
                        <ul class="list-unstyled small">
                            <li><code>@{{order.number}}</code> - Order number</li>
                            <li><code>@{{order.total}}</code> - Order total</li>
                            <li><code>@{{order.status}}</code> - Order status</li>
                            <li><code>@{{order.date}}</code> - Order date</li>
                            <li><code>@{{order.tracking}}</code> - Tracking number</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-warning mb-2">Store</h6>
                        <ul class="list-unstyled small">
                            <li><code>@{{store.name}}</code> - Store name</li>
                            <li><code>@{{store.phone}}</code> - Phone number</li>
                            <li><code>@{{store.email}}</code> - Support email</li>
                            <li><code>@{{store.url}}</code> - Website URL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="templateModalTitle">Create Email Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="templateForm">
                <div class="modal-body">
                    <input type="hidden" id="templateId" name="id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="templateName" name="name" required maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="templateCategory" name="category" required>
                                <option value="transactional">Transactional</option>
                                <option value="marketing">Marketing</option>
                                <option value="personal">Personal</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="templateActive" name="is_active" checked>
                                <label class="form-check-label" for="templateActive">Active</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject Line <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="templateSubject" name="subject" required maxlength="200"
                                   placeholder="e.g., Welcome to Pecos River Traders, @{{customer.first_name}}!">
                        </div>
                        <div class="col-12">
                            <label class="form-label">HTML Body <span class="text-danger">*</span></label>
                            <textarea class="form-control tinymce-email" id="templateBodyHtml" name="body_html" rows="12" required
                                      placeholder="<h1>Hello @{{customer.first_name}}</h1>&#10;<p>Your email content here...</p>"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Plain Text Body</label>
                            <textarea class="form-control font-monospace" id="templateBodyText" name="body_text" rows="6"
                                      placeholder="Plain text version of the email for clients that don't support HTML"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-info" onclick="previewCurrentTemplate()">
                        <i class="bi bi-eye me-1"></i> Preview
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="templateSubmitBtn">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Email Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Subject</label>
                    <div class="bg-light rounded p-2" id="previewSubject"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">HTML Preview</label>
                    <div class="border rounded p-3 bg-white" id="previewBody" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getCategoryBadgeClass($category) {
    return match($category) {
        'transactional' => 'bg-info-subtle text-info',
        'marketing' => 'bg-warning-subtle text-warning',
        'personal' => 'bg-success-subtle text-success',
        'service' => 'bg-danger-subtle text-danger',
        default => 'bg-secondary-subtle text-secondary'
    };
}
@endphp

@push('scripts')
<!-- TinyMCE CDN (self-hosted, no API key required) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';

// TinyMCE initialization for email templates
function initEmailTinyMCE() {
    tinymce.init({
        selector: '.tinymce-email',
        height: 400,
        menubar: false,
        plugins: 'anchor autolink charmap link lists table visualblocks code',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | forecolor backcolor | link | align | numlist bullist | table | removeformat | code',
        content_style: `
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                font-size: 14px;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
            }
            p { margin: 0 0 10px 0; }
            h1, h2, h3 { color: #8B4513; }
            a { color: #8B4513; }
            table { border-collapse: collapse; width: 100%; }
            table td, table th { border: 1px solid #ddd; padding: 8px; }
        `,
        branding: false,
        promotion: false,
        statusbar: true,
        elementpath: false,
        link_default_target: '_blank',
        table_responsive_width: true,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
}

function destroyEmailTinyMCE() {
    var editor = tinymce.get('templateBodyHtml');
    if (editor) {
        editor.destroy();
    }
}

// Initialize TinyMCE when template modal opens
document.getElementById('templateModal').addEventListener('shown.bs.modal', function() {
    setTimeout(function() {
        initEmailTinyMCE();
    }, 100);
});

// Destroy TinyMCE when template modal closes
document.getElementById('templateModal').addEventListener('hidden.bs.modal', function() {
    destroyEmailTinyMCE();
});

// Sample data for preview
const sampleData = {
    customer: {
        first_name: 'John',
        last_name: 'Doe',
        email: 'john@example.com',
        loyalty_tier: 'Gold',
        loyalty_points: 1250
    },
    order: {
        number: 'ORD-12345',
        total: '$299.99',
        status: 'Shipped',
        date: 'November 29, 2025',
        tracking: 'TRACK123456789'
    },
    store: {
        name: 'Pecos River Traders',
        phone: '(555) 123-4567',
        email: 'support@pecosrivertraders.com',
        url: 'https://pecosrivertraders.com'
    }
};

// Replace template variables with sample data
function replaceVariables(text) {
    return text
        .replace(/@?\{\{customer\.(\w+)\}\}/g, (match, key) => sampleData.customer[key] || match)
        .replace(/@?\{\{order\.(\w+)\}\}/g, (match, key) => sampleData.order[key] || match)
        .replace(/@?\{\{store\.(\w+)\}\}/g, (match, key) => sampleData.store[key] || match);
}

// Category filter
document.querySelectorAll('input[name="categoryFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const filter = this.value;
        document.querySelectorAll('.template-card').forEach(card => {
            const category = card.dataset.category;
            card.style.display = (filter === 'all' || category === filter) ? '' : 'none';
        });
    });
});

// Preview template
function previewTemplate(template) {
    document.getElementById('previewSubject').textContent = replaceVariables(template.subject || '');
    document.getElementById('previewBody').innerHTML = replaceVariables(template.body_html || '');
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Preview current form
function previewCurrentTemplate() {
    const subject = document.getElementById('templateSubject').value;
    // Get content from TinyMCE if available
    var editor = tinymce.get('templateBodyHtml');
    const body = editor ? editor.getContent() : document.getElementById('templateBodyHtml').value;
    document.getElementById('previewSubject').textContent = replaceVariables(subject);
    document.getElementById('previewBody').innerHTML = replaceVariables(body);
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Edit template
function editTemplate(template) {
    document.getElementById('templateModalTitle').textContent = 'Edit Email Template';
    document.getElementById('templateSubmitBtn').textContent = 'Update Template';
    document.getElementById('templateId').value = template.id;
    document.getElementById('templateName').value = template.name;
    document.getElementById('templateCategory').value = template.category;
    document.getElementById('templateActive').checked = template.is_active;
    document.getElementById('templateSubject').value = template.subject;
    document.getElementById('templateBodyHtml').value = template.body_html || '';
    document.getElementById('templateBodyText').value = template.body_text || '';

    var modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();

    // Set TinyMCE content after editor initializes
    setTimeout(function() {
        var editor = tinymce.get('templateBodyHtml');
        if (editor) {
            editor.setContent(template.body_html || '');
        }
    }, 200);
}

// Duplicate template
function duplicateTemplate(template) {
    document.getElementById('templateModalTitle').textContent = 'Create Email Template';
    document.getElementById('templateSubmitBtn').textContent = 'Create Template';
    document.getElementById('templateId').value = '';
    document.getElementById('templateName').value = template.name + ' (Copy)';
    document.getElementById('templateCategory').value = template.category;
    document.getElementById('templateActive').checked = true;
    document.getElementById('templateSubject').value = template.subject;
    document.getElementById('templateBodyHtml').value = template.body_html || '';
    document.getElementById('templateBodyText').value = template.body_text || '';

    var modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();

    // Set TinyMCE content after editor initializes
    setTimeout(function() {
        var editor = tinymce.get('templateBodyHtml');
        if (editor) {
            editor.setContent(template.body_html || '');
        }
    }, 200);
}

// Reset modal
document.getElementById('templateModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('templateModalTitle').textContent = 'Create Email Template';
    document.getElementById('templateSubmitBtn').textContent = 'Create Template';
    document.getElementById('templateForm').reset();
    document.getElementById('templateId').value = '';
});

// Form submission
document.getElementById('templateForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const templateId = document.getElementById('templateId').value;
    const isEdit = !!templateId;

    // Get content from TinyMCE if available
    var editor = tinymce.get('templateBodyHtml');
    var bodyHtmlContent = editor ? editor.getContent() : document.getElementById('templateBodyHtml').value;

    const data = {
        name: document.getElementById('templateName').value,
        category: document.getElementById('templateCategory').value,
        is_active: document.getElementById('templateActive').checked,
        subject: document.getElementById('templateSubject').value,
        body_html: bodyHtmlContent,
        body_text: document.getElementById('templateBodyText').value
    };

    try {
        const url = isEdit
            ? `${API_BASE}/admin/crm/email-templates/${templateId}`
            : `${API_BASE}/admin/crm/email-templates`;

        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            showToast(isEdit ? 'Template updated successfully' : 'Template created successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            const error = await response.json();
            showToast(error.message || 'Failed to save template', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
});

// Toggle template status
async function toggleTemplateStatus(id, isActive) {
    try {
        const response = await fetch(`${API_BASE}/admin/crm/email-templates/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: isActive })
        });

        if (response.ok) {
            showToast(`Template ${isActive ? 'activated' : 'deactivated'}`, 'success');
        } else {
            showToast('Failed to update template status', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// Delete template
async function deleteTemplate(id) {
    if (!confirm('Are you sure you want to delete this template?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/crm/email-templates/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            showToast('Template deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to delete template', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>
@endpush
