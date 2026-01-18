@extends('layouts.admin')

@section('title', 'Shipping Settings')

@section('content')
<div class="page-header">
    <h1>Shipping Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">Settings</a></li>
            <li class="breadcrumb-item active">Shipping</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <!-- Shipping Zones -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Shipping Zones</h5>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                    <i class="bi bi-plus"></i> Add Zone
                </button>
            </div>
            <div class="card-body" id="zonesContainer">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading zones...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Sidebar -->
    <div class="col-lg-4">
        <!-- Free Shipping -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Free Shipping</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="enableFreeShipping">
                    <label class="form-check-label" for="enableFreeShipping">Enable Free Shipping</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Minimum Order Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="freeShippingMinimum" value="75">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Apply To</label>
                    <select class="form-select" id="freeShippingZones">
                        <option value="all">All Zones</option>
                        <option value="domestic">Domestic Only</option>
                        <option value="selected">Selected Zones</option>
                    </select>
                </div>
                <button class="btn btn-prt w-100" onclick="saveShippingSettings()">Save Settings</button>
            </div>
        </div>

        <!-- Shipping Classes -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Shipping Classes</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">Assign products to shipping classes for special rates.</p>
                <ul class="list-group list-group-flush" id="classesContainer">
                    <li class="list-group-item text-center">Loading...</li>
                </ul>
                <button class="btn btn-outline-primary btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="bi bi-plus"></i> Add Class
                </button>
            </div>
        </div>

        <!-- Carrier Integration -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Carrier Integration</h5>
            </div>
            <div class="card-body" id="carriersContainer">
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <small class="ms-2">Loading carriers...</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Zone Modal -->
<div class="modal fade" id="addZoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shipping Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Zone Name *</label>
                    <input type="text" class="form-control" id="newZoneName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Regions (comma-separated codes)</label>
                    <input type="text" class="form-control" id="newZoneRegions" placeholder="US,CA,MX">
                    <small class="text-muted">e.g., US, CA, GB, AU</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="addZone()">Add Zone</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Zone Modal -->
<div class="modal fade" id="editZoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shipping Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editZoneId">
                <div class="mb-3">
                    <label class="form-label">Zone Name *</label>
                    <input type="text" class="form-control" id="editZoneName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Regions (comma-separated codes)</label>
                    <input type="text" class="form-control" id="editZoneRegions">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="updateZone()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Method Modal -->
<div class="modal fade" id="addMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shipping Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="methodZoneId">
                <div class="mb-3">
                    <label class="form-label">Method Name *</label>
                    <input type="text" class="form-control" id="newMethodName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rate *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="newMethodRate" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Delivery Time</label>
                    <input type="text" class="form-control" id="newMethodDelivery" placeholder="3-5 business days">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="addMethod()">Add Method</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Method Modal -->
<div class="modal fade" id="editMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shipping Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editMethodId">
                <div class="mb-3">
                    <label class="form-label">Method Name *</label>
                    <input type="text" class="form-control" id="editMethodName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rate *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="editMethodRate" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Delivery Time</label>
                    <input type="text" class="form-control" id="editMethodDelivery">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="editMethodActive" checked>
                    <label class="form-check-label" for="editMethodActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="updateMethod()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shipping Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Class Name *</label>
                    <input type="text" class="form-control" id="newClassName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Surcharge *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="newClassSurcharge" value="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="addClass()">Add Class</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shipping Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editClassId">
                <div class="mb-3">
                    <label class="form-label">Class Name *</label>
                    <input type="text" class="form-control" id="editClassName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Surcharge *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="editClassSurcharge" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="updateClass()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Carrier Configuration Modal -->
<div class="modal fade" id="carrierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure <span id="carrierModalName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="carrierId">
                <div class="mb-3">
                    <label class="form-label">API Key</label>
                    <input type="text" class="form-control" id="carrierApiKey" placeholder="Enter API key">
                </div>
                <div class="mb-3">
                    <label class="form-label">API Secret</label>
                    <input type="password" class="form-control" id="carrierApiSecret" placeholder="Enter API secret">
                </div>
                <div class="mb-3">
                    <label class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="carrierAccountNumber" placeholder="Enter account number">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="carrierEnabled" checked>
                    <label class="form-check-label" for="carrierEnabled">Enabled</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="saveCarrier()">Save & Connect</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';

