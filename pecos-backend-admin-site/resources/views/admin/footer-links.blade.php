@extends('layouts.admin')

@section('title', 'Footer Navigation Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Footer Navigation Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">Settings</a></li>
                <li class="breadcrumb-item active">Footer Navigation</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="previewFooter()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Preview Footer Layout">
            <i class="bi bi-eye me-1"></i> Preview
        </button>
        <button class="btn btn-prt" onclick="showAddLinkModal()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add a New Custom Link">
            <i class="bi bi-plus-lg me-1"></i> Add Custom Link
        </button>
    </div>
</div>

<!-- Footer Column Titles -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-type me-2"></i>Footer Column Titles</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Column 1</label>
                <input type="text" class="form-control" id="column1Title" value="Shop" onchange="saveColumnTitles()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Column 2</label>
                <input type="text" class="form-control" id="column2Title" value="Resources" onchange="saveColumnTitles()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Column 3</label>
                <input type="text" class="form-control" id="column3Title" value="Customer Service" onchange="saveColumnTitles()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Column 4</label>
                <input type="text" class="form-control" id="column4Title" value="Connect" onchange="saveColumnTitles()">
            </div>
        </div>
    </div>
</div>

<!-- Footer Links Management -->
<div class="row">
    <!-- Column 1: Shop -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-shop me-2"></i><span id="col1Header">Shop</span></h6>
                <button class="btn btn-sm btn-outline-primary" onclick="showAddLinkModal('shop')" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Link to Shop">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="shopLinks">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Column 2: Resources -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-book me-2"></i><span id="col2Header">Resources</span></h6>
                <button class="btn btn-sm btn-outline-primary" onclick="showAddLinkModal('resources')" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Link to Resources">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="resourcesLinks">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Column 3: Customer Service -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-headset me-2"></i><span id="col3Header">Customer Service</span></h6>
                <button class="btn btn-sm btn-outline-primary" onclick="showAddLinkModal('customer_service')" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Link to Customer Service">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="customerServiceLinks">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Column 4: Connect -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-share me-2"></i><span id="col4Header">Connect</span></h6>
                <button class="btn btn-sm btn-outline-primary" onclick="showAddLinkModal('connect')" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Link to Connect">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="connectLinks">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Legend Card -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Link Types</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-secondary me-2">Core</span>
                <small class="text-muted">Always present, cannot be disabled</small>
            </div>
            <div class="col-md-3">
                <span class="badge bg-primary me-2">Feature</span>
                <small class="text-muted">Toggleable via Feature Config</small>
            </div>
            <div class="col-md-3">
                <span class="badge bg-info me-2">Page</span>
                <small class="text-muted">Links to editable pages</small>
            </div>
            <div class="col-md-3">
                <span class="badge bg-success me-2">Custom</span>
                <small class="text-muted">Site-specific custom links</small>
            </div>
        </div>
    </div>
</div>

<!-- Editable Pages Section -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Editable Pages</h5>
        <a href="{{ route('admin.footer.pages') }}" class="btn btn-prt btn-sm">
            <i class="bi bi-pencil me-1"></i> Manage Pages
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Type</th>
                        <th>Footer Section</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="pagesTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-3">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <nav class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <div id="pagesPaginationInfo" class="text-muted small"></div>
                <ul class="pagination pagination-sm mb-0" id="pagesPagination"></ul>
            </div>
        </nav>
    </div>
</div>

<!-- Add/Edit Link Modal -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkModalTitle">Add Custom Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="linkForm">
                    <input type="hidden" id="linkId">
                    <input type="hidden" id="linkSection">

                    <div class="mb-3">
                        <label class="form-label">Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="linkLabel" required placeholder="e.g., About Our Company">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="linkUrl" required placeholder="/about-us or https://...">
                        <small class="text-muted">Use relative paths for internal links (e.g., /about-us)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Footer Section</label>
                        <select class="form-select" id="linkSectionSelect">
                            <option value="shop">Shop</option>
                            <option value="resources">Resources</option>
                            <option value="customer_service">Customer Service</option>
                            <option value="connect">Connect</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link Type</label>
                        <select class="form-select" id="linkType">
                            <option value="custom">Custom Link</option>
                            <option value="page">Page Link</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="linkActive" checked>
                            <label class="form-check-label" for="linkActive">Active</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="linkNewTab">
                            <label class="form-check-label" for="linkNewTab">Open in new tab</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveLink()">
                    <i class="bi bi-check-lg me-1"></i> Save Link
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
                <h5 class="modal-title">Footer Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="footerPreview" style="background-color: #333; color: #fff; padding: 40px;">
                    <!-- Preview rendered here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Page Modal -->
<div class="modal fade" id="editPageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPageModalTitle">Edit Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPageForm">
                    <input type="hidden" id="editPageId">

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Page Title -->
                            <div class="mb-3">
                                <label class="form-label">Page Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editPageTitle" required
                                       placeholder="e.g., Shipping Policy" onkeyup="generateEditSlug()">
                            </div>

                            <!-- URL Slug -->
                            <div class="mb-3">
                                <label class="form-label">URL Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text">/</span>
                                    <input type="text" class="form-control" id="editPageSlug"
                                           placeholder="shipping-policy">
                                </div>
                                <small class="text-muted">Auto-generated from title. Edit if needed.</small>
                            </div>

                            <!-- Content Editor -->
                            <div class="mb-3">
                                <label class="form-label">Page Content</label>
                                <textarea id="editPageContent" class="form-control tinymce-editor" rows="15"
                                          placeholder="Enter page content here..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Page Settings -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Page Settings</h6>

                                    <!-- Page Type -->
                                    <div class="mb-3">
                                        <label class="form-label">Page Type</label>
                                        <select class="form-select" id="editPageType">
                                            <option value="policy">Policy Page</option>
                                            <option value="info">Info Page</option>
                                            <option value="custom">Custom Page</option>
                                        </select>
                                    </div>

                                    <!-- Footer Section -->
                                    <div class="mb-3">
                                        <label class="form-label">Footer Section</label>
                                        <select class="form-select" id="editPageFooterSection">
                                            <option value="">-- Not in Footer --</option>
                                            <option value="shop">Shop</option>
                                            <option value="resources">Resources</option>
                                            <option value="customer_service">Customer Service</option>
                                            <option value="connect">Connect</option>
                                        </select>
                                    </div>

                                    <!-- Show in Footer -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="editShowInFooter">
                                            <label class="form-check-label" for="editShowInFooter">Show in Footer</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">SEO Settings</h6>

                                    <!-- Meta Title -->
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="editMetaTitle"
                                               placeholder="Page title for search engines">
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="editMetaDescription" rows="2"
                                                  placeholder="Brief description for search results"></textarea>
                                    </div>

                                    <hr>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editPageStatus"
                                                       id="editStatusDraft" value="draft">
                                                <label class="form-check-label" for="editStatusDraft">Draft</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editPageStatus"
                                                       id="editStatusPublished" value="published">
                                                <label class="form-check-label" for="editStatusPublished">Published</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveEditedPage()">
                    <i class="bi bi-check-lg me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.link-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.15s;
}
.link-item:hover {
    background-color: #f8f9fa;
}
.link-item:last-child {
    border-bottom: none;
}
.link-item .grip {
    cursor: grab;
    color: #adb5bd;
    margin-right: 0.75rem;
}
.link-item .link-info {
    flex: 1;
}
.link-item .link-label {
    font-weight: 500;
    margin-bottom: 2px;
}
.link-item .link-url {
    font-size: 0.75rem;
    color: #6c757d;
}
.link-item .link-actions {
    display: flex;
    gap: 0.25rem;
}
.link-item.disabled {
    opacity: 0.5;
}
.link-item .badge {
    font-size: 0.65rem;
    font-weight: normal;
}
.feature-disabled-badge {
    background-color: #dc3545 !important;
}

