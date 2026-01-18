@extends('layouts.admin')

@section('title', 'Content Pages')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Content Pages</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.footer.links') }}">Footer Navigation</a></li>
                <li class="breadcrumb-item active">Pages</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-prt" onclick="showAddPageModal()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create a New Content Page">
        <i class="bi bi-plus-lg me-1"></i> Add New Page
    </button>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4" id="pageTabs">
    <li class="nav-item">
        <a class="nav-link active" href="#" data-filter="all">All Pages</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="policy">Policy Pages</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="info">Info Pages</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="custom">Custom Pages</a>
    </li>
</ul>

<!-- Pages List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="pagesTable">
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>Page Title</th>
                        <th>Type</th>
                        <th>Footer Section</th>
                        <th>Show in Footer</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="pagesTableBody">
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Page Modal -->
<div class="modal fade" id="pageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pageModalTitle">Add New Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="pageForm">
                    <input type="hidden" id="pageId">

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Page Title -->
                            <div class="mb-3">
                                <label class="form-label">Page Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pageTitle" required
                                       placeholder="e.g., Shipping Policy" onkeyup="generateSlug()">
                            </div>

                            <!-- URL Slug -->
                            <div class="mb-3">
                                <label class="form-label">URL Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text">/</span>
                                    <input type="text" class="form-control" id="pageSlug"
                                           placeholder="shipping-policy">
                                </div>
                                <small class="text-muted">Auto-generated from title. Edit if needed.</small>
                            </div>

                            <!-- Content Editor -->
                            <div class="mb-3">
                                <label class="form-label">Page Content</label>
                                <div id="editorContainer">
                                    <textarea id="pageContent" class="form-control" rows="15"
                                              placeholder="Enter page content here..."></textarea>
                                </div>
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
                                        <select class="form-select" id="pageType">
                                            <option value="policy">Policy Page</option>
                                            <option value="info">Info Page</option>
                                            <option value="custom">Custom Page</option>
                                        </select>
                                        <small class="text-muted mt-1 d-block">
                                            Policy pages are for legal content like Terms & Privacy.
                                        </small>
                                    </div>

                                    <!-- Footer Section -->
                                    <div class="mb-3">
                                        <label class="form-label">Footer Section</label>
                                        <select class="form-select" id="pageFooterSection">
                                            <option value="">-- Not in Footer --</option>
                                            <option value="shop">Shop</option>
                                            <option value="resources">Resources</option>
                                            <option value="customer_service" selected>Customer Service</option>
                                            <option value="connect">Connect</option>
                                        </select>
                                    </div>

                                    <!-- Show in Footer -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="showInFooter" checked>
                                            <label class="form-check-label" for="showInFooter">Show in Footer</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">SEO Settings</h6>

                                    <!-- Meta Title -->
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="metaTitle"
                                               placeholder="Page title for search engines">
                                        <small class="text-muted">Leave blank to use page title</small>
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="metaDescription" rows="2"
                                                  placeholder="Brief description for search results"></textarea>
                                    </div>

                                    <hr>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pageStatus"
                                                       id="statusDraft" value="draft">
                                                <label class="form-check-label" for="statusDraft">Draft</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pageStatus"
                                                       id="statusPublished" value="published" checked>
                                                <label class="form-check-label" for="statusPublished">Published</label>
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
                <button type="button" class="btn btn-outline-secondary" onclick="previewPage()">
                    <i class="bi bi-eye me-1"></i> Preview
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-primary" onclick="savePage('draft')">
                    <i class="bi bi-file-earmark me-1"></i> Save Draft
                </button>
                <button type="button" class="btn btn-prt" onclick="savePage('published')">
                    <i class="bi bi-check-lg me-1"></i> Publish
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewPageModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #660000 0%, #8B6C42 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Page Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background: linear-gradient(135deg, #FFF9F0 0%, #F1C895 100%); min-height: 70vh;">
                <div id="pagePreviewContent" class="p-4">
                    <!-- Preview rendered here -->
                </div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto"><i class="bi bi-info-circle me-1"></i>This is how the page will appear on the frontend</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* PRT CSS Variables for Preview */
:root {
    --prt-tan: #F1C895;
    --prt-tan-dark: #D4A76E;
    --prt-brown: #8B6C42;
    --prt-red: #990000;
    --prt-red-dark: #660000;
    --prt-green: #004000;
    --prt-accent: #FFCC00;
    --prt-cream: #FFF9F0;
}

.page-type-badge {
    font-size: 0.7rem;
}
#pagesTable tbody tr {
    cursor: pointer;
}
#pagesTable tbody tr:hover {
    background-color: #f8f9fa;
}
.editor-toolbar {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
    padding: 8px;
    background: #f8f9fa;
}
.editor-toolbar button {
    padding: 4px 8px;
    margin-right: 2px;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 3px;
    cursor: pointer;
}
.editor-toolbar button:hover {
    background: #e9ecef;
}
#pageContent {
    border-radius: 0 0 4px 4px;
    font-family: monospace;
    min-height: 300px;
}

/* Preview Modal - Match Frontend Styling */
#pagePreviewContent {
    background: linear-gradient(135deg, var(--prt-cream) 0%, #fff 100%);
    border-radius: 8px;
    padding: 2rem !important;
}
#pagePreviewContent .content-card,
#pagePreviewContent .page-content {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}
#pagePreviewContent h1 {
    color: var(--prt-brown);
    font-weight: 700;
    border-bottom: 3px solid var(--prt-tan);
    padding-bottom: 0.75rem;
}
#pagePreviewContent h2 {
    color: var(--prt-brown);
    font-weight: 600;
}
#pagePreviewContent h3 {
    color: var(--prt-brown);
    font-weight: 600;
}
#pagePreviewContent .lead {
    font-size: 1.15rem;
    color: #555;
}
#pagePreviewContent .table {
    margin-bottom: 1rem;
}
#pagePreviewContent .table thead {
    background-color: var(--prt-brown);
    color: white;
}
#pagePreviewContent .table th,
#pagePreviewContent .table td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
}
#pagePreviewContent .alert {
    border-radius: 8px;
    border: none;
}
#pagePreviewContent .alert-success {
    background-color: #d4edda;
    color: #155724;
}
#pagePreviewContent .alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}
#pagePreviewContent .alert-warning {
    background-color: #fff3cd;
    color: #856404;
}
#pagePreviewContent .card {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}
#pagePreviewContent a {
    color: var(--prt-red);
}
#pagePreviewContent a:hover {
    color: var(--prt-red-dark);
}
#pagePreviewContent ul li,
#pagePreviewContent ol li {
    margin-bottom: 0.5rem;
}
#pagePreviewContent .text-success {
    color: #28a745 !important;
}
#pagePreviewContent .text-danger {
    color: #dc3545 !important;
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
</style>
@endpush

