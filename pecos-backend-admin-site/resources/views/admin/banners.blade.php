@extends('layouts.admin')

@section('title', 'Homepage Banners')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Homepage Banners</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Homepage Banners</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-prt" onclick="showAddBannerModal()">
        <i class="bi bi-plus-lg me-1"></i> Add Banner
    </button>
</div>

<!-- Carousel Settings Card -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Banner Carousel Settings</h5>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="carouselEnabled" onchange="saveBannerSettings()">
            <label class="form-check-label" for="carouselEnabled">Enable Carousel</label>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Slide Duration (seconds)</label>
                <input type="number" class="form-control" id="slideDuration" min="1" max="30" value="5">
            </div>
            <div class="col-md-3">
                <label class="form-label">Transition</label>
                <select class="form-select" id="transitionStyle">
                    <option value="slide">Slide</option>
                    <option value="fade">Fade</option>
                    <option value="none">None</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Desktop Height (px)</label>
                <input type="number" class="form-control" id="bannerHeight" min="100" max="800" value="400">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mobile Height (px)</label>
                <input type="number" class="form-control" id="mobileBannerHeight" min="100" max="500" value="250">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showIndicators" checked>
                    <label class="form-check-label" for="showIndicators">Show Slide Indicators</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showControls" checked>
                    <label class="form-check-label" for="showControls">Show Navigation Controls</label>
                </div>
            </div>
        </div>
        <button class="btn btn-prt mt-3" onclick="saveBannerSettings()">
            <i class="bi bi-check-lg me-1"></i> Save Settings
        </button>
    </div>
</div>