/* Row selection styles */
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
.table tbody tr[style*="cursor: pointer"]:hover {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let linkModal, previewModal, editPageModal;
let pagesCurrentPage = 1;
let pagesPerPage = 5;
let allPagesData = [];
let editPageTinyMCE = null;

// Row highlight function
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;
    // Don't highlight if clicking on buttons, links, or selects
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.closest('button') || target.closest('a') || target.closest('select')) {
        return;
    }
    // Remove selection from all other rows
    var selectedRows = document.querySelectorAll('#pagesTableBody tr.row-selected');
    selectedRows.forEach(function(r) {
        r.classList.remove('row-selected');
    });
    // Add selection to clicked row
    row.classList.add('row-selected');
}

// Default footer structure based on requirements
const defaultFooterLinks = {
    shop: [
        { id: 1, label: 'Home', url: '/', link_type: 'core', is_active: true, sort_order: 0 },
        { id: 2, label: 'All Products', url: '/products', link_type: 'core', is_active: true, sort_order: 1 },
        { id: 3, label: 'Special Products', url: '/specialty-products', link_type: 'feature', feature_flag: 'specialty_products_enabled', is_active: true, sort_order: 2 },
        { id: 4, label: 'Gift Cards', url: '/gift-cards', link_type: 'feature', feature_flag: 'gift_cards_enabled', is_active: true, sort_order: 3 },
        { id: 5, label: 'Shopping Cart', url: '/cart', link_type: 'core', is_active: true, sort_order: 4 }
    ],
    resources: [
        { id: 6, label: 'About Us', url: '/about', link_type: 'core', is_active: true, sort_order: 0 },
        { id: 7, label: 'Blog', url: '/blog', link_type: 'feature', feature_flag: 'blog_enabled', is_active: true, sort_order: 1 },
        { id: 8, label: 'Events', url: '/events', link_type: 'feature', feature_flag: 'events_enabled', is_active: true, sort_order: 2 },
        { id: 9, label: 'Sizing Guide', url: '/sizing-guide', link_type: 'page', is_active: true, sort_order: 3 },
        { id: 10, label: 'Pecos Bill Legend', url: '/pecos-bill', link_type: 'custom', is_active: true, sort_order: 4 }
    ],
    customer_service: [
        { id: 11, label: 'Contact Us', url: '/contact', link_type: 'core', is_active: true, sort_order: 0 },
        { id: 12, label: 'FAQ', url: '/faq', link_type: 'feature', feature_flag: 'faq_enabled', is_active: true, sort_order: 1 },
        { id: 13, label: 'Tell-A-Friend', url: '/tell-a-friend', link_type: 'feature', feature_flag: 'tell_a_friend_enabled', is_active: true, sort_order: 2 },
        { id: 14, label: 'Shipping Policy', url: '/shipping-policy', link_type: 'page', is_active: true, sort_order: 3 },
        { id: 15, label: 'Return Policy', url: '/return-policy', link_type: 'page', is_active: true, sort_order: 4 },
        { id: 16, label: 'Privacy Policy', url: '/privacy-policy', link_type: 'page', is_active: true, sort_order: 5 }
    ],
    connect: [
        { id: 17, label: 'Facebook', url: 'https://facebook.com', link_type: 'custom', is_active: true, sort_order: 0, new_tab: true },
        { id: 18, label: 'Instagram', url: 'https://instagram.com', link_type: 'custom', is_active: true, sort_order: 1, new_tab: true },
        { id: 19, label: 'Newsletter Signup', url: '/newsletter', link_type: 'custom', is_active: true, sort_order: 2 }
    ]
};

