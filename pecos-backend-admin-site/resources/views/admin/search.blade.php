@extends('layouts.admin')

@section('title', 'Advanced Search')
@section('page-title', 'Advanced Search Configuration')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Searches Today</h6>
                    <h2 id="searches-today">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="card-title">Zero Results</h6>
                    <h2 id="zero-results">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Active Facets</h6>
                    <h2 id="active-facets">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title">Synonyms</h6>
                    <h2 id="synonyms-count">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="searchTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="facets-tab" data-bs-toggle="tab" href="#facets" role="tab">Facets</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="synonyms-tab" data-bs-toggle="tab" href="#synonyms" role="tab">Synonyms</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="redirects-tab" data-bs-toggle="tab" href="#redirects" role="tab">Redirects</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="boosts-tab" data-bs-toggle="tab" href="#boosts" role="tab">Boosts</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="analytics-tab" data-bs-toggle="tab" href="#analytics" role="tab">Analytics</a>
        </li>
    </ul>

    <div class="tab-content" id="searchTabContent">
        <!-- Facets Tab -->
        <div class="tab-pane fade show active" id="facets" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Search Facets</h5>
                    <button class="btn btn-primary btn-sm" onclick="showFacetModal()">
                        <i class="fas fa-plus"></i> New Facet
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="facets-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
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

        <!-- Synonyms Tab -->
        <div class="tab-pane fade" id="synonyms" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Search Synonyms</h5>
                    <button class="btn btn-primary btn-sm" onclick="showSynonymModal()">
                        <i class="fas fa-plus"></i> New Synonym
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="synonyms-table">
                            <thead>
                                <tr>
                                    <th>Term</th>
                                    <th>Synonyms</th>
                                    <th>Bidirectional</th>
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

        <!-- Redirects Tab -->
        <div class="tab-pane fade" id="redirects" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Search Redirects</h5>
                    <button class="btn btn-primary btn-sm" onclick="showRedirectModal()">
                        <i class="fas fa-plus"></i> New Redirect
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="redirects-table">
                            <thead>
                                <tr>
                                    <th>Search Term</th>
                                    <th>Redirect URL</th>
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

        <!-- Boosts Tab -->
        <div class="tab-pane fade" id="boosts" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Search Boosts</h5>
                    <button class="btn btn-primary btn-sm" onclick="showBoostModal()">
                        <i class="fas fa-plus"></i> New Boost
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="boosts-table">
                            <thead>
                                <tr>
                                    <th>Search Term</th>
                                    <th>Product</th>
                                    <th>Boost Value</th>
                                    <th>Schedule</th>
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

        <!-- Analytics Tab -->
        <div class="tab-pane fade" id="analytics" role="tabpanel">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header"><h5 class="mb-0">Top Searches</h5></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush" id="top-searches"></ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header"><h5 class="mb-0">Top Zero-Result Queries</h5></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush" id="zero-result-queries"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Facet Modal -->
<div class="modal fade" id="facetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Facet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="facet-form">
                    <input type="hidden" id="facet-id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="facet-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="facet-code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="facet-type" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attribute Name</label>
                        <input type="text" class="form-control" id="facet-attribute">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="facet-order" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Options</label>
                            <input type="number" class="form-control" id="facet-max" value="10">
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="facet-active" checked>
                        <label class="form-check-label" for="facet-active">Active</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="facet-collapsed">
                        <label class="form-check-label" for="facet-collapsed">Collapsed by default</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="facet-count" checked>
                        <label class="form-check-label" for="facet-count">Show result counts</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveFacet()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Synonym Modal -->
<div class="modal fade" id="synonymModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Synonym</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="synonym-form">
                    <input type="hidden" id="synonym-id">
                    <div class="mb-3">
                        <label class="form-label">Term</label>
                        <input type="text" class="form-control" id="synonym-term" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Synonyms (comma-separated)</label>
                        <textarea class="form-control" id="synonym-values" rows="3" required></textarea>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="synonym-bidirectional" checked>
                        <label class="form-check-label" for="synonym-bidirectional">Bidirectional</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="synonym-active" checked>
                        <label class="form-check-label" for="synonym-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSynonym()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Redirect Modal -->
<div class="modal fade" id="redirectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Redirect</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="redirect-form">
                    <input type="hidden" id="redirect-id">
                    <div class="mb-3">
                        <label class="form-label">Search Term</label>
                        <input type="text" class="form-control" id="redirect-term" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Redirect URL</label>
                        <input type="text" class="form-control" id="redirect-url" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="redirect-active" checked>
                        <label class="form-check-label" for="redirect-active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveRedirect()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url") }}/api/v1';
