@extends('layouts.admin')

@section('title', 'Currency Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Currency Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Currencies</li>
                </ol>
            </nav>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="fetchExternalRates()">
                <i class="fas fa-sync-alt"></i> Fetch Rates
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#currencyModal" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Add Currency
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total Currencies</h6>
                            <h2 class="mb-0" id="stat-total">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Active</h6>
                            <h2 class="mb-0" id="stat-active">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Default Currency</h6>
                            <h2 class="mb-0" id="stat-default">-</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Last Rate Update</h6>
                            <h6 class="mb-0" id="stat-last-update">-</h6>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Converter -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Currency Converter</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Amount</label>
                    <input type="number" class="form-control" id="convert-amount" value="100" step="0.01">
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Currency</label>
                    <select class="form-select" id="convert-from"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Currency</label>
                    <select class="form-select" id="convert-to"></select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="convertCurrency()">
                        <i class="fas fa-calculator"></i> Convert
                    </button>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100" onclick="swapCurrencies()" title="Swap">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </div>
            <div id="conversion-result" class="mt-3 d-none">
                <div class="alert alert-info mb-0">
                    <strong id="result-text"></strong>
                    <br><small class="text-muted" id="result-rate"></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Currencies Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Currencies</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="currencies-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Exchange Rate</th>
                            <th>Format Preview</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="currencies-tbody">
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Currency Modal -->
<div class="modal fade" id="currencyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="currencyModalTitle">Add Currency</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="currency-form">
                    <input type="hidden" id="currency-id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Currency Code *</label>
                            <input type="text" class="form-control" id="currency-code" maxlength="3"
                                   placeholder="USD" required style="text-transform: uppercase;">
                            <small class="text-muted">ISO 4217 code (3 letters)</small>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Currency Name *</label>
                            <input type="text" class="form-control" id="currency-name"
                                   placeholder="US Dollar" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Symbol *</label>
                            <input type="text" class="form-control" id="currency-symbol"
                                   maxlength="10" placeholder="$" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Symbol Position</label>
                            <select class="form-select" id="currency-symbol-position">
                                <option value="before">Before amount ($100)</option>
                                <option value="after">After amount (100$)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Decimal Places</label>
                            <select class="form-select" id="currency-decimal-places">
                                <option value="0">0 (¥100)</option>
                                <option value="2" selected>2 ($100.00)</option>
                                <option value="3">3 ($100.000)</option>
                                <option value="4">4 ($100.0000)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Decimal Separator</label>
                            <select class="form-select" id="currency-decimal-separator">
                                <option value=".">Period (.)</option>
                                <option value=",">Comma (,)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thousand Separator</label>
                            <select class="form-select" id="currency-thousand-separator">
                                <option value=",">Comma (,)</option>
                                <option value=".">Period (.)</option>
                                <option value=" ">Space ( )</option>
                                <option value="">None</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="currency-sort-order"
                                   value="0" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exchange Rate (vs Base Currency)</label>
                            <input type="number" class="form-control" id="currency-rate"
                                   step="0.000001" placeholder="1.00">
                            <small class="text-muted">Leave empty to set later</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="currency-active" checked>
                                <label class="form-check-label" for="currency-active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <div class="form-control bg-light" id="currency-preview">$1,234.56</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCurrency()">Save Currency</button>
            </div>
        </div>
    </div>
</div>

<!-- Rate History Modal -->
<div class="modal fade" id="rateHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exchange Rate History - <span id="history-currency-code"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Rate</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody id="rate-history-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Rate Modal -->
<div class="modal fade" id="updateRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Exchange Rate - <span id="rate-currency-code"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rate-form">
                    <input type="hidden" id="rate-currency-id">
                    <div class="mb-3">
                        <label class="form-label">Current Rate</label>
                        <input type="text" class="form-control" id="rate-current" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Rate *</label>
                        <input type="number" class="form-control" id="rate-new" step="0.000001" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveRate()">Update Rate</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE = '{{ config("app.api_url", env("API_URL", "http://localhost:8000")) }}/api';
let currencies = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCurrencies();

    // Update preview on form changes
    ['currency-symbol', 'currency-symbol-position', 'currency-decimal-places',
     'currency-decimal-separator', 'currency-thousand-separator'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', updatePreview);
        document.getElementById(id)?.addEventListener('input', updatePreview);
    });
});