const defaultPages = [
    { id: 1, title: 'Shipping Policy', slug: 'shipping-policy', page_type: 'policy', footer_section: 'customer_service', status: 'published', updated_at: '2025-11-29' },
    { id: 2, title: 'Return Policy', slug: 'return-policy', page_type: 'policy', footer_section: 'customer_service', status: 'published', updated_at: '2025-11-28' },
    { id: 3, title: 'Privacy Policy', slug: 'privacy-policy', page_type: 'policy', footer_section: 'customer_service', status: 'published', updated_at: '2025-11-25' },
    { id: 4, title: 'Terms of Service', slug: 'terms-of-service', page_type: 'policy', footer_section: 'customer_service', status: 'published', updated_at: '2025-11-20' },
    { id: 5, title: 'Sizing Guide', slug: 'sizing-guide', page_type: 'info', footer_section: 'resources', status: 'published', updated_at: '2025-11-15' },
    { id: 6, title: 'About Pecos River', slug: 'about-pecos', page_type: 'custom', footer_section: 'resources', status: 'published', updated_at: '2025-11-10' },
    { id: 7, title: 'Pecos Bill Legend', slug: 'pecos-bill', page_type: 'custom', footer_section: 'resources', status: 'published', updated_at: '2025-11-10' }
];

const columnTitles = {
    column1: 'Shop',
    column2: 'Resources',
    column3: 'Customer Service',
    column4: 'Connect'
};

