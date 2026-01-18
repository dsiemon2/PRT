@extends('layouts.admin')

@section('title', 'Product Variants')
@section('page-title', 'Product Variants & SKU Management')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Total Variants</h6>
                    <h2 id="total-variants">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Active Variants</h6>
                    <h2 id="active-variants">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="card-title">Low Stock</h6>
                    <h2 id="low-stock">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Out of Stock</h6>
                    <h2 id="out-of-stock">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="variantTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="attributes-tab" data-bs-toggle="tab" href="#attributes" role="tab">Attribute Types</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="values-tab" data-bs-toggle="tab" href="#values" role="tab">Attribute Values</a>
        </li>
    </ul>

    <div class="tab-content" id="variantTabContent">
        <!-- Attribute Types Tab -->
        <div class="tab-pane fade show active" id="attributes" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Attribute Types</h5>
                    <button class="btn btn-primary btn-sm" onclick="showAttributeTypeModal()">
                        <i class="fas fa-plus"></i> New Attribute Type
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="attribute-types-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Display Type</th>
                                    <th>Variation</th>
                                    <th>Filterable</th>
                                    <th>Values</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attribute Values Tab -->
        <div class="tab-pane fade" id="values" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Attribute Values</h5>
                    <div>
                        <select class="form-select form-select-sm d-inline-block w-auto me-2" id="filter-type" onchange="loadAttributeValues()">
                            <option value="">All Types</option>
                        </select>
                        <button class="btn btn-primary btn-sm" onclick="showAttributeValueModal()">
                            <i class="fas fa-plus"></i> New Value
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="attribute-values-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Label</th>
                                    <th>Swatch</th>
                                    <th>Order</th>
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
    </div>
</div>

<!-- Attribute Type Modal -->
<div class="modal fade" id="attributeTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attribute Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="attribute-type-form">
                    <input type="hidden" id="type-id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="type-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="type-code" required>
                        <small class="text-muted">Unique identifier (lowercase, no spaces)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Type</label>
                        <select class="form-select" id="type-display" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="type-order" value="0">
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="type-visible" checked>
                        <label class="form-check-label" for="type-visible">Visible to customers</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="type-variation" checked>
                        <label class="form-check-label" for="type-variation">Creates product variations</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="type-filterable" checked>
                        <label class="form-check-label" for="type-filterable">Filterable in search</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAttributeType()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Attribute Value Modal -->
<div class="modal fade" id="attributeValueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attribute Value</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="attribute-value-form">
                    <input type="hidden" id="value-id">
                    <div class="mb-3">
                        <label class="form-label">Attribute Type</label>
                        <select class="form-select" id="value-type" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Value</label>
                        <input type="text" class="form-control" id="value-value" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Label (Display Name)</label>
                        <input type="text" class="form-control" id="value-label">
                        <small class="text-muted">Optional. Uses value if empty.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Swatch Value</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="value-swatch" placeholder="#000000 or image URL">
                            <input type="color" class="form-control form-control-color" id="value-color-picker" onchange="document.getElementById('value-swatch').value = this.value">
                        </div>
                        <small class="text-muted">For color swatches: hex color or image URL</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="value-order" value="0">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="value-active" checked>
                        <label class="form-check-label" for="value-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAttributeValue()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url") }}/api/v1';