@push('scripts')
@include('partials.tinymce')
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let pageModal, previewPageModal;
let currentFilter = 'all';

// Row highlight function
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;
    // Don't highlight if clicking on buttons, links, or selects
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'SELECT' ||
        target.tagName === 'I' || target.closest('button') || target.closest('a') || target.closest('select') ||
        target.closest('.btn-group')) {
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

// Sample pages data with actual content from PRT2 policy pages
const samplePages = [
    {
        id: 1,
        title: 'Shipping Policy',
        slug: 'shipping-policy',
        page_type: 'policy',
        footer_section: 'customer_service',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">Shipping Information</h2>
<p class="lead">We strive to get your order to you as quickly as possible. Most orders are processed and shipped within 1-2 business days.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Shipping Options & Costs</h3>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead style="background-color: #8B6C42; color: white;">
            <tr>
                <th>Shipping Method</th>
                <th>Delivery Time</th>
                <th>Cost</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Standard Shipping</strong></td>
                <td>5-7 business days</td>
                <td>$6.95</td>
            </tr>
            <tr>
                <td><strong>Expedited Shipping</strong></td>
                <td>3-4 business days</td>
                <td>$12.95</td>
            </tr>
            <tr>
                <td><strong>Express Shipping</strong></td>
                <td>2 business days</td>
                <td>$19.95</td>
            </tr>
            <tr>
                <td><strong>Overnight Shipping</strong></td>
                <td>1 business day</td>
                <td>$29.95</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="alert alert-success mt-4">
    <i class="bi bi-gift"></i> <strong>Free Shipping:</strong> Enjoy free standard shipping on all orders over $75!
</div>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Processing Time</h3>
<ul>
    <li>Orders placed before 2:00 PM (CT) Monday-Friday are typically processed the same day</li>
    <li>Orders placed after 2:00 PM or on weekends/holidays are processed the next business day</li>
    <li>You'll receive a shipping confirmation email with tracking information once your order ships</li>
    <li>During peak seasons (holidays), processing may take 2-3 business days</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Shipping Locations</h3>
<p>We currently ship to:</p>
<ul>
    <li><i class="bi bi-check-circle text-success"></i> All 50 U.S. states</li>
    <li><i class="bi bi-check-circle text-success"></i> APO/FPO/DPO addresses</li>
    <li><i class="bi bi-check-circle text-success"></i> U.S. territories (Puerto Rico, Guam, U.S. Virgin Islands)</li>
</ul>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> International shipping is not currently available but coming soon!
</div>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Order Tracking</h3>
<p>Once your order ships, you'll receive:</p>
<ul>
    <li>A shipping confirmation email</li>
    <li>Tracking number for your package</li>
    <li>Estimated delivery date</li>
    <li>Link to track your shipment in real-time</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Delivery Issues</h3>
<p><strong>Package Not Received?</strong></p>
<ul>
    <li>Check with household members or neighbors</li>
    <li>Look around your delivery location (porches, garages, etc.)</li>
    <li>Check the tracking information for delivery confirmation</li>
    <li>Contact us within 7 days if you still haven't received your package</li>
</ul>

<p><strong>Damaged Package?</strong></p>
<ul>
    <li>Take photos of the damaged packaging</li>
    <li>Contact us immediately at <a href="mailto:shipping@pecosrivertraders.com">shipping@pecosrivertraders.com</a></li>
    <li>Keep all packaging materials until the issue is resolved</li>
    <li>We'll arrange for a replacement or refund</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Shipping Restrictions</h3>
<ul>
    <li>We cannot ship to P.O. boxes for expedited or express shipping</li>
    <li>Some remote locations may require additional delivery time</li>
    <li>Signature may be required for high-value orders</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Holidays & Weather</h3>
<p>Deliveries may be delayed during:</p>
<ul>
    <li>Major holidays (Thanksgiving, Christmas, New Year's, etc.)</li>
    <li>Severe weather conditions</li>
    <li>Natural disasters or emergencies</li>
</ul>
<p>We appreciate your patience during these times.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Contact Us</h3>
<p>Questions about shipping? We're here to help!</p>
<p>
    Email: <a href="mailto:shipping@pecosrivertraders.com">shipping@pecosrivertraders.com</a><br>
    Phone: 1-555-123-4567
</p>`,
        meta_title: 'Shipping Policy - Pecos River Traders',
        meta_description: 'Learn about our shipping rates, delivery times, and shipping methods at Pecos River Trading Company. Fast and reliable delivery on all orders.',
        updated_at: '2025-11-29T10:30:00'
    },
    {
        id: 2,
        title: 'Return Policy',
        slug: 'return-policy',
        page_type: 'policy',
        footer_section: 'customer_service',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">30-Day Return Guarantee</h2>
<p class="lead">We want you to be completely satisfied with your purchase. If you're not happy with your order, we accept returns within 30 days of delivery.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Return Eligibility</h3>
<p>Items are eligible for return if they meet the following conditions:</p>
<ul>
    <li><i class="bi bi-check-circle text-success"></i> Returned within 30 days of delivery</li>
    <li><i class="bi bi-check-circle text-success"></i> In original, unworn condition</li>
    <li><i class="bi bi-check-circle text-success"></i> With all original tags and packaging</li>
    <li><i class="bi bi-check-circle text-success"></i> Not damaged or altered</li>
    <li><i class="bi bi-check-circle text-success"></i> Include original receipt or order confirmation</li>
</ul>

<div class="alert alert-warning mt-4">
    <i class="bi bi-exclamation-triangle"></i> <strong>Note:</strong> For hygiene reasons, shoes that show signs of wear (including wear on soles) cannot be returned.
</div>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">How to Return an Item</h3>
<ol class="mb-4">
    <li class="mb-2"><strong>Contact Us:</strong> Email us at <a href="mailto:returns@pecosrivertraders.com">returns@pecosrivertraders.com</a> or call 1-555-123-4567</li>
    <li class="mb-2"><strong>Provide Information:</strong> Include your order number and reason for return</li>
    <li class="mb-2"><strong>Receive Return Authorization:</strong> We'll send you a return authorization number (RMA)</li>
    <li class="mb-2"><strong>Package Items:</strong> Securely pack the items with all original packaging and tags</li>
    <li class="mb-2"><strong>Ship the Package:</strong> Send to the address provided with your RMA number clearly marked</li>
</ol>

<div class="card bg-light mb-4">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-box-seam"></i> Return Shipping Address:</h5>
        <p class="mb-0">
            Pecos River Traders - Returns Department<br>
            [Your Return Address]<br>
            [City, State ZIP]
        </p>
    </div>
</div>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Refunds</h3>
<ul>
    <li>Refunds are processed within 5-7 business days after we receive your return</li>
    <li>Refunds are issued to the original payment method</li>
    <li>Original shipping charges are non-refundable unless the item is defective</li>
    <li>You are responsible for return shipping costs unless the item is defective or we made an error</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Exchanges</h3>
<p>We're happy to exchange items for a different size or color:</p>
<ul>
    <li>Follow the same return process above</li>
    <li>Indicate in your email that you want an exchange</li>
    <li>We'll ship the exchange item once we receive your return</li>
    <li>Exchange shipping is free if the exchange is due to our error</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Defective or Damaged Items</h3>
<p>If you receive a defective or damaged item:</p>
<ul>
    <li>Contact us immediately (within 7 days of delivery)</li>
    <li>Provide photos of the defect or damage</li>
    <li>We'll arrange for a free replacement or full refund</li>
    <li>We'll cover all return shipping costs</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Non-Returnable Items</h3>
<p>The following items cannot be returned:</p>
<ul>
    <li><i class="bi bi-x-circle text-danger"></i> Clearance or final sale items</li>
    <li><i class="bi bi-x-circle text-danger"></i> Gift cards</li>
    <li><i class="bi bi-x-circle text-danger"></i> Items without original tags or packaging</li>
    <li><i class="bi bi-x-circle text-danger"></i> Items showing signs of wear</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Questions?</h3>
<p>If you have any questions about our return policy, please don't hesitate to contact us. Our customer service team is here to help!</p>`,
        meta_title: 'Return Policy - Pecos River Traders',
        meta_description: '30-day return guarantee at Pecos River Traders. Learn about our easy return and exchange process.',
        updated_at: '2025-11-28T14:00:00'
    },
    {
        id: 3,
        title: 'Privacy Policy',
        slug: 'privacy-policy',
        page_type: 'policy',
        footer_section: 'customer_service',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">Introduction</h2>
<p>Pecos River Traders ("we", "us", or "our") respects your privacy and is committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you visit our website.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Information We Collect</h3>
<p>We may collect the following types of information:</p>
<ul>
    <li><strong>Personal Information:</strong> Name, email address, phone number, shipping address, and billing information</li>
    <li><strong>Transaction Information:</strong> Purchase history, payment details, and order information</li>
    <li><strong>Technical Information:</strong> IP address, browser type, device information, and cookies</li>
    <li><strong>Usage Data:</strong> Pages visited, time spent on site, and navigation patterns</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">How We Use Your Information</h3>
<p>We use your information to:</p>
<ul>
    <li>Process and fulfill your orders</li>
    <li>Communicate with you about your purchases</li>
    <li>Improve our website and customer service</li>
    <li>Send promotional emails (with your consent)</li>
    <li>Prevent fraud and enhance security</li>
    <li>Comply with legal obligations</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Information Sharing</h3>
<p>We do not sell your personal information. We may share your data with:</p>
<ul>
    <li><strong>Service Providers:</strong> Payment processors, shipping companies, and email services</li>
    <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
    <li><strong>Business Transfers:</strong> In connection with a merger, sale, or acquisition</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Cookies and Tracking</h3>
<p>We use cookies and similar technologies to enhance your experience. You can control cookie settings through your browser preferences.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Data Security</h3>
<p>We implement appropriate security measures to protect your personal information. However, no method of transmission over the internet is 100% secure.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Your Rights</h3>
<p>You have the right to:</p>
<ul>
    <li>Access your personal data</li>
    <li>Correct inaccurate information</li>
    <li>Request deletion of your data</li>
    <li>Opt-out of marketing communications</li>
    <li>Object to data processing</li>
</ul>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Contact Us</h3>
<p>If you have questions about this privacy policy, please contact us at:</p>
<p>
    Email: <a href="mailto:privacy@pecosrivertraders.com">privacy@pecosrivertraders.com</a>
</p>

<div class="alert alert-info mt-5">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> This privacy policy may be updated periodically. Please check this page regularly for changes.
</div>`,
        meta_title: 'Privacy Policy - Pecos River Traders',
        meta_description: 'Learn how Pecos River Traders protects your privacy and handles your personal data.',
        updated_at: '2025-11-25T09:00:00'
    },
    {
        id: 4,
        title: 'Terms of Service',
        slug: 'terms-of-service',
        page_type: 'policy',
        footer_section: 'customer_service',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">Terms of Service</h2>
<p class="lead">Please read these terms of service carefully before using our website.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Acceptance of Terms</h3>
<p>By accessing or using the Pecos River Traders website, you agree to be bound by these Terms of Service and all applicable laws and regulations.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Use of Website</h3>
<p>You agree to use this website only for lawful purposes and in a way that does not infringe on the rights of others.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Product Information</h3>
<p>We strive to provide accurate product descriptions and pricing. However, we reserve the right to correct any errors and to change or update information at any time.</p>

<h3 class="mt-5 mb-3" style="color: #8B6C42;">Limitation of Liability</h3>
<p>Pecos River Traders shall not be liable for any indirect, incidental, special, or consequential damages resulting from your use of our website or products.</p>`,
        meta_title: 'Terms of Service - Pecos River Traders',
        meta_description: 'Read our terms of service for using the Pecos River Traders website.',
        updated_at: '2025-11-20T16:00:00'
    },
    {
        id: 5,
        title: 'Shoe Sizing Guide',
        slug: 'sizing-guide',
        page_type: 'info',
        footer_section: 'resources',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">How to Measure Your Feet</h2>
<p class="lead">For the most accurate fit, follow these simple steps:</p>

<ol class="mb-4">
    <li class="mb-3"><strong>Prepare Your Materials:</strong> You'll need a piece of paper, a pen or pencil, and a ruler or measuring tape.</li>
    <li class="mb-3"><strong>Measure at the Right Time:</strong> Feet swell during the day, so measure them in the evening for the most accurate size.</li>
    <li class="mb-3"><strong>Wear Socks:</strong> Put on the type of socks you'll typically wear with the shoes.</li>
    <li class="mb-3"><strong>Trace Your Foot:</strong> Stand on the paper and trace around your foot with the pen held vertically.</li>
    <li class="mb-3"><strong>Measure Length:</strong> Measure from the heel to the longest toe.</li>
    <li class="mb-3"><strong>Measure Width:</strong> Measure across the widest part of your foot.</li>
    <li class="mb-3"><strong>Repeat for Both Feet:</strong> Often, one foot is slightly larger. Use the larger measurement.</li>
</ol>

<div class="alert alert-info">
    <i class="bi bi-lightbulb"></i> <strong>Pro Tip:</strong> If you're between sizes, we recommend sizing up for boots and work shoes to allow room for thicker socks.
</div>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Men's Size Chart</h2>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead style="background-color: #8B6C42; color: white;">
            <tr>
                <th>US Size</th>
                <th>UK Size</th>
                <th>EU Size</th>
                <th>Inches</th>
                <th>CM</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>6</td><td>5.5</td><td>39</td><td>9.25"</td><td>23.5</td></tr>
            <tr><td>6.5</td><td>6</td><td>39.5</td><td>9.5"</td><td>24.1</td></tr>
            <tr><td>7</td><td>6.5</td><td>40</td><td>9.625"</td><td>24.4</td></tr>
            <tr><td>7.5</td><td>7</td><td>40.5</td><td>9.75"</td><td>24.8</td></tr>
            <tr><td>8</td><td>7.5</td><td>41</td><td>9.9375"</td><td>25.2</td></tr>
            <tr><td>8.5</td><td>8</td><td>42</td><td>10.125"</td><td>25.7</td></tr>
            <tr><td>9</td><td>8.5</td><td>42.5</td><td>10.25"</td><td>26.0</td></tr>
            <tr><td>9.5</td><td>9</td><td>43</td><td>10.4375"</td><td>26.5</td></tr>
            <tr><td>10</td><td>9.5</td><td>44</td><td>10.5625"</td><td>26.8</td></tr>
            <tr><td>10.5</td><td>10</td><td>44.5</td><td>10.75"</td><td>27.3</td></tr>
            <tr><td>11</td><td>10.5</td><td>45</td><td>10.9375"</td><td>27.8</td></tr>
            <tr><td>11.5</td><td>11</td><td>45.5</td><td>11.125"</td><td>28.3</td></tr>
            <tr><td>12</td><td>11.5</td><td>46</td><td>11.25"</td><td>28.6</td></tr>
            <tr><td>13</td><td>12.5</td><td>47</td><td>11.5625"</td><td>29.4</td></tr>
            <tr><td>14</td><td>13.5</td><td>48</td><td>11.875"</td><td>30.2</td></tr>
        </tbody>
    </table>
</div>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Women's Size Chart</h2>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead style="background-color: #8B6C42; color: white;">
            <tr>
                <th>US Size</th>
                <th>UK Size</th>
                <th>EU Size</th>
                <th>Inches</th>
                <th>CM</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>5</td><td>3</td><td>35.5</td><td>8.1875"</td><td>20.8</td></tr>
            <tr><td>5.5</td><td>3.5</td><td>36</td><td>8.375"</td><td>21.3</td></tr>
            <tr><td>6</td><td>4</td><td>36.5</td><td>8.5"</td><td>21.6</td></tr>
            <tr><td>6.5</td><td>4.5</td><td>37</td><td>8.75"</td><td>22.2</td></tr>
            <tr><td>7</td><td>5</td><td>37.5</td><td>8.875"</td><td>22.5</td></tr>
            <tr><td>7.5</td><td>5.5</td><td>38</td><td>9.0625"</td><td>23.0</td></tr>
            <tr><td>8</td><td>6</td><td>38.5</td><td>9.1875"</td><td>23.3</td></tr>
            <tr><td>8.5</td><td>6.5</td><td>39</td><td>9.375"</td><td>23.8</td></tr>
            <tr><td>9</td><td>7</td><td>40</td><td>9.5"</td><td>24.1</td></tr>
            <tr><td>9.5</td><td>7.5</td><td>40.5</td><td>9.6875"</td><td>24.6</td></tr>
            <tr><td>10</td><td>8</td><td>41</td><td>9.875"</td><td>25.1</td></tr>
            <tr><td>10.5</td><td>8.5</td><td>42</td><td>10"</td><td>25.4</td></tr>
            <tr><td>11</td><td>9</td><td>42.5</td><td>10.1875"</td><td>25.9</td></tr>
            <tr><td>12</td><td>10</td><td>44</td><td>10.5"</td><td>26.7</td></tr>
        </tbody>
    </table>
</div>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Children's Size Chart</h2>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead style="background-color: #8B6C42; color: white;">
            <tr>
                <th>US Size</th>
                <th>UK Size</th>
                <th>EU Size</th>
                <th>Inches</th>
                <th>CM</th>
                <th>Age Range</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>10.5</td><td>10</td><td>28</td><td>6.625"</td><td>16.8</td><td>4-5 years</td></tr>
            <tr><td>11</td><td>10.5</td><td>28.5</td><td>6.75"</td><td>17.1</td><td>4-5 years</td></tr>
            <tr><td>11.5</td><td>11</td><td>29</td><td>7"</td><td>17.8</td><td>5-6 years</td></tr>
            <tr><td>12</td><td>11.5</td><td>30</td><td>7.125"</td><td>18.1</td><td>5-6 years</td></tr>
            <tr><td>12.5</td><td>12</td><td>30.5</td><td>7.25"</td><td>18.4</td><td>6-7 years</td></tr>
            <tr><td>13</td><td>12.5</td><td>31</td><td>7.5"</td><td>19.1</td><td>6-7 years</td></tr>
            <tr><td>13.5</td><td>13</td><td>31.5</td><td>7.625"</td><td>19.4</td><td>7-8 years</td></tr>
            <tr><td>1</td><td>13.5</td><td>32</td><td>7.75"</td><td>19.7</td><td>7-8 years</td></tr>
            <tr><td>1.5</td><td>1</td><td>33</td><td>8"</td><td>20.3</td><td>8-9 years</td></tr>
            <tr><td>2</td><td>1.5</td><td>33.5</td><td>8.125"</td><td>20.6</td><td>8-9 years</td></tr>
            <tr><td>2.5</td><td>2</td><td>34</td><td>8.25"</td><td>21.0</td><td>9-10 years</td></tr>
            <tr><td>3</td><td>2.5</td><td>35</td><td>8.5"</td><td>21.6</td><td>9-10 years</td></tr>
        </tbody>
    </table>
</div>

<div class="alert alert-warning mt-3">
    <i class="bi bi-exclamation-triangle"></i> <strong>Note:</strong> Age ranges are approximate. Always measure your child's feet for the most accurate fit.
</div>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Width Guide</h2>
<p>Shoe widths are typically labeled with letters:</p>
<ul>
    <li><strong>B (Narrow)</strong> - For slimmer feet</li>
    <li><strong>D (Medium/Standard)</strong> - Most common width for men</li>
    <li><strong>M (Medium)</strong> - Most common width for women</li>
    <li><strong>2E or EE (Wide)</strong> - For wider feet</li>
    <li><strong>4E or EEEE (Extra Wide)</strong> - For extra wide feet</li>
</ul>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Fitting Tips</h2>
<ul>
    <li><i class="bi bi-check-circle text-success"></i> There should be about 1/2 inch (thumb width) between your longest toe and the end of the shoe</li>
    <li><i class="bi bi-check-circle text-success"></i> Your heel should fit snugly without slipping</li>
    <li><i class="bi bi-check-circle text-success"></i> The ball of your foot should sit comfortably in the widest part of the shoe</li>
    <li><i class="bi bi-check-circle text-success"></i> Walk around to ensure no pinching or rubbing</li>
    <li><i class="bi bi-check-circle text-success"></i> For boots, allow extra room for thicker socks</li>
    <li><i class="bi bi-check-circle text-success"></i> Children's shoes should have about 1/2 inch of growing room</li>
</ul>

<div class="alert alert-info mt-4">
    <h5><i class="bi bi-question-circle"></i> Still Not Sure?</h5>
    <p class="mb-0">Contact our customer service team at <a href="mailto:sizing@pecosrivertraders.com">sizing@pecosrivertraders.com</a> or call 1-555-123-4567. We're happy to help you find the perfect fit!</p>
</div>`,
        meta_title: 'Boot & Hat Sizing Guide - Pecos River Traders',
        meta_description: 'Find your perfect boot and hat size with our comprehensive sizing guide for men, women, and children.',
        updated_at: '2025-11-15T11:00:00'
    },
    {
        id: 6,
        title: 'About Us',
        slug: 'about-us',
        page_type: 'info',
        footer_section: 'resources',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">Our Story</h2>
<p class="lead">Pecos River Traders has been providing quality footwear to customers since 2000. We specialize in comfortable, durable, and affordable shoes for work, casual wear, and outdoor activities.</p>
<p>Located in the heart of the Southwest, our company is named after the historic Pecos River, which has been a vital source of life and commerce for centuries. Like the river, we aim to be a reliable and constant source for all your footwear needs.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Our Mission</h2>
<p>At Pecos River Traders, our mission is simple: to provide high-quality footwear at prices everyone can afford. We believe that comfort and durability shouldn't come with a premium price tag.</p>
<p>We carefully select each product in our inventory to ensure it meets our standards for quality and value. Whether you need boots for work, sandals for summer, or comfortable slip-ons for everyday wear, we've got you covered.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">What We Offer</h2>
<div class="row g-3">
    <div class="col-md-6">
        <div class="text-center p-3 border rounded">
            <i class="bi bi-stars display-5 mb-3" style="color: #990000;"></i>
            <h5>Quality Products</h5>
            <p>Hand-selected footwear from trusted manufacturers</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="text-center p-3 border rounded">
            <i class="bi bi-tag display-5 mb-3" style="color: #990000;"></i>
            <h5>Affordable Prices</h5>
            <p>Great value without compromising on quality</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="text-center p-3 border rounded">
            <i class="bi bi-truck display-5 mb-3" style="color: #990000;"></i>
            <h5>Fast Shipping</h5>
            <p>Quick delivery to get your shoes to you fast</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="text-center p-3 border rounded">
            <i class="bi bi-headset display-5 mb-3" style="color: #990000;"></i>
            <h5>Customer Support</h5>
            <p>Friendly team ready to help with your needs</p>
        </div>
    </div>
</div>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Our Products</h2>
<p>We offer a wide range of footwear for the whole family:</p>
<ul class="list-unstyled ms-3">
    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Men's Footwear:</strong> Boots, slip-ons, horseshoes, and more</li>
    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Women's Footwear:</strong> Boots, sandals, fashion shoes, slip-ons, and chunks</li>
    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Kids' Footwear:</strong> Durable shoes for growing feet</li>
    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Work Shoes:</strong> Safety and comfort for long days</li>
    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Casual Wear:</strong> Comfortable styles for everyday activities</li>
</ul>`,
        meta_title: 'About Us - Pecos River Traders',
        meta_description: 'Learn about Pecos River Trading Company, your trusted source for quality western footwear since 2000.',
        updated_at: '2025-11-10T08:00:00'
    },
    {
        id: 7,
        title: 'The Legend of Pecos Bill',
        slug: 'pecos-bill',
        page_type: 'custom',
        footer_section: 'resources',
        show_in_footer: true,
        status: 'published',
        content: `<div class="text-center mb-4">
    <img src="/prt2/assets/images/Disney_Pecos_Bill.jpg" alt="Pecos Bill" class="img-fluid mb-4" style="max-width: 300px;">
    <p class="lead"><strong>SEARCH FOR PECOS BILL</strong><br>(Pay cuss Bill)</p>
</div>

<h2 class="mb-4" style="color: #8B6C42;">The Search Begins</h2>
<p>When I first hear'd about Pecos Bill, I had just finished up a little job in a little village in New Mexico. Albuquerque hain't had rain in most a year and the crops had long since dried up and blown away by thet ol' high desert wind. The cattle were next, and when I got there it weren't a purty sight.</p>
<p>The drought had not jeest dried up all the watering holes, but there warn't no grass or hay to feed on. Most of them had got so skinny that the sun passed right through 'em, they didn't even make a shadow. The ranchers had to tie rocks to the cows feet to keep them from blowin' away.</p>
<p>These folks kept telling me how they wished Pecos Bill were in the area, he could fix it in the shake of a rattlesnakes tail. I tried to put it out of my mind, but they jus' kept it up, story after story about this Bill character. Well not to be outdone, I decided to stick around a day and fix their problem.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">The Cloud Wrangling</h2>
<p>Mornin', jest after dawn I gathered up all the rope they could muster up. Well, to make a long tale short 'nuf to stand, with the help of Tornado, my horse, I dragged all that rope up the Sandia Mountain.</p>
<p>As the clouds come neer to bumpin' into miss Sandia I lassoed the biggest one and set Tornado a slidin' down the slope. As the rope tightened, it started squeezin' that cloud until it finally burst. The hole that popped was facing the mountain and hit with such force that this ol' cloud started back-tracking right smack over Albuquerque.</p>
<p>Well, anyway, 'nuff said. I'm still fumin' about all this attention folks are giving to the Bill, whoever he is. So I right then and thar 'cided to turn around and look up this man or beast.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">On the Trail</h2>
<p>I first got on his trail in Amarillo. A dusty little crossroads, but a keg of beer and a couple of them 5 pound steaks made it whole lot easier to swaller the braggin' they were doing about "their" Bill. Seems 'cording to them, that he and his gal Slue Foot spent a lot of time in these parts.</p>
<p>A couple of days ride put me in the town of Pecos. No one was quite sure where he was actually born, but since he was raised somewhere along the Pecos river, this was just as gooda place as any to say he was from.</p>
<p>One feller who looked old enough to have witnessed the creation said that Bill was from somewhere back east. His parents decided to go west and he fell plum off the wagon when they were crossing the Pecos River. The youngun was taken in by a female coyote who nursed him till he was old enough to hunt.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">Riding the Tornado</h2>
<p>One fella obliged my questions by telling me how he has run upon some bad weather a few years back, and saw a funnel cloud coming across the plain. He said "my head told me to skedaddle but my legs wern't listen'n, I just stood there quackin' in my boots".</p>
<p>Then he blubbered the darnest thing. Said that thar tornado started wabbling and changing direction – it was a sight! It was then that he saw Pecos Bill ridin' that twister like a buckin' bronco. Bill had lassoed it and was ridin' it down.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">The Big River</h2>
<p>I guess it was a couple a days later, pushing on down the trail I ran into cowpoke just making camp. Said some dern fool had lassoed a rain cloud in Albuquerque. It set off such a chain reaction that clouds as far north as the Raton pass had started leaking.</p>
<p>When Bill got there Albuquerque was so deep in water Bill had to quick dig a river to get all the water down to the ocean. They wanted to name it Pecos Bill river, but Ole Bill noted that there was already a river named Pecos, he thought it should just be called Big River.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">The Lone Star</h2>
<p>Well, I got to tell you, I never did catch up with this fellow Pecos Bill, but I sure did get my fill of stories about him.</p>
<p>I hear tell that Slue Foot Sue is going to make an honest man out of Bill and I'd like to be there for the weddin'. I hear tell that Texas is changin' their slogan. They were the State of Endless Stars, but when Bill got a yes to his proposal to Slue, he got drunk happy and shot out all the stars but one.</p>
<p>The governor is pushin' for the new slogan to be the Lone Star State. Sure will be easier to make a flag iffen he gets his way.</p>

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle"></i> <strong>About This Tale:</strong> Pecos Bill is one of America's most beloved tall tales, a legendary cowboy of the American Southwest. Like all tall tales, his story grows with each telling!
</div>`,
        meta_title: 'The Legend of Pecos Bill - Pecos River Traders',
        meta_description: 'Discover the legendary tale of Pecos Bill, the greatest cowboy hero of the American Southwest.',
        updated_at: '2025-11-10T08:00:00'
    },
    {
        id: 8,
        title: 'The Pecos River',
        slug: 'pecos-river',
        page_type: 'custom',
        footer_section: 'resources',
        show_in_footer: true,
        status: 'published',
        content: `<div class="text-center mb-4">
    <img src="/prt2/assets/images/180px-Pecos_River.jpg" alt="Pecos River" class="img-fluid mb-4" style="max-width: 300px;">
    <h2 style="color: #8B6C42;">PECOS</h2>
    <p class="lead">(Pay cuss)</p>
</div>

<h2 class="mb-4" style="color: #8B6C42;">About the River</h2>
<p>The Pecos River meanders lazily through the Northeastern part of New Mexico and continues quietly through western Texas, murmuring to dozens of small villages as it searches out the Rio Grande at the bottom.</p>
<p>Although not a well known as her sister, the Mississippi, it is far more famous. For one thing, it is the division between everything that is and everything that ain't. Everything east of the Pecos is "ain't" (except for Texas).</p>
<p>For instance, "The Best Barbeque West of the Pecos" or "The Biggest Festival West of the Pecos". No one brags about being east of the Pecos.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">To "Pecos" Someone</h2>
<p>John Troesser wrote: "Pecos used to be used as a verb. Like Shanghai. It meant to rob someone and roll the body down a steep riverbank where it was unlikely to be found until you were long gone. Murder was optional. It's not heard much anymore, since criminals now don't go to the effort of concealing their crimes.</p>

<div class="alert alert-warning">
    <h5>Conjugation of "Pecos":</h5>
    <table class="table table-sm mb-0">
        <tr>
            <td>I Pecos</td>
            <td>We Pecos</td>
        </tr>
        <tr>
            <td>You Pecos</td>
            <td>Y'all Pecos</td>
        </tr>
        <tr>
            <td>He, she or it Pecoses</td>
            <td>They Pecos</td>
        </tr>
    </table>
</div>

<h5 class="mt-4">Examples:</h5>
<ul>
    <li>My wife Pecosed her first three husbands.</li>
    <li>That was the Sheriff y'all Pecosed last night.</li>
    <li>I was learnin' my boy to Pecos when I got snake bit.</li>
    <li>There's been a lot of Pecosin' goin' on at the Sheffield Riverwalk.</li>
</ul>

<p class="mt-4">Hey, Maybe it'll catch on again. We can PECOS the party. Hon, would you Pecos that light and get some sleep. I feel a Pecos mood coming on. Did you enjoy the dinner, it was last nights road Pecos!</p>
<p>Maybe Pecosing got wore out ever since Judge Roy Bean pecosed everybody that was a guest in his courtroom – even a few lawyers I suspect.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">The Hero of the Pecos</h2>
<p>So the next time you hear the name Pecos, you'll be a little up on the history.</p>
<p>Many, many things have made the Pecos River famous, but none as much as it's all-time hero, <a href="pecos-bill.php">Pecos Bill</a>.</p>

<h2 class="mb-4 mt-5" style="color: #8B6C42;">The Texas River Walk</h2>
<p>Living in Texas, I get to see an awful lot of the Pecos, but aside from the Pecos, I think my favorite is the River Walk. Most of us older folks are vaguely aware that the famous Alamo is in San Antonio Texas, but just down the street from it is one of the prettiest riverbanks I have ever seen.</p>
<p>With walks shaded by trees and decked with flowers, the River Walk is lined on both sides with shops offering just about every souvenir imaginable. There are Paddlewheels that travel up and down that section of the river giving visitors a tour of the historic sites, complete with tour guides pointing them out and giving everyone the history behind them.</p>`,
        meta_title: 'About the Pecos River - Pecos River Traders',
        meta_description: 'Learn about the historic Pecos River that flows through New Mexico and Texas - the inspiration for Pecos River Traders.',
        updated_at: '2025-11-10T08:00:00'
    },
    {
        id: 9,
        title: 'Contact Us',
        slug: 'contact-us',
        page_type: 'info',
        footer_section: 'customer_service',
        show_in_footer: true,
        status: 'published',
        content: `<h2 class="mb-4" style="color: #8B6C42;">Send Us a Message</h2>
<p class="lead">We'd love to hear from you! Whether you have a question about products, orders, or anything else, our team is ready to answer.</p>

<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> For order issues, returns, or shipping questions, please log in to your account and submit a support request for faster tracking and response.
</div>

<h3 class="mb-4 mt-5" style="color: #8B6C42;">Contact Information</h3>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="text-center p-3 border rounded h-100">
            <i class="bi bi-envelope-fill display-4 mb-3" style="color: #990000;"></i>
            <h5>Email</h5>
            <a href="mailto:contact@pecosrivertraders.com">contact@pecosrivertraders.com</a>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="text-center p-3 border rounded h-100">
            <i class="bi bi-telephone-fill display-4 mb-3" style="color: #990000;"></i>
            <h5>Phone</h5>
            <a href="tel:+17179148124">717-914-8124</a>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="text-center p-3 border rounded h-100">
            <i class="bi bi-clock-fill display-4 mb-3" style="color: #990000;"></i>
            <h5>Business Hours</h5>
            <p class="mb-0 small">Mon-Fri: 9AM - 6PM<br>Sat: 10AM - 4PM<br>Sun: Closed</p>
        </div>
    </div>
</div>

<h3 class="mb-3 mt-5" style="color: #8B6C42;">Quick Links</h3>
<ul class="list-unstyled">
    <li class="mb-2"><i class="bi bi-chevron-right"></i> <a href="shipping-policy.php">Shipping Information</a></li>
    <li class="mb-2"><i class="bi bi-chevron-right"></i> <a href="return-policy.php">Return Policy</a></li>
    <li class="mb-2"><i class="bi bi-chevron-right"></i> <a href="privacy-policy.php">Privacy Policy</a></li>
</ul>

<h3 class="mb-3 mt-5" style="color: #8B6C42;">Common Questions</h3>
<p class="mb-2"><strong>Q: How long does shipping take?</strong></p>
<p class="mb-3 text-muted">A: Most orders arrive within 5-7 business days.</p>

<p class="mb-2"><strong>Q: What's your return policy?</strong></p>
<p class="mb-3 text-muted">A: We accept returns within 30 days of purchase.</p>

<p class="mb-2"><strong>Q: Do you offer bulk discounts?</strong></p>
<p class="mb-0 text-muted">A: Yes! Contact us for bulk order pricing.</p>`,
        meta_title: 'Contact Us - Pecos River Traders',
        meta_description: 'Get in touch with Pecos River Trading Company. Contact us for questions about products, orders, or customer service.',
        updated_at: '2025-11-10T08:00:00'
    }
];

document.addEventListener('DOMContentLoaded', function() {
    pageModal = new bootstrap.Modal(document.getElementById('pageModal'));
    previewPageModal = new bootstrap.Modal(document.getElementById('previewPageModal'));

    // Initialize Bootstrap tooltips for static buttons
    initTooltips();

    // Tab filtering
    document.querySelectorAll('#pageTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('#pageTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            renderPages();
        });
    });

    loadPages();

    // Initialize TinyMCE if available
    if (typeof tinymce !== 'undefined') {
        initTinyMCE();
    }

    // Check if URL contains a page ID to auto-open edit modal
    const pathMatch = window.location.pathname.match(/\/footer\/pages\/(\d+)/);
    if (pathMatch) {
        const pageId = parseInt(pathMatch[1]);
        // Wait for pages to load then open edit modal
        setTimeout(() => {
            if (window.pagesData) {
                editPage(pageId);
            }
        }, 500);
    }
});