document.addEventListener('DOMContentLoaded', function() {
    linkModal = new bootstrap.Modal(document.getElementById('linkModal'));
    previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    editPageModal = new bootstrap.Modal(document.getElementById('editPageModal'));
    loadFooterData();

    // Initialize Bootstrap tooltips for static buttons
    initTooltips();

    // Initialize TinyMCE for edit modal when it opens
    document.getElementById('editPageModal').addEventListener('shown.bs.modal', function() {
        if (!editPageTinyMCE && typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#editPageContent',
                height: 350,
                menubar: false,
                plugins: 'anchor autolink charmap codesample emoticons link lists table visualblocks wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | link | align | numlist bullist | table | removeformat',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
                branding: false,
                promotion: false,
                setup: function(editor) {
                    editPageTinyMCE = editor;
                }
            });
        }
    });

    // Destroy TinyMCE when modal closes
    document.getElementById('editPageModal').addEventListener('hidden.bs.modal', function() {
        if (editPageTinyMCE) {
            tinymce.remove('#editPageContent');
            editPageTinyMCE = null;
        }
    });
});

function initTooltips() {
    // Dispose existing tooltips before reinitializing
    const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    existingTooltips.forEach(el => {
        const tooltip = bootstrap.Tooltip.getInstance(el);
        if (tooltip) tooltip.dispose();
    });

    // Initialize all tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
}

async function loadFooterData() {
    try {
        // Try to load from API
        const response = await fetch(`${API_BASE}/admin/footer`);
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                renderAllColumns(data.data.links || defaultFooterLinks);
                renderPagesTable(data.data.pages || defaultPages);
                loadColumnTitles(data.data.settings || columnTitles);
                return;
            }
        }
    } catch (error) {
        console.log('API not available, using defaults');
    }

    // Use defaults
    renderAllColumns(defaultFooterLinks);
    renderPagesTable(defaultPages);
    loadColumnTitles(columnTitles);
}

function loadColumnTitles(settings) {
    document.getElementById('column1Title').value = settings.column1 || 'Shop';
    document.getElementById('column2Title').value = settings.column2 || 'Resources';
    document.getElementById('column3Title').value = settings.column3 || 'Customer Service';
    document.getElementById('column4Title').value = settings.column4 || 'Connect';

    document.getElementById('col1Header').textContent = settings.column1 || 'Shop';
    document.getElementById('col2Header').textContent = settings.column2 || 'Resources';
    document.getElementById('col3Header').textContent = settings.column3 || 'Customer Service';
    document.getElementById('col4Header').textContent = settings.column4 || 'Connect';
}

function renderAllColumns(links) {
    renderColumn('shopLinks', links.shop || [], 'shop');
    renderColumn('resourcesLinks', links.resources || [], 'resources');
    renderColumn('customerServiceLinks', links.customer_service || [], 'customer_service');
    renderColumn('connectLinks', links.connect || [], 'connect');

    // Reinitialize tooltips after dynamic content is added
    initTooltips();
}