let displayTypes = {};
let attributeTypes = [];

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadDisplayTypes();
    loadAttributeTypes();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/variants/stats`);
        const data = await response.json();
        const stats = data.data;

        document.getElementById('total-variants').textContent = stats.total_variants || 0;
        document.getElementById('active-variants').textContent = stats.active_variants || 0;
        document.getElementById('low-stock').textContent = stats.low_stock_variants || 0;
        document.getElementById('out-of-stock').textContent = stats.out_of_stock_variants || 0;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadDisplayTypes() {
    try {
        const response = await fetch(`${API_BASE}/admin/variants/display-types`);
        const data = await response.json();
        displayTypes = data.data;

        const select = document.getElementById('type-display');
        select.innerHTML = '';
        Object.entries(displayTypes).forEach(([key, label]) => {
            select.innerHTML += `<option value="${key}">${label}</option>`;
        });
    } catch (error) {
        console.error('Error loading display types:', error);
    }
}

async function loadAttributeTypes() {
    try {
        const response = await fetch(`${API_BASE}/admin/attribute-types`);
        const data = await response.json();
        attributeTypes = data.data || [];

        const tbody = document.querySelector('#attribute-types-table tbody');
        tbody.innerHTML = '';

        attributeTypes.forEach(type => {
            tbody.innerHTML += `
                <tr>
                    <td>${type.sort_order}</td>
                    <td><strong>${type.name}</strong></td>
                    <td><code>${type.code}</code></td>
                    <td>${displayTypes[type.display_type] || type.display_type}</td>
                    <td>${type.is_variation ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>'}</td>
                    <td>${type.is_filterable ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>'}</td>
                    <td><span class="badge bg-info">${type.values?.length || 0}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editAttributeType(${type.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAttributeType(${type.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        // Update filter dropdown
        const filterSelect = document.getElementById('filter-type');
        const valueTypeSelect = document.getElementById('value-type');
        filterSelect.innerHTML = '<option value="">All Types</option>';
        valueTypeSelect.innerHTML = '';
        attributeTypes.forEach(type => {
            filterSelect.innerHTML += `<option value="${type.id}">${type.name}</option>`;
            valueTypeSelect.innerHTML += `<option value="${type.id}">${type.name}</option>`;
        });

        loadAttributeValues();
    } catch (error) {
        console.error('Error loading attribute types:', error);
    }
}

async function loadAttributeValues() {
    const filterTypeId = document.getElementById('filter-type').value;
    const tbody = document.querySelector('#attribute-values-table tbody');
    tbody.innerHTML = '';

    for (const type of attributeTypes) {
        if (filterTypeId && type.id != filterTypeId) continue;

        (type.values || []).forEach(val => {
            let swatchDisplay = '-';
            if (val.swatch_value) {
                if (val.swatch_value.startsWith('#')) {
                    swatchDisplay = `<span class="d-inline-block" style="width:24px;height:24px;background:${val.swatch_value};border:1px solid #ccc;border-radius:3px;"></span>`;
                } else {
                    swatchDisplay = `<img src="${val.swatch_value}" style="width:24px;height:24px;object-fit:cover;border-radius:3px;">`;
                }
            }

            tbody.innerHTML += `
                <tr>
                    <td><small class="text-muted">${type.name}</small></td>
                    <td><strong>${val.value}</strong></td>
                    <td>${val.label || '-'}</td>
                    <td>${swatchDisplay}</td>
                    <td>${val.sort_order}</td>
                    <td><span class="badge bg-${val.is_active ? 'success' : 'secondary'}">${val.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editAttributeValue(${val.id}, ${type.id}, '${val.value}', '${val.label || ''}', '${val.swatch_value || ''}', ${val.sort_order}, ${val.is_active})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAttributeValue(${val.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    }
}

// Attribute Type CRUD
function showAttributeTypeModal() {
    document.getElementById('attribute-type-form').reset();
    document.getElementById('type-id').value = '';
    new bootstrap.Modal(document.getElementById('attributeTypeModal')).show();
}

async function editAttributeType(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/attribute-types/${id}`);
        const data = await response.json();
        const t = data.data;

        document.getElementById('type-id').value = t.id;
        document.getElementById('type-name').value = t.name;
        document.getElementById('type-code').value = t.code;
        document.getElementById('type-display').value = t.display_type;
        document.getElementById('type-order').value = t.sort_order;
        document.getElementById('type-visible').checked = t.is_visible;
        document.getElementById('type-variation').checked = t.is_variation;
        document.getElementById('type-filterable').checked = t.is_filterable;

        new bootstrap.Modal(document.getElementById('attributeTypeModal')).show();
    } catch (error) {
        alert('Error loading attribute type');
    }
}

async function saveAttributeType() {
    const id = document.getElementById('type-id').value;
    const data = {
        name: document.getElementById('type-name').value,
        code: document.getElementById('type-code').value,
        display_type: document.getElementById('type-display').value,
        sort_order: parseInt(document.getElementById('type-order').value),
        is_visible: document.getElementById('type-visible').checked,
        is_variation: document.getElementById('type-variation').checked,
        is_filterable: document.getElementById('type-filterable').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/attribute-types/${id}` : `${API_BASE}/admin/attribute-types`;
        const response = await fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('attributeTypeModal')).hide();
            loadAttributeTypes();
            loadStats();
        } else {
            const err = await response.json();
            alert(err.message || 'Error saving attribute type');
        }
    } catch (error) {
        alert('Error saving attribute type');
    }
}

async function deleteAttributeType(id) {
    if (!confirm('Delete this attribute type and all its values?')) return;
    await fetch(`${API_BASE}/admin/attribute-types/${id}`, { method: 'DELETE' });
    loadAttributeTypes();
    loadStats();
}

// Attribute Value CRUD
function showAttributeValueModal() {
    document.getElementById('attribute-value-form').reset();
    document.getElementById('value-id').value = '';
    new bootstrap.Modal(document.getElementById('attributeValueModal')).show();
}

function editAttributeValue(id, typeId, value, label, swatch, order, active) {
    document.getElementById('value-id').value = id;
    document.getElementById('value-type').value = typeId;
    document.getElementById('value-value').value = value;
    document.getElementById('value-label').value = label;
    document.getElementById('value-swatch').value = swatch;
    document.getElementById('value-order').value = order;
    document.getElementById('value-active').checked = active;
    if (swatch && swatch.startsWith('#')) {
        document.getElementById('value-color-picker').value = swatch;
    }
    new bootstrap.Modal(document.getElementById('attributeValueModal')).show();
}

async function saveAttributeValue() {
    const id = document.getElementById('value-id').value;
    const typeId = document.getElementById('value-type').value;
    const data = {
        value: document.getElementById('value-value').value,
        label: document.getElementById('value-label').value || null,
        swatch_value: document.getElementById('value-swatch').value || null,
        sort_order: parseInt(document.getElementById('value-order').value),
        is_active: document.getElementById('value-active').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/attribute-values/${id}` : `${API_BASE}/admin/attribute-types/${typeId}/values`;
        const response = await fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('attributeValueModal')).hide();
            loadAttributeTypes();
        } else {
            const err = await response.json();
            alert(err.message || 'Error saving attribute value');
        }
    } catch (error) {
        alert('Error saving attribute value');
    }
}

async function deleteAttributeValue(id) {
    if (!confirm('Delete this attribute value?')) return;
    await fetch(`${API_BASE}/admin/attribute-values/${id}`, { method: 'DELETE' });
    loadAttributeTypes();
}
</script>
@endsection