function initTinyMCE() {
    if (tinymce.get('pageContent')) {
        tinymce.get('pageContent').remove();
    }

    tinymce.init({
        selector: '#pageContent',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link | code | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
    });
}

async function loadPages() {
    try {
        const response = await fetch(`${API_BASE}/admin/footer/pages`);
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                window.pagesData = data.data;
                renderPages();
                return;
            }
        }
    } catch (error) {
        console.log('API not available, using sample data');
    }

    window.pagesData = samplePages;
    renderPages();
}

function renderPages() {
    const tbody = document.getElementById('pagesTableBody');
    let pages = window.pagesData || samplePages;

    // Apply filter
    if (currentFilter !== 'all') {
        pages = pages.filter(p => p.page_type === currentFilter);
    }

    if (pages.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-x" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No pages found</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    pages.forEach(page => {
        const statusBadge = page.status === 'published'
            ? '<span class="badge bg-success">Published</span>'
            : '<span class="badge bg-warning text-dark">Draft</span>';

        const typeBadge = getTypeBadge(page.page_type);
        const sectionLabel = getSectionLabel(page.footer_section);
        const footerIcon = page.show_in_footer
            ? '<i class="bi bi-check-circle-fill text-success"></i>'
            : '<i class="bi bi-x-circle text-muted"></i>';

        html += `
            <tr data-id="${page.id}" onclick="highlightRow(event)" style="cursor: pointer;">
                <td><i class="bi bi-grip-vertical text-muted" style="cursor: grab;"></i></td>
                <td>
                    <strong>${page.title}</strong>
                    <br><small class="text-muted">/${page.slug}</small>
                </td>
                <td>${typeBadge}</td>
                <td>${sectionLabel || '<span class="text-muted">-</span>'}</td>
                <td class="text-center">${footerIcon}</td>
                <td>${statusBadge}</td>
                <td><small class="text-muted">${formatDate(page.updated_at)}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editPage(${page.id})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Page">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="duplicatePage(${page.id})" data-bs-toggle="tooltip" data-bs-placement="top" title="Duplicate Page">
                            <i class="bi bi-files"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deletePage(${page.id})" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Page">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    // Reinitialize tooltips after dynamic content is added
    initTooltips();
}

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

function getTypeBadge(type) {
    const badges = {
        policy: '<span class="badge bg-warning text-dark page-type-badge">Policy</span>',
        info: '<span class="badge bg-info page-type-badge">Info</span>',
        custom: '<span class="badge bg-success page-type-badge">Custom</span>'
    };
    return badges[type] || '<span class="badge bg-secondary page-type-badge">Other</span>';
}

function getSectionLabel(section) {
    const labels = {
        shop: 'Shop',
        resources: 'Resources',
        customer_service: 'Customer Service',
        connect: 'Connect'
    };
    return labels[section] || '';
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

function generateSlug() {
    const title = document.getElementById('pageTitle').value;
    const slug = title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('pageSlug').value = slug;
}

function showAddPageModal() {
    document.getElementById('pageModalTitle').textContent = 'Add New Page';
    document.getElementById('pageForm').reset();
    document.getElementById('pageId').value = '';
    document.getElementById('statusPublished').checked = true;
    document.getElementById('showInFooter').checked = true;

    // Clear TinyMCE if available
    if (typeof tinymce !== 'undefined' && tinymce.get('pageContent')) {
        tinymce.get('pageContent').setContent('');
    } else {
        document.getElementById('pageContent').value = '';
    }

    pageModal.show();
}

function editPage(id) {
    const pages = window.pagesData || samplePages;
    const page = pages.find(p => p.id === id);
    if (!page) return;

    document.getElementById('pageModalTitle').textContent = 'Edit Page';
    document.getElementById('pageId').value = page.id;
    document.getElementById('pageTitle').value = page.title;
    document.getElementById('pageSlug').value = page.slug;
    document.getElementById('pageType').value = page.page_type;
    document.getElementById('pageFooterSection').value = page.footer_section || '';
    document.getElementById('showInFooter').checked = page.show_in_footer;
    document.getElementById('metaTitle').value = page.meta_title || '';
    document.getElementById('metaDescription').value = page.meta_description || '';

    if (page.status === 'published') {
        document.getElementById('statusPublished').checked = true;
    } else {
        document.getElementById('statusDraft').checked = true;
    }

    // Set content
    if (typeof tinymce !== 'undefined' && tinymce.get('pageContent')) {
        tinymce.get('pageContent').setContent(page.content || '');
    } else {
        document.getElementById('pageContent').value = page.content || '';
    }

    pageModal.show();
}

async function savePage(status) {
    const id = document.getElementById('pageId').value;

    // Get content from TinyMCE or textarea
    let content = '';
    if (typeof tinymce !== 'undefined' && tinymce.get('pageContent')) {
        content = tinymce.get('pageContent').getContent();
    } else {
        content = document.getElementById('pageContent').value;
    }

    const data = {
        title: document.getElementById('pageTitle').value,
        slug: document.getElementById('pageSlug').value,
        page_type: document.getElementById('pageType').value,
        footer_section: document.getElementById('pageFooterSection').value || null,
        show_in_footer: document.getElementById('showInFooter').checked,
        status: status || (document.getElementById('statusPublished').checked ? 'published' : 'draft'),
        content: content,
        meta_title: document.getElementById('metaTitle').value,
        meta_description: document.getElementById('metaDescription').value
    };

    if (!data.title || !data.slug) {
        alert('Please fill in the required fields');
        return;
    }

    try {
        const url = id ? `${API_BASE}/admin/footer/pages/${id}` : `${API_BASE}/admin/footer/pages`;
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            pageModal.hide();
            loadPages();
            showToast('Page saved successfully', 'success');
        } else {
            // Demo mode
            savePageLocally(id, data);
        }
    } catch (error) {
        // Demo mode
        savePageLocally(id, data);
    }
}

function savePageLocally(id, data) {
    if (id) {
        const index = samplePages.findIndex(p => p.id === parseInt(id));
        if (index > -1) {
            samplePages[index] = { ...samplePages[index], ...data, updated_at: new Date().toISOString() };
        }
    } else {
        const newId = Math.max(...samplePages.map(p => p.id)) + 1;
        samplePages.push({ id: newId, ...data, updated_at: new Date().toISOString() });
    }
    window.pagesData = samplePages;
    pageModal.hide();
    renderPages();
    showToast('Page saved (demo mode)', 'success');
}

function duplicatePage(id) {
    const pages = window.pagesData || samplePages;
    const page = pages.find(p => p.id === id);
    if (!page) return;

    const newPage = {
        ...page,
        id: Math.max(...pages.map(p => p.id)) + 1,
        title: page.title + ' (Copy)',
        slug: page.slug + '-copy',
        status: 'draft',
        updated_at: new Date().toISOString()
    };

    samplePages.push(newPage);
    window.pagesData = samplePages;
    renderPages();
    showToast('Page duplicated', 'success');
}

async function deletePage(id) {
    if (!confirm('Are you sure you want to delete this page? This action cannot be undone.')) return;

    try {
        await fetch(`${API_BASE}/admin/footer/pages/${id}`, {
            method: 'DELETE'
        });
        loadPages();
        showToast('Page deleted', 'success');
    } catch (error) {
        // Demo mode
        const index = samplePages.findIndex(p => p.id === id);
        if (index > -1) {
            samplePages.splice(index, 1);
            window.pagesData = samplePages;
            renderPages();
            showToast('Page deleted (demo mode)', 'success');
        }
    }
}

function previewPage() {
    const title = document.getElementById('pageTitle').value || 'Untitled Page';

    let content = '';
    if (typeof tinymce !== 'undefined' && tinymce.get('pageContent')) {
        content = tinymce.get('pageContent').getContent();
    } else {
        content = document.getElementById('pageContent').value;
    }

    const preview = document.getElementById('pagePreviewContent');
    preview.innerHTML = `
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-card">
                        ${content || '<p class="text-muted">No content yet...</p>'}
                    </div>
                </div>
            </div>
        </div>
    `;

    previewPageModal.show();
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    toast.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