document.addEventListener('DOMContentLoaded', function() {
    loadZones();
    loadClasses();
    loadSettings();
    loadCarriers();
});

async function loadZones() {
    try {
        const response = await fetch(`${API_BASE}/admin/shipping/zones`);
        const data = await response.json();

        if (data.success) {
            renderZones(data.data);
        }
    } catch (error) {
        console.error('Error loading zones:', error);
        document.getElementById('zonesContainer').innerHTML = '<p class="text-danger">Error loading zones</p>';
    }
}

function renderZones(zones) {
    const container = document.getElementById('zonesContainer');

    if (zones.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No shipping zones configured</p>';
        return;
    }

    let html = '';
    zones.forEach(zone => {
        html += `
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">${zone.name}</h6>
                    <div>
                        <button class="btn btn-sm btn-outline-success me-1" onclick="showAddMethodModal(${zone.id})" title="Add Method">
                            <i class="bi bi-plus"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="showEditZoneModal(${zone.id}, '${zone.name}', '${zone.regions || ''}')" title="Edit Zone">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteZone(${zone.id})" title="Delete Zone">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Rate</th>
                            <th>Est. Delivery</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        ${zone.methods && zone.methods.length > 0 ? zone.methods.map(method => `
                            <tr>
                                <td>${method.name}</td>
                                <td>$${parseFloat(method.rate).toFixed(2)}</td>
                                <td>${method.delivery_time || 'N/A'}</td>
                                <td><span class="status-badge ${method.is_active ? 'active' : 'inactive'}">${method.is_active ? 'Active' : 'Inactive'}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="showEditMethodModal(${method.id}, '${method.name}', ${method.rate}, '${method.delivery_time || ''}', ${method.is_active})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMethod(${method.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('') : '<tr><td colspan="5" class="text-muted text-center">No methods configured</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
    });

    container.innerHTML = html;
}

async function loadClasses() {
    try {
        const response = await fetch(`${API_BASE}/admin/shipping/classes`);
        const data = await response.json();

        if (data.success) {
            renderClasses(data.data);
        }
    } catch (error) {
        console.error('Error loading classes:', error);
    }
}

function renderClasses(classes) {
    const container = document.getElementById('classesContainer');

    let html = '';
    classes.forEach(cls => {
        html += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${cls.name}</span>
                <div>
                    ${cls.is_default ? '<span class="badge bg-secondary me-2">Default</span>' : `<span class="badge bg-primary me-2">+$${parseFloat(cls.surcharge).toFixed(2)}</span>`}
                    ${!cls.is_default ? `
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="showEditClassModal(${cls.id}, '${cls.name}', ${cls.surcharge})"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteClass(${cls.id})"><i class="bi bi-trash"></i></button>
                    ` : ''}
                </div>
            </li>
        `;
    });

    container.innerHTML = html;
}

async function loadSettings() {
    try {
        const response = await fetch(`${API_BASE}/admin/shipping/settings`);
        const data = await response.json();

        if (data.success) {
            document.getElementById('enableFreeShipping').checked = data.data.free_shipping_enabled === '1';
            document.getElementById('freeShippingMinimum').value = data.data.free_shipping_minimum || 75;
            document.getElementById('freeShippingZones').value = data.data.free_shipping_zones || 'domestic';
        }
    } catch (error) {
        console.error('Error loading settings:', error);
    }
}

async function addZone() {
    const name = document.getElementById('newZoneName').value;
    const regions = document.getElementById('newZoneRegions').value;

    if (!name) {
        alert('Zone name is required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/zones`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, regions })
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addZoneModal')).hide();
            document.getElementById('newZoneName').value = '';
            document.getElementById('newZoneRegions').value = '';
            loadZones();
        } else {
            alert(data.message || 'Error adding zone');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding zone');
    }
}

function showEditZoneModal(id, name, regions) {
    document.getElementById('editZoneId').value = id;
    document.getElementById('editZoneName').value = name;
    document.getElementById('editZoneRegions').value = regions;
    new bootstrap.Modal(document.getElementById('editZoneModal')).show();
}

