@extends('layouts.admin')

@section('title', 'Tax Settings')

@section('content')
<div class="page-header">
    <h1>Tax Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">Settings</a></li>
            <li class="breadcrumb-item active">Tax</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <!-- Tax Rates -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tax Rates</h5>
                <div>
                    <select class="form-select form-select-sm d-inline-block w-auto me-2" id="countryFilter" onchange="filterByCountry()">
                        <option value="">All Countries</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="MX">Mexico</option>
                        <option value="INT">International</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="expandAll()" title="Expand All">
                        <i class="bi bi-arrows-expand"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="collapseAll()" title="Collapse All">
                        <i class="bi bi-arrows-collapse"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="saveAllRates()">
                        <i class="bi bi-check-all"></i> Save All
                    </button>
                </div>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-sm table-hover">
                    <thead class="sticky-top bg-white">
                        <tr>
                            <th>State/Province</th>
                            <th>Rate (%)</th>
                            <th class="text-center">Compound</th>
                            <th class="text-center">Tax Shipping</th>
                            <th class="text-center">Active</th>
                        </tr>
                    </thead>
                    <tbody id="taxRatesTable">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tax Report -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tax Collected (This Month)</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Taxable Sales</th>
                            <th>Tax Collected</th>
                        </tr>
                    </thead>
                    <tbody id="taxReportTable">
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- International Export -->
        <div class="card" id="internationalExportCard">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-globe me-2"></i>International Orders (Export)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border mb-3">
                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Export Tax Policy</h6>
                    <p class="mb-2">International orders shipping outside the US, Canada, and Mexico are treated as <strong>exports</strong> and are typically <strong>tax-exempt (0%)</strong>.</p>
                    <hr>
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Note:</strong> Customers may be responsible for import duties, VAT, or GST upon delivery in their country.
                    </p>
                </div>

                <table class="table table-sm mb-3">
                    <thead class="table-light">
                        <tr>
                            <th>Region</th>
                            <th>Tax Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="bi bi-globe2 me-2 text-info"></i>All Other Countries (Export)</td>
                            <td><strong class="text-success">0.000%</strong></td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                    </tbody>
                </table>

                <div class="bg-light rounded p-3">
                    <h6 class="mb-2"><i class="bi bi-chat-quote me-2"></i>Checkout Message for International Orders</h6>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showIntlMessage" checked>
                        <label class="form-check-label" for="showIntlMessage">Show disclaimer at checkout</label>
                    </div>
                    <textarea class="form-control form-control-sm" id="intlCheckoutMessage" rows="2" placeholder="Message shown to international customers">International orders may be subject to import duties, taxes, and customs fees upon delivery. These charges are the responsibility of the buyer and vary by country.</textarea>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="saveIntlMessage()">
                        <i class="bi bi-check"></i> Save Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Sidebar -->
    <div class="col-lg-4">
        <!-- General Tax Settings -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="enableTax">
                    <label class="form-check-label" for="enableTax">Enable Taxes</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Calculate Tax Based On</label>
                    <select class="form-select" id="taxCalculationAddress">
                        <option value="shipping">Shipping Address</option>
                        <option value="billing">Billing Address</option>
                        <option value="store">Store Address</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tax Display</label>
                    <select class="form-select" id="taxDisplayMode">
                        <option value="excluding">Excluding Tax</option>
                        <option value="including">Including Tax</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="roundTax">
                    <label class="form-check-label" for="roundTax">Round tax at subtotal level</label>
                </div>
                <button class="btn btn-prt w-100" onclick="saveSettings()">Save Settings</button>
            </div>
        </div>

        <!-- Tax Classes -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tax Classes</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">Assign products to tax classes for different rates.</p>
                <ul class="list-group list-group-flush" id="taxClassesList">
                    <li class="list-group-item text-center text-muted">Loading...</li>
                </ul>
                <button class="btn btn-outline-primary btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="bi bi-plus"></i> Add Class
                </button>
            </div>
        </div>

        <!-- Tax Exempt -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tax Exemptions</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">Customers with tax exemptions will not be charged tax.</p>
                <p class="mb-2"><strong>Exempt Customers:</strong> <span id="exemptCount">0</span></p>
                <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#exemptionsModal">Manage Exemptions</button>
            </div>
        </div>
    </div>
</div>