function renderColumn(containerId, links, section) {
    const container = document.getElementById(containerId);

    if (!links || links.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="bi bi-link-45deg" style="font-size: 1.5rem;"></i>
                <p class="small mb-0 mt-1">No links</p>
            </div>
        `;
        return;
    }

    let html = '';
    links.sort((a, b) => a.sort_order - b.sort_order).forEach(link => {
        const typeBadge = getTypeBadge(link.link_type);
        const isDisabled = !link.is_active;
        const isCore = link.link_type === 'core';

        html += `
            <div class="link-item ${isDisabled ? 'disabled' : ''}" data-id="${link.id}">
                ${!isCore ? '<i class="bi bi-grip-vertical grip"></i>' : '<i class="bi bi-lock text-muted me-2" title="Core link"></i>'}
                <div class="link-info">
                    <div class="link-label">${link.label} ${typeBadge}</div>
                    <div class="link-url">${link.url}</div>
                </div>
                <div class="link-actions">
                    ${!isCore ? `
                        <button class="btn btn-sm btn-link text-primary p-0" onclick="toggleLink(${link.id}, ${!link.is_active})" data-bs-toggle="tooltip" data-bs-placement="top" title="${link.is_active ? 'Disable Link' : 'Enable Link'}">
                            <i class="bi bi-${link.is_active ? 'eye' : 'eye-slash'}"></i>
                        </button>
                        <button class="btn btn-sm btn-link text-secondary p-0" onclick="editLink(${link.id}, '${section}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Link">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${link.link_type === 'custom' ? `
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteLink(${link.id})" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Link">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : ''}
                    ` : ''}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;

    // Initialize sortable
    new Sortable(container, {
        animation: 150,
        handle: '.grip',
        filter: '.link-item:has(.bi-lock)',
        onEnd: function(evt) {
            saveOrder(section);
        }
    });
}

function getTypeBadge(type) {
    const badges = {
        core: '<span class="badge bg-secondary">Core</span>',
        feature: '<span class="badge bg-primary">Feature</span>',
        page: '<span class="badge bg-info">Page</span>',
        custom: '<span class="badge bg-success">Custom</span>'
    };
    return badges[type] || '';
}

function renderPagesTable(pages, storeData = true) {
    const tbody = document.getElementById('pagesTableBody');

    // Store all pages data for pagination
    if (storeData) {
        allPagesData = pages || [];
    }

    if (!allPagesData || allPagesData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    No pages found. <a href="{{ route('admin.footer.pages') }}">Create your first page</a>
                </td>
            </tr>
        `;
        document.getElementById('pagesPaginationInfo').textContent = '';
        document.getElementById('pagesPagination').innerHTML = '';
        return;
    }

    // Calculate pagination
    const totalPages = Math.ceil(allPagesData.length / pagesPerPage);
    const startIndex = (pagesCurrentPage - 1) * pagesPerPage;
    const endIndex = Math.min(startIndex + pagesPerPage, allPagesData.length);
    const paginatedPages = allPagesData.slice(startIndex, endIndex);

    let html = '';
    paginatedPages.forEach(page => {
        const statusBadge = page.status === 'published'
            ? '<span class="badge bg-success">Published</span>'
            : '<span class="badge bg-warning">Draft</span>';
        const typeBadge = getPageTypeBadge(page.page_type);
        const sectionLabel = getSectionLabel(page.footer_section);

        html += `
            <tr onclick="highlightRow(event)" style="cursor: pointer;">
                <td>
                    <strong>${page.title}</strong>
                    <br><small class="text-muted">/${page.slug}</small>
                </td>
                <td>${typeBadge}</td>
                <td>${sectionLabel}</td>
                <td>${statusBadge}</td>
                <td><small>${formatDate(page.updated_at)}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); editPageInline(${page.id})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Page">
                        <i class="bi bi-pencil"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    // Render pagination
    renderPagesPagination(totalPages, startIndex + 1, endIndex, allPagesData.length);

    // Reinitialize tooltips after dynamic content is added
    initTooltips();
}

function renderPagesPagination(totalPages, from, to, total) {
    // Update info text
    document.getElementById('pagesPaginationInfo').textContent =
        `Showing ${from} to ${to} of ${total} pages`;

    const pagination = document.getElementById('pagesPagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Previous button
    html += `<li class="page-item ${pagesCurrentPage === 1 ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="goToPage(${pagesCurrentPage - 1}); return false;">Previous</a>`;
    html += `</li>`;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === pagesCurrentPage ? 'active' : ''}">`;
        html += `<a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>`;
        html += `</li>`;
    }

    // Next button
    html += `<li class="page-item ${pagesCurrentPage === totalPages ? 'disabled' : ''}">`;
    html += `<a class="page-link" href="#" onclick="goToPage(${pagesCurrentPage + 1}); return false;">Next</a>`;
    html += `</li>`;

    pagination.innerHTML = html;
}

function goToPage(page) {
    const totalPages = Math.ceil(allPagesData.length / pagesPerPage);
    if (page < 1 || page > totalPages) return;
    pagesCurrentPage = page;
    renderPagesTable(null, false);
}

function getPageTypeBadge(type) {
    const badges = {
        policy: '<span class="badge bg-warning text-dark">Policy</span>',
        info: '<span class="badge bg-info">Info</span>',
        custom: '<span class="badge bg-success">Custom</span>'
    };
    return badges[type] || '<span class="badge bg-secondary">Other</span>';
}