function loadCurrencies() {
    fetch(`${API_BASE}/admin/currencies`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currencies = data.data;
                renderCurrencies();
                updateStats();
                populateConverterSelects();
            }
        })
        .catch(err => {
            console.error('Error loading currencies:', err);
            document.getElementById('currencies-tbody').innerHTML =
                '<tr><td colspan="9" class="text-center text-danger">Error loading currencies</td></tr>';
        });
}

function renderCurrencies() {
    const tbody = document.getElementById('currencies-tbody');

    if (currencies.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No currencies found</td></tr>';
        return;
    }

    tbody.innerHTML = currencies.map(c => `
        <tr>
            <td><strong>${c.code}</strong></td>
            <td>${c.name}</td>
            <td class="text-center"><span class="badge bg-secondary">${c.symbol}</span></td>
            <td>
                ${c.rate ? c.rate.toFixed(6) : '-'}
                ${c.rate ? `<button class="btn btn-sm btn-link p-0 ms-1" onclick="showUpdateRate(${c.id}, '${c.code}', ${c.rate})" title="Update Rate">
                    <i class="fas fa-edit"></i>
                </button>` : ''}
            </td>
            <td>${formatCurrencyPreview(c, 1234.56)}</td>
            <td>
                <span class="badge ${c.is_active ? 'bg-success' : 'bg-danger'}">
                    ${c.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>
                ${c.is_default ? '<i class="fas fa-star text-warning" title="Default Currency"></i>' :
                  `<button class="btn btn-sm btn-outline-warning" onclick="setDefault(${c.id})" title="Set as Default">
                      <i class="far fa-star"></i>
                   </button>`}
            </td>
            <td>
                <small>${c.fetched_at ? new Date(c.fetched_at).toLocaleDateString() : '-'}</small>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-info" onclick="showRateHistory(${c.id}, '${c.code}')" title="Rate History">
                        <i class="fas fa-history"></i>
                    </button>
                    <button class="btn btn-outline-primary" onclick="editCurrency(${c.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${!c.is_default ? `<button class="btn btn-outline-danger" onclick="deleteCurrency(${c.id}, '${c.code}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

function formatCurrencyPreview(currency, amount) {
    const decimals = currency.decimal_places || 2;
    const decSep = currency.decimal_separator || '.';
    const thousandSep = currency.thousand_separator || ',';

    let formatted = amount.toFixed(decimals);
    let parts = formatted.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
    formatted = parts.join(decSep);

    if (currency.symbol_position === 'after') {
        return formatted + currency.symbol;
    }
    return currency.symbol + formatted;
}

function updateStats() {
    document.getElementById('stat-total').textContent = currencies.length;
    document.getElementById('stat-active').textContent = currencies.filter(c => c.is_active).length;

    const defaultCurrency = currencies.find(c => c.is_default);
    document.getElementById('stat-default').textContent = defaultCurrency ? defaultCurrency.code : '-';

    const lastUpdate = currencies.reduce((latest, c) => {
        if (c.fetched_at && (!latest || new Date(c.fetched_at) > new Date(latest))) {
            return c.fetched_at;
        }
        return latest;
    }, null);

    document.getElementById('stat-last-update').textContent = lastUpdate ?
        new Date(lastUpdate).toLocaleString() : 'Never';
}

function populateConverterSelects() {
    const activeCurrencies = currencies.filter(c => c.is_active);
    const options = activeCurrencies.map(c => `<option value="${c.code}">${c.code} - ${c.name}</option>`).join('');

    document.getElementById('convert-from').innerHTML = options;
    document.getElementById('convert-to').innerHTML = options;

    // Set default selections
    const defaultCurrency = currencies.find(c => c.is_default);
    if (defaultCurrency) {
        document.getElementById('convert-from').value = defaultCurrency.code;
    }
    if (activeCurrencies.length > 1) {
        const secondCurrency = activeCurrencies.find(c => !c.is_default) || activeCurrencies[1];
        document.getElementById('convert-to').value = secondCurrency.code;
    }
}

function openCreateModal() {
    document.getElementById('currencyModalTitle').textContent = 'Add Currency';
    document.getElementById('currency-form').reset();
    document.getElementById('currency-id').value = '';
    document.getElementById('currency-active').checked = true;
    updatePreview();
}

function editCurrency(id) {
    const currency = currencies.find(c => c.id === id);
    if (!currency) return;

    document.getElementById('currencyModalTitle').textContent = 'Edit Currency';
    document.getElementById('currency-id').value = id;
    document.getElementById('currency-code').value = currency.code;
    document.getElementById('currency-name').value = currency.name;
    document.getElementById('currency-symbol').value = currency.symbol;
    document.getElementById('currency-symbol-position').value = currency.symbol_position || 'before';
    document.getElementById('currency-decimal-places').value = currency.decimal_places || 2;
    document.getElementById('currency-decimal-separator').value = currency.decimal_separator || '.';
    document.getElementById('currency-thousand-separator').value = currency.thousand_separator || ',';
    document.getElementById('currency-sort-order').value = currency.sort_order || 0;
    document.getElementById('currency-rate').value = currency.rate || '';
    document.getElementById('currency-active').checked = currency.is_active;

    updatePreview();
    new bootstrap.Modal(document.getElementById('currencyModal')).show();
}

function updatePreview() {
    const symbol = document.getElementById('currency-symbol').value || '$';
    const position = document.getElementById('currency-symbol-position').value || 'before';
    const decimals = parseInt(document.getElementById('currency-decimal-places').value) || 2;
    const decSep = document.getElementById('currency-decimal-separator').value || '.';
    const thousandSep = document.getElementById('currency-thousand-separator').value || ',';

    let amount = (1234.56).toFixed(decimals);
    let parts = amount.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
    amount = parts.join(decSep);

    const preview = position === 'after' ? amount + symbol : symbol + amount;
    document.getElementById('currency-preview').textContent = preview;
}

function saveCurrency() {
    const id = document.getElementById('currency-id').value;
    const data = {
        code: document.getElementById('currency-code').value.toUpperCase(),
        name: document.getElementById('currency-name').value,
        symbol: document.getElementById('currency-symbol').value,
        symbol_position: document.getElementById('currency-symbol-position').value,
        decimal_places: parseInt(document.getElementById('currency-decimal-places').value),
        decimal_separator: document.getElementById('currency-decimal-separator').value,
        thousand_separator: document.getElementById('currency-thousand-separator').value,
        sort_order: parseInt(document.getElementById('currency-sort-order').value) || 0,
        is_active: document.getElementById('currency-active').checked,
    };

    const rate = document.getElementById('currency-rate').value;
    if (rate && !id) {
        data.rate = parseFloat(rate);
    }

    const url = id ? `${API_BASE}/admin/currencies/${id}` : `${API_BASE}/admin/currencies`;
    const method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('currencyModal')).hide();
            loadCurrencies();
            showToast('Currency saved successfully', 'success');
        } else {
            showToast(data.error || 'Failed to save currency', 'danger');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error saving currency', 'danger');
    });
}