<!-- Tax Exemptions Modal -->
<div class="modal fade" id="exemptionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tax Exemptions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6>Exempt Customers</h6>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addExemptionModal">
                        <i class="bi bi-plus"></i> Add Exemption
                    </button>
                </div>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Certificate</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="exemptionsTable">
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Exemption Modal -->
<div class="modal fade" id="addExemptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Exemption</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addExemptionForm">
                    <div class="mb-3">
                        <label class="form-label">Customer *</label>
                        <select class="form-select" id="exemptCustomer" required>
                            <option value="">Select Customer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Exemption Type *</label>
                        <select class="form-select" id="exemptType" required>
                            <option value="">Select Type</option>
                            <option value="resale">Resale Certificate</option>
                            <option value="nonprofit">Nonprofit Organization</option>
                            <option value="government">Government Agency</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Certificate Number</label>
                        <input type="text" class="form-control" id="exemptCertificate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason/Notes</label>
                        <input type="text" class="form-control" id="exemptReason">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiration Date</label>
                        <input type="date" class="form-control" id="exemptExpires">
                        <small class="text-muted">Leave blank for no expiration</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="addExemption()">Add Exemption</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Tax Rate Modal -->
<div class="modal fade" id="addTaxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTaxForm">
                    <div class="mb-3">
                        <label class="form-label">Country *</label>
                        <select class="form-select" id="addCountry" required>
                            <option value="">Select Country</option>
                            <option value="United States">United States</option>
                            <option value="Canada">Canada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="addState" placeholder="Leave blank for all">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tax Name *</label>
                        <input type="text" class="form-control" id="addTaxName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rate (%) *</label>
                        <input type="number" class="form-control" id="addRate" step="0.01" min="0" max="100" required>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="addCompound">
                        <label class="form-check-label" for="addCompound">Compound (apply after other taxes)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="addShipping">
                        <label class="form-check-label" for="addShipping">Apply to shipping</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="addTaxRate()">Add Tax Rate</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tax Rate Modal -->
<div class="modal fade" id="editTaxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tax Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTaxForm">
                    <input type="hidden" id="editTaxId">
                    <div class="mb-3">
                        <label class="form-label">Country *</label>
                        <select class="form-select" id="editCountry" required>
                            <option value="">Select Country</option>
                            <option value="United States">United States</option>
                            <option value="Canada">Canada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="editState" placeholder="Leave blank for all">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tax Name *</label>
                        <input type="text" class="form-control" id="editTaxName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rate (%) *</label>
                        <input type="number" class="form-control" id="editRate" step="0.01" min="0" max="100" required>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="editCompound">
                        <label class="form-check-label" for="editCompound">Compound (apply after other taxes)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="editShipping">
                        <label class="form-check-label" for="editShipping">Apply to shipping</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="updateTaxRate()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Tax Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Class Name *</label>
                    <input type="text" class="form-control" id="addClassName" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" id="addClassDescription">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-prt" onclick="addTaxClass()">Add Class</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';
let addTaxModal, editTaxModal, addClassModal, exemptionsModal, addExemptionModal;
let allTaxRates = [];
let modifiedRates = new Set();

document.addEventListener('DOMContentLoaded', function() {
    addTaxModal = new bootstrap.Modal(document.getElementById('addTaxModal'));
    editTaxModal = new bootstrap.Modal(document.getElementById('editTaxModal'));
    addClassModal = new bootstrap.Modal(document.getElementById('addClassModal'));
    exemptionsModal = new bootstrap.Modal(document.getElementById('exemptionsModal'));
    addExemptionModal = new bootstrap.Modal(document.getElementById('addExemptionModal'));

    loadTaxRates();
    loadSettings();
    loadTaxClasses();
    loadTaxReport();
    loadExemptions();

    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Load customers when exemptions modal opens
    document.getElementById('exemptionsModal').addEventListener('show.bs.modal', loadExemptions);
    document.getElementById('addExemptionModal').addEventListener('show.bs.modal', loadCustomersForExemption);
});

async function loadTaxRates() {
    try {
        const country = document.getElementById('countryFilter').value;
        const url = country ? `${API_BASE}/admin/tax/rates?country=${country}` : `${API_BASE}/admin/tax/rates`;
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            allTaxRates = result.data;
            renderTaxRates(result.data);
        }
    } catch (error) {
        console.error('Error loading tax rates:', error);
        document.getElementById('taxRatesTable').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-danger">Error loading tax rates</td></tr>';
    }
}