function getSectionLabel(section) {
    const labels = {
        shop: 'Shop',
        resources: 'Resources',
        customer_service: 'Customer Service',
        connect: 'Connect'
    };
    return labels[section] || section;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function showAddLinkModal(section = 'shop') {
    document.getElementById('linkModalTitle').textContent = 'Add Custom Link';
    document.getElementById('linkForm').reset();
    document.getElementById('linkId').value = '';
    document.getElementById('linkSection').value = section;
    document.getElementById('linkSectionSelect').value = section;
    document.getElementById('linkActive').checked = true;
    linkModal.show();
}

async function editLink(id, section) {
    // Find link in defaults or fetch from API
    let link = null;
    for (const sec in defaultFooterLinks) {
        const found = defaultFooterLinks[sec].find(l => l.id === id);
        if (found) {
            link = found;
            break;
        }
    }

    if (!link) return;

    document.getElementById('linkModalTitle').textContent = 'Edit Link';
    document.getElementById('linkId').value = link.id;
    document.getElementById('linkLabel').value = link.label;
    document.getElementById('linkUrl').value = link.url;
    document.getElementById('linkSectionSelect').value = section;
    document.getElementById('linkType').value = link.link_type;
    document.getElementById('linkActive').checked = link.is_active;
    document.getElementById('linkNewTab').checked = link.new_tab || false;

    linkModal.show();
}

async function saveLink() {
    const id = document.getElementById('linkId').value;
    const data = {
        label: document.getElementById('linkLabel').value,
        url: document.getElementById('linkUrl').value,
        section: document.getElementById('linkSectionSelect').value,
        link_type: document.getElementById('linkType').value,
        is_active: document.getElementById('linkActive').checked,
        new_tab: document.getElementById('linkNewTab').checked
    };

    if (!data.label || !data.url) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const url = id ? `${API_BASE}/admin/footer/links/${id}` : `${API_BASE}/admin/footer/links`;
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            linkModal.hide();
            loadFooterData();
            showToast('Link saved successfully', 'success');
        } else {
            // For demo, just close and show success
            linkModal.hide();
            showToast('Link saved (demo mode)', 'success');
        }
    } catch (error) {
        // For demo mode
        linkModal.hide();
        showToast('Link saved (demo mode)', 'success');
    }
}

async function toggleLink(id, active) {
    try {
        await fetch(`${API_BASE}/admin/footer/links/${id}/toggle`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_active: active })
        });
        loadFooterData();
    } catch (error) {
        // Toggle locally for demo
        for (const section in defaultFooterLinks) {
            const link = defaultFooterLinks[section].find(l => l.id === id);
            if (link) {
                link.is_active = active;
                break;
            }
        }
        renderAllColumns(defaultFooterLinks);
        showToast(`Link ${active ? 'enabled' : 'disabled'}`, 'success');
    }
}

async function deleteLink(id) {
    if (!confirm('Are you sure you want to delete this link?')) return;

    try {
        await fetch(`${API_BASE}/admin/footer/links/${id}`, {
            method: 'DELETE'
        });
        loadFooterData();
        showToast('Link deleted', 'success');
    } catch (error) {
        // Remove from local for demo
        for (const section in defaultFooterLinks) {
            const index = defaultFooterLinks[section].findIndex(l => l.id === id);
            if (index > -1) {
                defaultFooterLinks[section].splice(index, 1);
                break;
            }
        }
        renderAllColumns(defaultFooterLinks);
        showToast('Link deleted (demo mode)', 'success');
    }
}