function setDefault(id) {
    if (!confirm('Set this currency as the default? This will affect all pricing calculations.')) return;

    fetch(`${API_BASE}/admin/currencies/${id}/default`, { method: 'PUT' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadCurrencies();
                showToast('Default currency updated', 'success');
            } else {
                showToast(data.error || 'Failed to set default', 'danger');
            }
        })
        .catch(err => showToast('Error setting default currency', 'danger'));
}

function deleteCurrency(id, code) {
    if (!confirm(`Delete currency ${code}? This cannot be undone.`)) return;

    fetch(`${API_BASE}/admin/currencies/${id}`, { method: 'DELETE' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadCurrencies();
                showToast('Currency deleted', 'success');
            } else {
                showToast(data.error || 'Failed to delete currency', 'danger');
            }
        })
        .catch(err => showToast('Error deleting currency', 'danger'));
}

function showUpdateRate(id, code, currentRate) {
    document.getElementById('rate-currency-id').value = id;
    document.getElementById('rate-currency-code').textContent = code;
    document.getElementById('rate-current').value = currentRate ? currentRate.toFixed(6) : 'Not set';
    document.getElementById('rate-new').value = currentRate || '';

    new bootstrap.Modal(document.getElementById('updateRateModal')).show();
}

function saveRate() {
    const id = document.getElementById('rate-currency-id').value;
    const rate = parseFloat(document.getElementById('rate-new').value);

    if (!rate || rate <= 0) {
        showToast('Please enter a valid rate', 'warning');
        return;
    }

    fetch(`${API_BASE}/admin/currencies/${id}/rate`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rate: rate })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('updateRateModal')).hide();
            loadCurrencies();
            showToast('Exchange rate updated', 'success');
        } else {
            showToast(data.error || 'Failed to update rate', 'danger');
        }
    })
    .catch(err => showToast('Error updating rate', 'danger'));
}