let facetTypes = {};

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadFacetTypes();
    loadFacets();
    loadSynonyms();
    loadRedirects();
    loadBoosts();
});

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/admin/search/stats`);
        const data = await response.json();
        const stats = data.data;

        document.getElementById('searches-today').textContent = stats.searches?.today || 0;
        document.getElementById('zero-results').textContent = stats.searches?.zero_results_today || 0;
        document.getElementById('active-facets').textContent = stats.facets?.active || 0;
        document.getElementById('synonyms-count').textContent = stats.synonyms?.active || 0;

        const topSearches = document.getElementById('top-searches');
        topSearches.innerHTML = '';
        (stats.top_searches || []).forEach((query, i) => {
            topSearches.innerHTML += `<li class="list-group-item d-flex justify-content-between">${query}<span class="badge bg-primary">#${i + 1}</span></li>`;
        });

        const zeroResult = document.getElementById('zero-result-queries');
        zeroResult.innerHTML = '';
        (stats.top_zero_result || []).forEach(item => {
            zeroResult.innerHTML += `<li class="list-group-item d-flex justify-content-between">${item.query}<span class="badge bg-warning text-dark">${item.count}</span></li>`;
        });
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadFacetTypes() {
    try {
        const response = await fetch(`${API_BASE}/admin/search/facet-types`);
        const data = await response.json();
        facetTypes = data.data;

        const select = document.getElementById('facet-type');
        select.innerHTML = '';
        Object.entries(facetTypes).forEach(([key, label]) => {
            select.innerHTML += `<option value="${key}">${label}</option>`;
        });
    } catch (error) {
        console.error('Error loading facet types:', error);
    }
}