<!-- Banners List -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-images me-2"></i>Banners</h5>
        <small class="text-muted">Drag to reorder</small>
    </div>
    <div class="card-body">
        <div id="bannersList">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bannerModalTitle">Add Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bannerForm" enctype="multipart/form-data">
                    <input type="hidden" id="bannerId">

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Info -->
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="bannerTitle" required placeholder="Summer Sale Banner">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" id="bannerSubtitle" placeholder="Up to 50% off on selected items">
                            </div>

                            <!-- Images -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Desktop Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="desktopImage" accept="image/*" onchange="previewImage(this, 'desktopPreview')">
                                    <small class="text-muted">Recommended: 1920x600px, Max 5MB</small>
                                    <div id="desktopPreview" class="mt-2"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Image (Optional)</label>
                                    <input type="file" class="form-control" id="mobileImage" accept="image/*" onchange="previewImage(this, 'mobilePreview')">
                                    <small class="text-muted">Recommended: 768x400px, Max 3MB</small>
                                    <div id="mobilePreview" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Link -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Link URL</label>
                                    <input type="url" class="form-control" id="bannerLinkUrl" placeholder="https://example.com/sale">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="bannerLinkText" placeholder="Shop Now">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alt Text</label>
                                <input type="text" class="form-control" id="bannerAltText" placeholder="Summer sale promotional banner">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Positioning & Styling -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Display Settings</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Banner Position</label>
                                        <select class="form-select" id="bannerPosition">
                                            <option value="full">Full Width</option>
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Text Position</label>
                                        <select class="form-select" id="textPosition">
                                            <option value="left">Left</option>
                                            <option value="center" selected>Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Overlay Color</label>
                                        <input type="text" class="form-control" id="overlayColor" value="rgba(0,0,0,0.3)" placeholder="rgba(0,0,0,0.3)">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Text Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="bannerTextColor" value="#FFFFFF">
                                            <input type="text" class="form-control" id="bannerTextColorText" value="#FFFFFF">
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">Scheduling</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="datetime-local" class="form-control" id="bannerStartDate">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">End Date</label>
                                        <input type="datetime-local" class="form-control" id="bannerEndDate">
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bannerActive" checked>
                                        <label class="form-check-label" for="bannerActive">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveBanner()">
                    <i class="bi bi-check-lg me-1"></i> Save Banner
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.banner-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
    transition: box-shadow 0.2s;
}
.banner-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.banner-thumbnail {
    width: 200px;
    height: 100px;
    object-fit: cover;
    background-color: #f8f9fa;
}
.banner-info {
    flex: 1;
    padding: 1rem;
}
.image-preview {
    max-width: 100%;
    max-height: 150px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
const STOREFRONT_URL = '{{ rtrim(env("STOREFRONT_URL", "http://localhost:8300"), "/") }}';
let bannerModal;

document.addEventListener('DOMContentLoaded', function() {
    bannerModal = new bootstrap.Modal(document.getElementById('bannerModal'));
    loadBanners();
    setupColorSync();
});

function setupColorSync() {
    const colorInput = document.getElementById('bannerTextColor');
    const textInput = document.getElementById('bannerTextColorText');

    colorInput.addEventListener('input', () => {
        textInput.value = colorInput.value;
    });

    textInput.addEventListener('input', () => {
        if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
            colorInput.value = textInput.value;
        }
    });
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="image-preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}

async function loadBanners() {
    try {
        const response = await fetch(`${API_BASE}/admin/banners`);
        const data = await response.json();

        // Update settings
        if (data.settings) {
            document.getElementById('carouselEnabled').checked = data.settings.carousel_enabled;
            document.getElementById('slideDuration').value = data.settings.slide_duration;
            document.getElementById('transitionStyle').value = data.settings.transition;
            document.getElementById('bannerHeight').value = data.settings.banner_height;
            document.getElementById('mobileBannerHeight').value = data.settings.mobile_banner_height;
            document.getElementById('showIndicators').checked = data.settings.show_indicators;
            document.getElementById('showControls').checked = data.settings.show_controls;
        }

        renderBannersList(data.data);
    } catch (error) {
        console.error('Error loading banners:', error);
        document.getElementById('bannersList').innerHTML = `
            <div class="alert alert-danger">Error loading banners. Please try again.</div>
        `;
    }
}

function renderBannersList(banners) {
    const container = document.getElementById('bannersList');

    if (!banners || banners.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="bi bi-image" style="font-size: 3rem;"></i>
                <p class="mt-2">No banners yet. Click "Add Banner" to create one.</p>
            </div>
        `;
        return;
    }

    let html = '<div id="sortableBanners">';
    banners.forEach((banner, index) => {
        const statusBadge = banner.is_active
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';

        const imageUrl = banner.desktop_image.startsWith('http')
            ? banner.desktop_image
            : `${STOREFRONT_URL}/${banner.desktop_image}`;

        html += `
            <div class="banner-card d-flex" data-id="${banner.id}">
                <div class="d-flex align-items-center px-3 bg-light">
                    <i class="bi bi-grip-vertical text-muted" style="cursor: grab; font-size: 1.2rem;"></i>
                </div>
                <img src="${imageUrl}" alt="${banner.alt_text || banner.title}" class="banner-thumbnail"
                     onerror="this.src='https://placehold.co/200x100?text=No+Image'">
                <div class="banner-info">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${banner.title}</h6>
                            <p class="text-muted small mb-2">${banner.subtitle || ''}</p>
                            <div class="d-flex gap-2 flex-wrap">
                                ${statusBadge}
                                <span class="badge bg-info">Position: ${banner.position}</span>
                                <span class="badge bg-secondary">Text: ${banner.text_position}</span>
                                ${banner.link_url ? `<span class="badge bg-primary"><i class="bi bi-link-45deg"></i> ${banner.link_text || 'Link'}</span>` : ''}
                            </div>
                            ${banner.start_date || banner.end_date ? `
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-calendar me-1"></i>
                                    ${banner.start_date ? formatDate(banner.start_date) : 'Always'}
                                    -
                                    ${banner.end_date ? formatDate(banner.end_date) : 'Forever'}
                                </small>
                            ` : ''}
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editBanner(${banner.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(${banner.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    container.innerHTML = html;

    // Initialize sortable
    new Sortable(document.getElementById('sortableBanners'), {
        animation: 150,
        handle: '.bi-grip-vertical',
        onEnd: function(evt) {
            saveOrder();
        }
    });
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function showAddBannerModal() {
    document.getElementById('bannerModalTitle').textContent = 'Add Banner';
    document.getElementById('bannerForm').reset();
    document.getElementById('bannerId').value = '';
    document.getElementById('bannerTextColor').value = '#FFFFFF';
    document.getElementById('bannerTextColorText').value = '#FFFFFF';
    document.getElementById('overlayColor').value = 'rgba(0,0,0,0.3)';
    document.getElementById('bannerActive').checked = true;
    document.getElementById('desktopPreview').innerHTML = '';
    document.getElementById('mobilePreview').innerHTML = '';
    bannerModal.show();
}

async function editBanner(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/banners/${id}`);
        const data = await response.json();
        const banner = data.data;

        if (!banner) return;

        document.getElementById('bannerModalTitle').textContent = 'Edit Banner';
        document.getElementById('bannerId').value = banner.id;
        document.getElementById('bannerTitle').value = banner.title;
        document.getElementById('bannerSubtitle').value = banner.subtitle || '';
        document.getElementById('bannerLinkUrl').value = banner.link_url || '';
        document.getElementById('bannerLinkText').value = banner.link_text || '';
        document.getElementById('bannerAltText').value = banner.alt_text || '';
        document.getElementById('bannerPosition').value = banner.position;
        document.getElementById('textPosition').value = banner.text_position;
        document.getElementById('overlayColor').value = banner.overlay_color;
        document.getElementById('bannerTextColor').value = banner.text_color;
        document.getElementById('bannerTextColorText').value = banner.text_color;
        document.getElementById('bannerActive').checked = banner.is_active;

        if (banner.start_date) {
            document.getElementById('bannerStartDate').value = banner.start_date.slice(0, 16);
        }
        if (banner.end_date) {
            document.getElementById('bannerEndDate').value = banner.end_date.slice(0, 16);
        }

        // Show existing images
        const desktopUrl = banner.desktop_image.startsWith('http')
            ? banner.desktop_image
            : `${STOREFRONT_URL}/${banner.desktop_image}`;
        document.getElementById('desktopPreview').innerHTML = `<img src="${desktopUrl}" class="image-preview">`;

        if (banner.mobile_image) {
            const mobileUrl = banner.mobile_image.startsWith('http')
                ? banner.mobile_image
                : `${STOREFRONT_URL}/${banner.mobile_image}`;
            document.getElementById('mobilePreview').innerHTML = `<img src="${mobileUrl}" class="image-preview">`;
        } else {
            document.getElementById('mobilePreview').innerHTML = '';
        }

        bannerModal.show();
    } catch (error) {
        console.error('Error loading banner:', error);
        alert('Error loading banner');
    }
}

async function saveBanner() {
    const id = document.getElementById('bannerId').value;
    const formData = new FormData();

    formData.append('title', document.getElementById('bannerTitle').value);
    formData.append('subtitle', document.getElementById('bannerSubtitle').value);
    formData.append('link_url', document.getElementById('bannerLinkUrl').value);
    formData.append('link_text', document.getElementById('bannerLinkText').value);
    formData.append('alt_text', document.getElementById('bannerAltText').value);
    formData.append('position', document.getElementById('bannerPosition').value);
    formData.append('text_position', document.getElementById('textPosition').value);
    formData.append('overlay_color', document.getElementById('overlayColor').value);
    formData.append('text_color', document.getElementById('bannerTextColor').value);
    formData.append('is_active', document.getElementById('bannerActive').checked ? 1 : 0);
    formData.append('start_date', document.getElementById('bannerStartDate').value);
    formData.append('end_date', document.getElementById('bannerEndDate').value);

    const desktopImage = document.getElementById('desktopImage').files[0];
    const mobileImage = document.getElementById('mobileImage').files[0];

    if (desktopImage) {
        formData.append('desktop_image', desktopImage);
    } else if (!id) {
        alert('Please select a desktop image');
        return;
    }

    if (mobileImage) {
        formData.append('mobile_image', mobileImage);
    }

    try {
        let url = `${API_BASE}/admin/banners`;
        let method = 'POST';

        if (id) {
            url = `${API_BASE}/admin/banners/${id}`;
            formData.append('_method', 'PUT');
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            bannerModal.hide();
            loadBanners();
            showToast('Banner saved successfully', 'success');
        } else {
            const error = await response.json();
            alert('Error: ' + (error.message || JSON.stringify(error.errors) || 'Failed to save'));
        }
    } catch (error) {
        console.error('Error saving banner:', error);
        alert('Error saving banner');
    }
}

async function deleteBanner(id) {
    if (!confirm('Are you sure you want to delete this banner?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/banners/${id}`, {
            method: 'DELETE'
        });

        if (response.ok) {
            loadBanners();
            showToast('Banner deleted', 'success');
        }
    } catch (error) {
        console.error('Error deleting banner:', error);
        alert('Error deleting banner');
    }
}

async function saveOrder() {
    const items = document.querySelectorAll('#sortableBanners .banner-card');
    const order = Array.from(items).map((item, index) => ({
        id: parseInt(item.dataset.id),
        display_order: index
    }));

    try {
        await fetch(`${API_BASE}/admin/banners/reorder`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });
    } catch (error) {
        console.error('Error saving order:', error);
    }
}

async function saveBannerSettings() {
    const data = {
        carousel_enabled: document.getElementById('carouselEnabled').checked,
        slide_duration: parseInt(document.getElementById('slideDuration').value),
        transition: document.getElementById('transitionStyle').value,
        banner_height: parseInt(document.getElementById('bannerHeight').value),
        mobile_banner_height: parseInt(document.getElementById('mobileBannerHeight').value),
        show_indicators: document.getElementById('showIndicators').checked,
        show_controls: document.getElementById('showControls').checked
    };

    try {
        const response = await fetch(`${API_BASE}/admin/banners/settings`, {
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
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    toast.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