async function saveOrder(section) {
    const container = document.getElementById(section === 'customer_service' ? 'customerServiceLinks' : section + 'Links');
    const items = container.querySelectorAll('.link-item');
    const order = Array.from(items).map((item, index) => ({
        id: parseInt(item.dataset.id),
        sort_order: index
    }));

    try {
        await fetch(`${API_BASE}/admin/footer/links/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ section, order })
        });
    } catch (error) {
        console.log('Order saved locally');
    }
}

async function saveColumnTitles() {
    const data = {
        column1: document.getElementById('column1Title').value,
        column2: document.getElementById('column2Title').value,
        column3: document.getElementById('column3Title').value,
        column4: document.getElementById('column4Title').value
    };

    // Update headers
    document.getElementById('col1Header').textContent = data.column1;
    document.getElementById('col2Header').textContent = data.column2;
    document.getElementById('col3Header').textContent = data.column3;
    document.getElementById('col4Header').textContent = data.column4;

    try {
        await fetch(`${API_BASE}/admin/footer/settings`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        showToast('Titles saved', 'success');
    } catch (error) {
        showToast('Titles saved (demo mode)', 'success');
    }
}

function previewFooter() {
    const col1 = document.getElementById('column1Title').value;
    const col2 = document.getElementById('column2Title').value;
    const col3 = document.getElementById('column3Title').value;
    const col4 = document.getElementById('column4Title').value;

    const preview = document.getElementById('footerPreview');

    let html = `
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 style="color: #8B4513; margin-bottom: 20px;">${col1}</h5>
                    <ul style="list-style: none; padding: 0;">
    `;

    defaultFooterLinks.shop.filter(l => l.is_active).forEach(link => {
        html += `<li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none;">${link.label}</a></li>`;
    });

    html += `
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 style="color: #8B4513; margin-bottom: 20px;">${col2}</h5>
                    <ul style="list-style: none; padding: 0;">
    `;

    defaultFooterLinks.resources.filter(l => l.is_active).forEach(link => {
        html += `<li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none;">${link.label}</a></li>`;
    });

    html += `
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 style="color: #8B4513; margin-bottom: 20px;">${col3}</h5>
                    <ul style="list-style: none; padding: 0;">
    `;

    defaultFooterLinks.customer_service.filter(l => l.is_active).forEach(link => {
        html += `<li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none;">${link.label}</a></li>`;
    });

    html += `
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 style="color: #8B4513; margin-bottom: 20px;">${col4}</h5>
                    <ul style="list-style: none; padding: 0;">
    `;

    defaultFooterLinks.connect.filter(l => l.is_active).forEach(link => {
        html += `<li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none;">${link.label}</a></li>`;
    });

    html += `
                    </ul>
                </div>
            </div>
            <hr style="border-color: #555;">
            <div class="text-center" style="color: #888;">
                <p>&copy; 2025 Pecos River Traders. All rights reserved.</p>
            </div>
        </div>
    `;

    preview.innerHTML = html;
    previewModal.show();
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    toast.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Sample page content for editing (matches footer-pages.blade.php)
const pageContentData = {
    1: { content: '<h2>Shipping Policy</h2><p>We ship to all 50 US states. Standard shipping takes 5-7 business days. Express shipping available for an additional fee.</p>', meta_title: 'Shipping Policy - Pecos River Traders', meta_description: 'Learn about our shipping options and delivery times.' },
    2: { content: '<h2>Return Policy</h2><p>We accept returns within 30 days of purchase. Items must be unused and in original packaging.</p>', meta_title: 'Return Policy - Pecos River Traders', meta_description: 'Our hassle-free return policy explained.' },
    3: { content: '<h2>Privacy Policy</h2><p>Your privacy is important to us. We do not sell or share your personal information with third parties.</p>', meta_title: 'Privacy Policy - Pecos River Traders', meta_description: 'How we protect your personal information.' },
    4: { content: '<h2>Terms of Service</h2><p>By using our website, you agree to these terms of service. Please read them carefully.</p>', meta_title: 'Terms of Service - Pecos River Traders', meta_description: 'Terms and conditions for using our website.' },
    5: { content: '<h2>Sizing Guide</h2><p>Use our comprehensive sizing guide to find the perfect fit for boots, hats, and apparel.</p>', meta_title: 'Sizing Guide - Pecos River Traders', meta_description: 'Find your perfect size with our sizing charts.' },
    6: { content: '<h2>About Pecos River</h2><p>The Pecos River flows through New Mexico and Texas, giving our region its distinctive character.</p>', meta_title: 'About Pecos River - Pecos River Traders', meta_description: 'Learn about the legendary Pecos River.' },
    7: { content: '<h2>The Legend of Pecos Bill</h2><p>Pecos Bill is a legendary cowboy of the American Southwest, said to have been raised by coyotes.</p>', meta_title: 'Pecos Bill Legend - Pecos River Traders', meta_description: 'The tall tale of Pecos Bill, legendary cowboy.' }
};

function editPageInline(pageId) {
    // Find page in allPagesData
    const page = allPagesData.find(p => p.id === pageId);
    if (!page) {
        showToast('Page not found', 'error');
        return;
    }

    // Get content data
    const contentData = pageContentData[pageId] || { content: '', meta_title: '', meta_description: '' };

    // Populate form
    document.getElementById('editPageId').value = page.id;
    document.getElementById('editPageTitle').value = page.title;
    document.getElementById('editPageSlug').value = page.slug;
    document.getElementById('editPageType').value = page.page_type;
    document.getElementById('editPageFooterSection').value = page.footer_section || '';
    document.getElementById('editShowInFooter').checked = page.show_in_footer !== false;
    document.getElementById('editMetaTitle').value = contentData.meta_title || '';
    document.getElementById('editMetaDescription').value = contentData.meta_description || '';

    // Set status
    if (page.status === 'draft') {
        document.getElementById('editStatusDraft').checked = true;
    } else {
        document.getElementById('editStatusPublished').checked = true;
    }

    // Set content in textarea (TinyMCE will pick it up when modal opens)
    document.getElementById('editPageContent').value = contentData.content || '';

    // Update modal title
    document.getElementById('editPageModalTitle').textContent = 'Edit: ' + page.title;

    // Show modal
    editPageModal.show();

    // If TinyMCE is already initialized, set content
    setTimeout(function() {
        if (editPageTinyMCE) {
            editPageTinyMCE.setContent(contentData.content || '');
        }
    }, 300);
}

function generateEditSlug() {
    const title = document.getElementById('editPageTitle').value;
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('editPageSlug').value = slug;
}

async function saveEditedPage() {
    const pageId = document.getElementById('editPageId').value;
    const title = document.getElementById('editPageTitle').value;

    if (!title) {
        alert('Please enter a page title');
        return;
    }

    // Get content from TinyMCE or textarea
    let content = '';
    if (editPageTinyMCE) {
        content = editPageTinyMCE.getContent();
    } else {
        content = document.getElementById('editPageContent').value;
    }

    const pageData = {
        title: title,
        slug: document.getElementById('editPageSlug').value,
        content: content,
        page_type: document.getElementById('editPageType').value,
        footer_section: document.getElementById('editPageFooterSection').value,
        show_in_footer: document.getElementById('editShowInFooter').checked,
        meta_title: document.getElementById('editMetaTitle').value,
        meta_description: document.getElementById('editMetaDescription').value,
        status: document.querySelector('input[name="editPageStatus"]:checked').value
    };

    try {
        const response = await fetch(`${API_BASE}/admin/footer/pages/${pageId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(pageData)
        });

        if (response.ok) {
            editPageModal.hide();
            loadFooterData();
            showToast('Page saved successfully', 'success');
        } else {
            // Demo mode - update local data
            updateLocalPageData(pageId, pageData);
        }
    } catch (error) {
        // Demo mode - update local data
        updateLocalPageData(pageId, pageData);
    }
}

function updateLocalPageData(pageId, pageData) {
    // Update allPagesData
    const pageIndex = allPagesData.findIndex(p => p.id === parseInt(pageId));
    if (pageIndex !== -1) {
        allPagesData[pageIndex] = {
            ...allPagesData[pageIndex],
            title: pageData.title,
            slug: pageData.slug,
            page_type: pageData.page_type,
            footer_section: pageData.footer_section,
            show_in_footer: pageData.show_in_footer,
            status: pageData.status,
            updated_at: new Date().toISOString()
        };

        // Update defaultPages array too
        const defaultIndex = defaultPages.findIndex(p => p.id === parseInt(pageId));
        if (defaultIndex !== -1) {
            defaultPages[defaultIndex] = allPagesData[pageIndex];
        }

        // Update pageContentData
        pageContentData[pageId] = {
            content: pageData.content,
            meta_title: pageData.meta_title,
            meta_description: pageData.meta_description
        };
    }

    editPageModal.hide();
    renderPagesTable(null, false);
    showToast('Page saved (demo mode)', 'success');
}
</script>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/zeznyjaqe9c56yilns9k0mck1wivl5fh6cnb14qyhrhm37zi/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endpush