async function loadFacets() {
    try {
        const response = await fetch(`${API_BASE}/admin/search/facets`);
        const data = await response.json();
        const facets = data.data || [];

        const tbody = document.querySelector('#facets-table tbody');
        tbody.innerHTML = '';

        facets.forEach(facet => {
            tbody.innerHTML += `
                <tr>
                    <td>${facet.sort_order}</td>
                    <td>${facet.name}</td>
                    <td><code>${facet.code}</code></td>
                    <td>${facetTypes[facet.type] || facet.type}</td>
                    <td><span class="badge bg-${facet.is_active ? 'success' : 'secondary'}">${facet.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editFacet(${facet.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFacet(${facet.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading facets:', error);
    }
}

async function loadSynonyms() {
    try {
        const response = await fetch(`${API_BASE}/admin/search/synonyms`);
        const data = await response.json();
        const synonyms = data.data || [];

        const tbody = document.querySelector('#synonyms-table tbody');
        tbody.innerHTML = '';

        synonyms.forEach(syn => {
            tbody.innerHTML += `
                <tr>
                    <td><strong>${syn.term}</strong></td>
                    <td><small>${syn.synonyms}</small></td>
                    <td>${syn.is_bidirectional ? '<i class="fas fa-exchange-alt text-success"></i>' : '<i class="fas fa-arrow-right text-muted"></i>'}</td>
                    <td><span class="badge bg-${syn.is_active ? 'success' : 'secondary'}">${syn.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editSynonym(${syn.id}, '${syn.term}', '${syn.synonyms.replace(/'/g, "\\'")}', ${syn.is_bidirectional}, ${syn.is_active})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSynonym(${syn.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading synonyms:', error);
    }
}

async function loadRedirects() {
    try {
        const response = await fetch(`${API_BASE}/admin/search/redirects`);
        const data = await response.json();
        const redirects = data.data || [];

        const tbody = document.querySelector('#redirects-table tbody');
        tbody.innerHTML = '';

        redirects.forEach(r => {
            tbody.innerHTML += `
                <tr>
                    <td><strong>${r.search_term}</strong></td>
                    <td><small>${r.redirect_url}</small></td>
                    <td><span class="badge bg-${r.is_active ? 'success' : 'secondary'}">${r.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editRedirect(${r.id}, '${r.search_term}', '${r.redirect_url}', ${r.is_active})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRedirect(${r.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading redirects:', error);
    }
}

async function loadBoosts() {
    try {
        const response = await fetch(`${API_BASE}/admin/search/boosts`);
        const data = await response.json();
        const boosts = data.data || [];

        const tbody = document.querySelector('#boosts-table tbody');
        tbody.innerHTML = '';

        boosts.forEach(b => {
            tbody.innerHTML += `
                <tr>
                    <td><strong>${b.search_term}</strong></td>
                    <td>${b.product?.name || 'Product #' + b.product_id}</td>
                    <td><span class="badge bg-info">${b.boost_value}</span></td>
                    <td>${b.starts_at ? new Date(b.starts_at).toLocaleDateString() : '-'} - ${b.expires_at ? new Date(b.expires_at).toLocaleDateString() : '-'}</td>
                    <td><span class="badge bg-${b.is_active ? 'success' : 'secondary'}">${b.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBoost(${b.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error loading boosts:', error);
    }
}

// Facet CRUD
function showFacetModal() {
    document.getElementById('facet-form').reset();
    document.getElementById('facet-id').value = '';
    new bootstrap.Modal(document.getElementById('facetModal')).show();
}

async function editFacet(id) {
    try {
        const response = await fetch(`${API_BASE}/admin/search/facets/${id}`);
        const data = await response.json();
        const f = data.data;

        document.getElementById('facet-id').value = f.id;
        document.getElementById('facet-name').value = f.name;
        document.getElementById('facet-code').value = f.code;
        document.getElementById('facet-type').value = f.type;
        document.getElementById('facet-attribute').value = f.attribute_name || '';
        document.getElementById('facet-order').value = f.sort_order;
        document.getElementById('facet-max').value = f.max_options;
        document.getElementById('facet-active').checked = f.is_active;
        document.getElementById('facet-collapsed').checked = f.is_collapsed;
        document.getElementById('facet-count').checked = f.show_count;

        new bootstrap.Modal(document.getElementById('facetModal')).show();
    } catch (error) {
        alert('Error loading facet');
    }
}

async function saveFacet() {
    const id = document.getElementById('facet-id').value;
    const data = {
        name: document.getElementById('facet-name').value,
        code: document.getElementById('facet-code').value,
        type: document.getElementById('facet-type').value,
        attribute_name: document.getElementById('facet-attribute').value || null,
        sort_order: parseInt(document.getElementById('facet-order').value),
        max_options: parseInt(document.getElementById('facet-max').value),
        is_active: document.getElementById('facet-active').checked,
        is_collapsed: document.getElementById('facet-collapsed').checked,
        show_count: document.getElementById('facet-count').checked
    };

    try {
        const url = id ? `${API_BASE}/admin/search/facets/${id}` : `${API_BASE}/admin/search/facets`;
        const response = await fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('facetModal')).hide();
            loadFacets();
            loadStats();
        }
    } catch (error) {
        alert('Error saving facet');
    }
}

async function deleteFacet(id) {
    if (!confirm('Delete this facet?')) return;
    await fetch(`${API_BASE}/admin/search/facets/${id}`, { method: 'DELETE' });
    loadFacets();
    loadStats();
}

// Synonym CRUD
function showSynonymModal() {
    document.getElementById('synonym-form').reset();
    document.getElementById('synonym-id').value = '';
    new bootstrap.Modal(document.getElementById('synonymModal')).show();
}

function editSynonym(id, term, synonyms, bidirectional, active) {
    document.getElementById('synonym-id').value = id;
    document.getElementById('synonym-term').value = term;
    document.getElementById('synonym-values').value = synonyms;
    document.getElementById('synonym-bidirectional').checked = bidirectional;
    document.getElementById('synonym-active').checked = active;
    new bootstrap.Modal(document.getElementById('synonymModal')).show();
}

async function saveSynonym() {
    const id = document.getElementById('synonym-id').value;
    const data = {
        term: document.getElementById('synonym-term').value,
        synonyms: document.getElementById('synonym-values').value,
        is_bidirectional: document.getElementById('synonym-bidirectional').checked,
        is_active: document.getElementById('synonym-active').checked
    };

    const url = id ? `${API_BASE}/admin/search/synonyms/${id}` : `${API_BASE}/admin/search/synonyms`;
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
    bootstrap.Modal.getInstance(document.getElementById('synonymModal')).hide();
    loadSynonyms();
    loadStats();
}

async function deleteSynonym(id) {
    if (!confirm('Delete this synonym?')) return;
    await fetch(`${API_BASE}/admin/search/synonyms/${id}`, { method: 'DELETE' });
    loadSynonyms();
    loadStats();
}

// Redirect CRUD
function showRedirectModal() {
    document.getElementById('redirect-form').reset();
    document.getElementById('redirect-id').value = '';
    new bootstrap.Modal(document.getElementById('redirectModal')).show();
}

function editRedirect(id, term, url, active) {
    document.getElementById('redirect-id').value = id;
    document.getElementById('redirect-term').value = term;
    document.getElementById('redirect-url').value = url;
    document.getElementById('redirect-active').checked = active;
    new bootstrap.Modal(document.getElementById('redirectModal')).show();
}

async function saveRedirect() {
    const id = document.getElementById('redirect-id').value;
    const data = {
        search_term: document.getElementById('redirect-term').value,
        redirect_url: document.getElementById('redirect-url').value,
        is_active: document.getElementById('redirect-active').checked
    };

    const url = id ? `${API_BASE}/admin/search/redirects/${id}` : `${API_BASE}/admin/search/redirects`;
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
    bootstrap.Modal.getInstance(document.getElementById('redirectModal')).hide();
    loadRedirects();
}

async function deleteRedirect(id) {
    if (!confirm('Delete this redirect?')) return;
    await fetch(`${API_BASE}/admin/search/redirects/${id}`, { method: 'DELETE' });
    loadRedirects();
}

async function deleteBoost(id) {
    if (!confirm('Delete this boost?')) return;
    await fetch(`${API_BASE}/admin/search/boosts/${id}`, { method: 'DELETE' });
    loadBoosts();
}
</script>
@endsection
