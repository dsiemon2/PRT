@extends('layouts.admin')

@section('title', 'Add Drop Shipper')

@section('content')
<div class="page-header">
    <h1>Add Drop Shipper</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.dropshippers') }}">Drop Shippers</a></li>
            <li class="breadcrumb-item active">Add New</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <form class="admin-form">
            <!-- Company Information -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Company Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Company Name *</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Name *</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <!-- API Configuration -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">API Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rate Limit (requests/hour)</label>
                            <input type="number" class="form-control" value="1000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">IP Whitelist</label>
                            <input type="text" class="form-control" placeholder="Comma-separated IPs">
                            <small class="text-muted">Leave empty to allow all IPs</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Permissions</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm_products" checked>
                            <label class="form-check-label" for="perm_products">Product Catalog (Read)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm_inventory" checked>
                            <label class="form-check-label" for="perm_inventory">Inventory Levels (Read)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm_pricing" checked>
                            <label class="form-check-label" for="perm_pricing">Pricing (Read)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm_orders" checked>
                            <label class="form-check-label" for="perm_orders">Order Creation (Write)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm_shipping">
                            <label class="form-check-label" for="perm_shipping">Shipping Rates (Read)</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission & Pricing -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Commission & Pricing</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Commission Rate (%)</label>
                            <input type="number" class="form-control" value="5" step="0.1" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Markup Percentage (%)</label>
                            <input type="number" class="form-control" value="0" step="0.1" min="0">
                            <small class="text-muted">Additional markup on wholesale prices</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pricing Tier</label>
                        <select class="form-select">
                            <option value="wholesale">Wholesale (Standard)</option>
                            <option value="preferred">Preferred Partner (10% discount)</option>
                            <option value="premium">Premium Partner (15% discount)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Additional Notes</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" rows="4" placeholder="Internal notes about this drop shipper..."></textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-prt">
                    <i class="bi bi-check-circle"></i> Create Drop Shipper
                </button>
                <a href="{{ route('admin.dropshippers') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Guide</h5>
            </div>
            <div class="card-body">
                <h6>API Keys</h6>
                <p class="small text-muted">API keys will be generated automatically after the drop shipper is created. You can regenerate keys from the detail page.</p>

                <h6>Webhooks</h6>
                <p class="small text-muted">Drop shippers can configure webhooks to receive real-time updates about inventory changes and order status.</p>

                <h6>Permissions</h6>
                <p class="small text-muted">Carefully select which API endpoints the drop shipper can access. Order creation should only be enabled for trusted partners.</p>
            </div>
        </div>
    </div>
</div>
@endsection