function showRateHistory(id, code) {
    document.getElementById('history-currency-code').textContent = code;

    fetch(`${API_BASE}/admin/currencies/${id}/history`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('rate-history-tbody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(h => `
                    <tr>
                        <td>${new Date(h.recorded_at).toLocaleString()}</td>
                        <td>${parseFloat(h.rate).toFixed(6)}</td>
                        <td><span class="badge bg-secondary">${h.source || 'manual'}</span></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No history available</td></tr>';
            }

            new bootstrap.Modal(document.getElementById('rateHistoryModal')).show();
        })
        .catch(err => showToast('Error loading rate history', 'danger'));
}

function convertCurrency() {
    const amount = parseFloat(document.getElementById('convert-amount').value);
    const from = document.getElementById('convert-from').value;
    const to = document.getElementById('convert-to').value;

    if (!amount || !from || !to) {
        showToast('Please fill in all conversion fields', 'warning');
        return;
    }

    fetch(`${API_BASE}/currencies/convert?amount=${amount}&from=${from}&to=${to}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const result = data.data;
                const fromCurrency = currencies.find(c => c.code === from);
                const toCurrency = currencies.find(c => c.code === to);

                document.getElementById('result-text').textContent =
                    `${formatCurrencyPreview(fromCurrency, result.amount)} = ${formatCurrencyPreview(toCurrency, result.converted)}`;
                document.getElementById('result-rate').textContent =
                    `Rate: 1 ${from} = ${result.rate.toFixed(6)} ${to}`;
                document.getElementById('conversion-result').classList.remove('d-none');
            } else {
                showToast(data.error || 'Conversion failed', 'danger');
            }
        })
        .catch(err => showToast('Error converting currency', 'danger'));
}

function swapCurrencies() {
    const from = document.getElementById('convert-from');
    const to = document.getElementById('convert-to');
    const temp = from.value;
    from.value = to.value;
    to.value = temp;
}

function fetchExternalRates() {
    if (!confirm('Fetch latest exchange rates from external API?')) return;

    showToast('Fetching exchange rates...', 'info');

    fetch(`${API_BASE}/admin/currencies/fetch-rates`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadCurrencies();
                showToast(`Updated ${data.updated || 0} exchange rates`, 'success');
            } else {
                showToast(data.error || 'Failed to fetch rates', 'danger');
            }
        })
        .catch(err => showToast('Error fetching rates', 'danger'));
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    new bootstrap.Toast(toast, { delay: 3000 }).show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}
</script>
@endsection