async function updateZone() {
    const id = document.getElementById('editZoneId').value;
    const name = document.getElementById('editZoneName').value;
    const regions = document.getElementById('editZoneRegions').value;

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/zones/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, regions })
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editZoneModal')).hide();
            loadZones();
        } else {
            alert(data.message || 'Error updating zone');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating zone');
    }
}

async function deleteZone(id) {
    if (!confirm('Delete this zone and all its methods?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/zones/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            loadZones();
        } else {
            alert(data.message || 'Error deleting zone');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting zone');
    }
}

function showAddMethodModal(zoneId) {
    document.getElementById('methodZoneId').value = zoneId;
    document.getElementById('newMethodName').value = '';
    document.getElementById('newMethodRate').value = '';
    document.getElementById('newMethodDelivery').value = '';
    new bootstrap.Modal(document.getElementById('addMethodModal')).show();
}

async function addMethod() {
    const zone_id = document.getElementById('methodZoneId').value;
    const name = document.getElementById('newMethodName').value;
    const rate = document.getElementById('newMethodRate').value;
    const delivery_time = document.getElementById('newMethodDelivery').value;

    if (!name || !rate) {
        alert('Name and rate are required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/methods`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ zone_id, name, rate, delivery_time })
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addMethodModal')).hide();
            loadZones();
        } else {
            alert(data.message || 'Error adding method');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding method');
    }
}

function showEditMethodModal(id, name, rate, delivery_time, is_active) {
    document.getElementById('editMethodId').value = id;
    document.getElementById('editMethodName').value = name;
    document.getElementById('editMethodRate').value = rate;
    document.getElementById('editMethodDelivery').value = delivery_time;
    document.getElementById('editMethodActive').checked = is_active;
    new bootstrap.Modal(document.getElementById('editMethodModal')).show();
}

async function updateMethod() {
    const id = document.getElementById('editMethodId').value;
    const name = document.getElementById('editMethodName').value;
    const rate = document.getElementById('editMethodRate').value;
    const delivery_time = document.getElementById('editMethodDelivery').value;
    const is_active = document.getElementById('editMethodActive').checked;

    if (!name || !rate) {
        alert('Name and rate are required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/methods/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, rate, delivery_time, is_active })
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editMethodModal')).hide();
            loadZones();
        } else {
            alert(data.message || 'Error updating method');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating method');
    }
}

async function deleteMethod(id) {
    if (!confirm('Delete this shipping method?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/methods/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            loadZones();
        } else {
            alert(data.message || 'Error deleting method');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting method');
    }
}

async function addClass() {
    const name = document.getElementById('newClassName').value;
    const surcharge = document.getElementById('newClassSurcharge').value;

    if (!name) {
        alert('Class name is required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/classes`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, surcharge })
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addClassModal')).hide();
            document.getElementById('newClassName').value = '';
            document.getElementById('newClassSurcharge').value = '0';
            loadClasses();
        } else {
            alert(data.message || 'Error adding class');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding class');
    }
}

function showEditClassModal(id, name, surcharge) {
    document.getElementById('editClassId').value = id;
    document.getElementById('editClassName').value = name;
    document.getElementById('editClassSurcharge').value = surcharge;
    new bootstrap.Modal(document.getElementById('editClassModal')).show();
}

async function updateClass() {
    const id = document.getElementById('editClassId').value;
    const name = document.getElementById('editClassName').value;
    const surcharge = document.getElementById('editClassSurcharge').value;

    if (!name) {
        alert('Class name is required');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/classes/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, surcharge })
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editClassModal')).hide();
            loadClasses();
        } else {
            alert(data.message || 'Error updating class');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating class');
    }
}

async function deleteClass(id) {
    if (!confirm('Delete this shipping class?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/classes/${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            loadClasses();
        } else {
            alert(data.message || 'Error deleting class');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting class');
    }
}

async function saveShippingSettings() {
    const settings = {
        free_shipping_enabled: document.getElementById('enableFreeShipping').checked ? '1' : '0',
        free_shipping_minimum: document.getElementById('freeShippingMinimum').value,
        free_shipping_zones: document.getElementById('freeShippingZones').value
    };

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/settings`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(settings)
        });

        const data = await response.json();

        if (data.success) {
            alert('Settings saved successfully');
        } else {
            alert(data.message || 'Error saving settings');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error saving settings');
    }
}

// Carrier functions
async function loadCarriers() {
    try {
        const response = await fetch(`${API_BASE}/admin/shipping/carriers`);
        const data = await response.json();

        if (data.success) {
            renderCarriers(data.data);
        }
    } catch (error) {
        console.error('Error loading carriers:', error);
        document.getElementById('carriersContainer').innerHTML = '<p class="text-danger small">Error loading carriers</p>';
    }
}

function renderCarriers(carriers) {
    const container = document.getElementById('carriersContainer');

    if (!carriers || carriers.length === 0) {
        container.innerHTML = '<p class="text-muted small">No carriers configured</p>';
        return;
    }

    let html = '';
    carriers.forEach((carrier, index) => {
        const isConnected = carrier.is_connected == 1;
        const isEnabled = carrier.is_enabled == 1;

        html += `
            <div class="d-flex align-items-center ${index < carriers.length - 1 ? 'mb-3' : ''}">
                <i class="bi bi-truck me-2"></i>
                <span>${carrier.carrier_name}</span>
                <span class="badge ${isConnected ? 'bg-success' : 'bg-secondary'} ms-auto me-2">
                    ${isConnected ? 'Connected' : 'Not Connected'}
                </span>
                <button class="btn btn-sm btn-outline-primary" onclick="showCarrierModal(${carrier.id}, '${carrier.carrier_name}')" title="Configure">
                    <i class="bi bi-gear"></i>
                </button>
            </div>
        `;
    });

    container.innerHTML = html;
}

async function showCarrierModal(id, name) {
    document.getElementById('carrierId').value = id;
    document.getElementById('carrierModalName').textContent = name;

    // Load carrier details
    try {
        const response = await fetch(`${API_BASE}/admin/shipping/carriers/${id}`);
        const data = await response.json();

        if (data.success) {
            document.getElementById('carrierApiKey').value = data.data.api_key || '';
            document.getElementById('carrierApiSecret').value = ''; // Don't show secret
            document.getElementById('carrierAccountNumber').value = data.data.account_number || '';
            document.getElementById('carrierEnabled').checked = data.data.is_enabled == 1;
        }
    } catch (error) {
        console.error('Error loading carrier:', error);
    }

    new bootstrap.Modal(document.getElementById('carrierModal')).show();
}

async function saveCarrier() {
    const id = document.getElementById('carrierId').value;
    const api_key = document.getElementById('carrierApiKey').value;
    const api_secret = document.getElementById('carrierApiSecret').value;
    const account_number = document.getElementById('carrierAccountNumber').value;
    const is_enabled = document.getElementById('carrierEnabled').checked;

    try {
        // First update credentials
        const updateData = {
            api_key,
            account_number,
            is_enabled
        };

        if (api_secret) {
            updateData.api_secret = api_secret;
        }

        const updateResponse = await fetch(`${API_BASE}/admin/shipping/carriers/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updateData)
        });

        const updateResult = await updateResponse.json();

        if (!updateResult.success) {
            alert(updateResult.message || 'Error updating carrier');
            return;
        }

        // Try to connect if we have credentials
        if (api_key || account_number) {
            const connectResponse = await fetch(`${API_BASE}/admin/shipping/carriers/${id}/connect`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });

            const connectResult = await connectResponse.json();

            if (connectResult.success) {
                alert('Carrier configured and connected successfully');
            } else {
                alert('Credentials saved but connection failed: ' + (connectResult.message || 'Unknown error'));
            }
        } else {
            alert('Carrier settings saved');
        }

        bootstrap.Modal.getInstance(document.getElementById('carrierModal')).hide();
        loadCarriers();

    } catch (error) {
        console.error('Error:', error);
        alert('Error saving carrier: ' + error.message);
    }
}

async function disconnectCarrier(id) {
    if (!confirm('Disconnect this carrier?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/shipping/carriers/${id}/disconnect`, {
            method: 'POST'
        });

        const data = await response.json();

        if (data.success) {
            loadCarriers();
        } else {
            alert(data.message || 'Error disconnecting carrier');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error disconnecting carrier');
    }
}
</script>
@endpush