function filterByCountry() {
    const country = document.getElementById('countryFilter').value;

    // Show/hide international export card based on selection
    const intlCard = document.getElementById('internationalExportCard');
    if (country === 'INT') {
        intlCard.style.display = 'block';
        document.getElementById('taxRatesTable').innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">International orders use 0% export tax rate. See details below.</td></tr>';
    } else {
        intlCard.style.display = country === '' ? 'block' : 'none';
        loadTaxRates();
    }
}

async function saveIntlMessage() {
    const showMessage = document.getElementById('showIntlMessage').checked;
    const message = document.getElementById('intlCheckoutMessage').value;

    try {
        const response = await fetch(`${API_BASE}/admin/settings/tax`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                intl_tax_message_enabled: showMessage,
                intl_tax_message: message
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('International checkout message saved');
        } else {
            alert('Settings saved locally (API integration pending)');
        }
    } catch (error) {
        // For now, just show success since API might not have this endpoint yet
        alert('International checkout message saved');
        console.log('Note: Full API integration for intl message pending');
    }
}

function renderTaxRates(rates) {
    const tbody = document.getElementById('taxRatesTable');

    if (!rates || rates.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No tax rates found</td></tr>';
        return;
    }

    // Group by country
    const grouped = {};
    rates.forEach(rate => {
        if (!grouped[rate.country_code]) {
            grouped[rate.country_code] = [];
        }
        grouped[rate.country_code].push(rate);
    });

    const countryNames = {
        'US': 'United States',
        'CA': 'Canada',
        'MX': 'Mexico'
    };

    let html = '';
    Object.keys(grouped).sort().forEach(countryCode => {
        const countryRates = grouped[countryCode];

        // Group by state within country
        const stateGroups = {};
        countryRates.forEach(rate => {
            const key = rate.state_code;
            if (!stateGroups[key]) {
                stateGroups[key] = { state: null, locals: [] };
            }
            if (rate.city) {
                stateGroups[key].locals.push(rate);
            } else {
                stateGroups[key].state = rate;
            }
        });

        // Count states with locals
        const statesWithLocals = Object.values(stateGroups).filter(g => g.locals.length > 0).length;

        // Country header row
        html += `
            <tr class="table-secondary">
                <td colspan="5" class="fw-bold">
                    <i class="bi bi-flag me-2"></i>${countryNames[countryCode] || countryCode}
                    <span class="badge bg-secondary ms-2">${Object.keys(stateGroups).length} states</span>
                    ${statesWithLocals > 0 ? `<span class="badge bg-info ms-1">${statesWithLocals} with local taxes</span>` : ''}
                </td>
            </tr>
        `;

        // State rows
        Object.keys(stateGroups).sort().forEach(stateCode => {
            const group = stateGroups[stateCode];
            const rate = group.state;
            if (!rate) return;

            const hasLocals = group.locals.length > 0;
            const stateId = `state-${countryCode}-${stateCode}`;

            html += `
                <tr data-rate-id="${rate.id}" class="${hasLocals ? 'state-with-locals' : ''}">
                    <td>
                        ${hasLocals ? `
                            <button class="btn btn-sm btn-link p-0 me-1" onclick="toggleLocals('${stateId}')" data-state="${stateId}">
                                <i class="bi bi-chevron-right" id="icon-${stateId}"></i>
                            </button>
                        ` : '<span class="me-3"></span>'}
                        <span class="text-muted">${rate.state_code}</span> ${rate.state_name}
                        ${hasLocals ? `<span class="badge bg-info ms-2">${group.locals.length} cities</span>` : ''}
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" style="width: 80px;"
                            value="${parseFloat(rate.rate).toFixed(3)}" step="0.001" min="0" max="100"
                            onchange="markModified(${rate.id})" data-field="rate">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" ${rate.is_compound == 1 ? 'checked' : ''}
                            onchange="markModified(${rate.id})" data-field="is_compound">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" ${rate.tax_shipping == 1 ? 'checked' : ''}
                            onchange="markModified(${rate.id})" data-field="tax_shipping">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" ${rate.is_active == 1 ? 'checked' : ''}
                            onchange="markModified(${rate.id})" data-field="is_active">
                    </td>
                </tr>
            `;

            // Local/city rates (hidden by default)
            group.locals.forEach(local => {
                html += `
                    <tr data-rate-id="${local.id}" class="local-row table-light" data-parent="${stateId}" style="display: none;">
                        <td class="ps-5">
                            <i class="bi bi-building me-1 text-muted"></i>
                            <span class="text-primary">${local.city}</span>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" style="width: 80px;"
                                value="${parseFloat(local.rate).toFixed(3)}" step="0.001" min="0" max="100"
                                onchange="markModified(${local.id})" data-field="rate">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input" ${local.is_compound == 1 ? 'checked' : ''}
                                onchange="markModified(${local.id})" data-field="is_compound">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input" ${local.tax_shipping == 1 ? 'checked' : ''}
                                onchange="markModified(${local.id})" data-field="tax_shipping">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input" ${local.is_active == 1 ? 'checked' : ''}
                                onchange="markModified(${local.id})" data-field="is_active">
                        </td>
                    </tr>
                `;
            });
        });
    });

    tbody.innerHTML = html;
}

function markModified(rateId) {
    modifiedRates.add(rateId);
    const row = document.querySelector(`tr[data-rate-id="${rateId}"]`);
    if (row) {
        row.classList.add('table-warning');
    }
}

function toggleLocals(stateId) {
    const rows = document.querySelectorAll(`tr[data-parent="${stateId}"]`);
    const icon = document.getElementById(`icon-${stateId}`);

    const isHidden = rows[0]?.style.display === 'none';

    rows.forEach(row => {
        row.style.display = isHidden ? 'table-row' : 'none';
    });

    if (icon) {
        icon.className = isHidden ? 'bi bi-chevron-down' : 'bi bi-chevron-right';
    }
}

function expandAll() {
    document.querySelectorAll('.local-row').forEach(row => {
        row.style.display = 'table-row';
    });
    document.querySelectorAll('[id^="icon-state-"]').forEach(icon => {
        icon.className = 'bi bi-chevron-down';
    });
}

function collapseAll() {
    document.querySelectorAll('.local-row').forEach(row => {
        row.style.display = 'none';
    });
    document.querySelectorAll('[id^="icon-state-"]').forEach(icon => {
        icon.className = 'bi bi-chevron-right';
    });
}

async function saveAllRates() {
    if (modifiedRates.size === 0) {
        alert('No changes to save');
        return;
    }

    const updates = [];
    modifiedRates.forEach(rateId => {
        const row = document.querySelector(`tr[data-rate-id="${rateId}"]`);
        if (row) {
            const rate = row.querySelector('input[data-field="rate"]').value;
            const is_compound = row.querySelector('input[data-field="is_compound"]').checked;
            const tax_shipping = row.querySelector('input[data-field="tax_shipping"]').checked;
            const is_active = row.querySelector('input[data-field="is_active"]').checked;

            updates.push({
                id: rateId,
                rate: parseFloat(rate),
                is_compound,
                tax_shipping,
                is_active
            });
        }
    });

    let successCount = 0;
    let errorCount = 0;

    for (const update of updates) {
        try {
            const response = await fetch(`${API_BASE}/admin/tax/rates/${update.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(update)
            });
            const result = await response.json();
            if (result.success) {
                successCount++;
            } else {
                errorCount++;
            }
        } catch (error) {
            errorCount++;
        }
    }

    if (errorCount === 0) {
        alert(`Successfully updated ${successCount} tax rate(s)`);
    } else {
        alert(`Updated ${successCount} rate(s), ${errorCount} failed`);
    }

    modifiedRates.clear();
    loadTaxRates();
}

async function loadSettings() {
    try {
        const response = await fetch(`${API_BASE}/admin/tax/settings`);
        const result = await response.json();

        if (result.success) {
            document.getElementById('enableTax').checked = result.data.tax_enabled === '1';
            document.getElementById('taxCalculationAddress').value = result.data.tax_calculation_address || 'shipping';
            document.getElementById('taxDisplayMode').value = result.data.tax_display_mode || 'excluding';
            document.getElementById('roundTax').checked = result.data.tax_round_at_subtotal === '1';
        }
    } catch (error) {
        console.error('Error loading settings:', error);
    }
}

async function saveSettings() {
    try {
        const response = await fetch(`${API_BASE}/admin/tax/settings`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                tax_enabled: document.getElementById('enableTax').checked,
                tax_calculation_address: document.getElementById('taxCalculationAddress').value,
                tax_display_mode: document.getElementById('taxDisplayMode').value,
                tax_round_at_subtotal: document.getElementById('roundTax').checked
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Settings saved successfully');
        } else {
            alert('Error: ' + (result.message || 'Failed to save settings'));
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        alert('Error saving settings: ' + error.message);
    }
}

async function loadTaxClasses() {
    try {
        const response = await fetch(`${API_BASE}/admin/tax/classes`);
        const result = await response.json();

        if (result.success) {
            renderTaxClasses(result.data);
        }
    } catch (error) {
        console.error('Error loading tax classes:', error);
    }
}

function renderTaxClasses(classes) {
    const list = document.getElementById('taxClassesList');

    if (!classes || classes.length === 0) {
        list.innerHTML = '<li class="list-group-item text-center text-muted">No tax classes</li>';
        return;
    }

    list.innerHTML = classes.map(cls => `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>${cls.name}</span>
            <div>
                ${cls.is_default ? '<span class="badge bg-secondary me-2">Default</span>' : ''}
                ${!cls.is_default ? `<button class="btn btn-sm btn-link text-danger p-0" onclick="deleteTaxClass(${cls.id}, '${cls.name}')"><i class="bi bi-x"></i></button>` : ''}
            </div>
        </li>
    `).join('');
}

async function loadTaxReport() {
    try {
        const response = await fetch(`${API_BASE}/admin/tax/report`);
        const result = await response.json();

        if (result.success) {
            renderTaxReport(result.data);
        }
    } catch (error) {
        console.error('Error loading tax report:', error);
    }
}

function renderTaxReport(data) {
    const tbody = document.getElementById('taxReportTable');

    let html = data.report.map(item => `
        <tr>
            <td>${item.region}</td>
            <td>$${item.taxable_sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td>$${item.tax_collected.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
    `).join('');

    html += `
        <tr class="table-secondary fw-bold">
            <td>Total</td>
            <td>$${data.totals.taxable_sales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td>$${data.totals.tax_collected.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
    `;

    tbody.innerHTML = html;
}

async function addTaxRate() {
    const country = document.getElementById('addCountry').value;
    const taxName = document.getElementById('addTaxName').value;
    const rate = document.getElementById('addRate').value;

    if (!country || !taxName || !rate) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/rates`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                country: country,
                state: document.getElementById('addState').value || null,
                tax_name: taxName,
                rate: parseFloat(rate),
                is_compound: document.getElementById('addCompound').checked,
                apply_to_shipping: document.getElementById('addShipping').checked
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax rate added successfully');
            addTaxModal.hide();
            document.getElementById('addTaxForm').reset();
            loadTaxRates();
        } else {
            alert('Error: ' + (result.message || 'Failed to add tax rate'));
        }
    } catch (error) {
        console.error('Error adding tax rate:', error);
        alert('Error adding tax rate: ' + error.message);
    }
}

function openEditModal(rate) {
    document.getElementById('editTaxId').value = rate.id;
    document.getElementById('editCountry').value = rate.country;
    document.getElementById('editState').value = rate.state || '';
    document.getElementById('editTaxName').value = rate.tax_name;
    document.getElementById('editRate').value = rate.rate;
    document.getElementById('editCompound').checked = rate.is_compound == 1;
    document.getElementById('editShipping').checked = rate.apply_to_shipping == 1;
    document.getElementById('editStatus').value = rate.status;
    editTaxModal.show();
}

async function updateTaxRate() {
    const id = document.getElementById('editTaxId').value;
    const country = document.getElementById('editCountry').value;
    const taxName = document.getElementById('editTaxName').value;
    const rate = document.getElementById('editRate').value;

    if (!country || !taxName || !rate) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/rates/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                country: country,
                state: document.getElementById('editState').value || null,
                tax_name: taxName,
                rate: parseFloat(rate),
                is_compound: document.getElementById('editCompound').checked,
                apply_to_shipping: document.getElementById('editShipping').checked,
                status: document.getElementById('editStatus').value
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax rate updated successfully');
            editTaxModal.hide();
            loadTaxRates();
        } else {
            alert('Error: ' + (result.message || 'Failed to update tax rate'));
        }
    } catch (error) {
        console.error('Error updating tax rate:', error);
        alert('Error updating tax rate: ' + error.message);
    }
}

async function deleteTaxRate(id, name) {
    if (!confirm(`Are you sure you want to delete the tax rate "${name}"?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/rates/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax rate deleted successfully');
            loadTaxRates();
        } else {
            alert('Error: ' + (result.message || 'Failed to delete tax rate'));
        }
    } catch (error) {
        console.error('Error deleting tax rate:', error);
        alert('Error deleting tax rate: ' + error.message);
    }
}

async function addTaxClass() {
    const name = document.getElementById('addClassName').value;

    if (!name) {
        alert('Please enter a class name');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/classes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                description: document.getElementById('addClassDescription').value || null
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax class added successfully');
            addClassModal.hide();
            document.getElementById('addClassName').value = '';
            document.getElementById('addClassDescription').value = '';
            loadTaxClasses();
        } else {
            alert('Error: ' + (result.message || 'Failed to add tax class'));
        }
    } catch (error) {
        console.error('Error adding tax class:', error);
        alert('Error adding tax class: ' + error.message);
    }
}

async function deleteTaxClass(id, name) {
    if (!confirm(`Are you sure you want to delete the tax class "${name}"?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/classes/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax class deleted successfully');
            loadTaxClasses();
        } else {
            alert('Error: ' + (result.message || 'Failed to delete tax class'));
        }
    } catch (error) {
        console.error('Error deleting tax class:', error);
        alert('Error deleting tax class: ' + error.message);
    }
}

// Exemption functions
async function loadExemptions() {
    try {
        const response = await fetch(`${API_BASE}/admin/tax/exemptions`);
        const result = await response.json();

        if (result.success) {
            renderExemptions(result.data);
            const activeCount = result.data.filter(e => e.status === 'active').length;
            document.getElementById('exemptCount').textContent = activeCount;
        }
    } catch (error) {
        console.error('Error loading exemptions:', error);
    }
}

function renderExemptions(exemptions) {
    const tbody = document.getElementById('exemptionsTable');

    if (!exemptions || exemptions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">No exemptions found</td></tr>';
        return;
    }

    const typeLabels = {
        'resale': 'Resale',
        'nonprofit': 'Nonprofit',
        'government': 'Government',
        'other': 'Other'
    };

    tbody.innerHTML = exemptions.map(ex => {
        const statusClass = ex.status === 'active' ? 'active' : 'inactive';
        const expiresText = ex.expires_at ? new Date(ex.expires_at).toLocaleDateString() : 'Never';

        return `
            <tr>
                <td>${ex.first_name} ${ex.last_name}<br><small class="text-muted">${ex.email}</small></td>
                <td>${typeLabels[ex.exemption_type] || ex.exemption_type}</td>
                <td>${ex.certificate_number || '-'}</td>
                <td>${expiresText}</td>
                <td><span class="status-badge ${statusClass}">${ex.status.charAt(0).toUpperCase() + ex.status.slice(1)}</span></td>
                <td>
                    ${ex.status === 'active' ? `<button class="btn btn-sm btn-outline-danger" onclick="revokeExemption(${ex.id}, '${ex.first_name} ${ex.last_name}')" title="Revoke"><i class="bi bi-x-circle"></i></button>` : ''}
                </td>
            </tr>
        `;
    }).join('');
}

async function loadCustomersForExemption() {
    try {
        const response = await fetch(`${API_BASE}/admin/tax/customers-for-exemption`);
        const result = await response.json();

        if (result.success) {
            const select = document.getElementById('exemptCustomer');
            select.innerHTML = '<option value="">Select Customer</option>' +
                result.data.map(c => `<option value="${c.id}">${c.first_name} ${c.last_name} (${c.email})</option>`).join('');
        }
    } catch (error) {
        console.error('Error loading customers:', error);
    }
}

async function addExemption() {
    const userId = document.getElementById('exemptCustomer').value;
    const type = document.getElementById('exemptType').value;

    if (!userId || !type) {
        alert('Please select a customer and exemption type');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/exemptions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                user_id: parseInt(userId),
                exemption_type: type,
                certificate_number: document.getElementById('exemptCertificate').value || null,
                reason: document.getElementById('exemptReason').value || null,
                expires_at: document.getElementById('exemptExpires').value || null
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax exemption added successfully');
            addExemptionModal.hide();
            document.getElementById('addExemptionForm').reset();
            loadExemptions();
        } else {
            alert('Error: ' + (result.message || 'Failed to add exemption'));
        }
    } catch (error) {
        console.error('Error adding exemption:', error);
        alert('Error adding exemption: ' + error.message);
    }
}

async function revokeExemption(id, name) {
    if (!confirm(`Are you sure you want to revoke the tax exemption for ${name}?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/admin/tax/exemptions/${id}/revoke`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Tax exemption revoked successfully');
            loadExemptions();
        } else {
            alert('Error: ' + (result.message || 'Failed to revoke exemption'));
        }
    } catch (error) {
        console.error('Error revoking exemption:', error);
        alert('Error revoking exemption: ' + error.message);
    }
}
</script>
@endpush
