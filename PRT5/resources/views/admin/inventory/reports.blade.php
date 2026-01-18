@extends('layouts.admin')

@section('title', $reportTitle ?? 'Inventory Reports')

@section('styles')
<style>
    .report-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .report-header {
        border-bottom: 2px solid var(--prt-brown);
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    @media print {
        .no-print { display: none; }
        .report-card { box-shadow: none; }
    }
    /* Row selection styles */
    .table tbody tr.row-selected td {
        background-color: #e3f2fd !important;
        color: #333 !important;
    }
    .table tbody tr.row-selected td strong,
    .table tbody tr.row-selected td .badge {
        color: #333 !important;
    }
    .table tbody tr {
        cursor: pointer;
    }
    .table tbody tr:hover:not(.row-selected) td {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4 no-print">
        <div class="col-12">
            <h1><i class="bi bi-graph-up"></i> Inventory Reports</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Report Type Selector -->
    <div class="report-card no-print">
        <h4><i class="bi bi-list-check"></i> Select Report Type</h4>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.inventory.reports', ['report' => 'valuation']) }}"
               class="btn {{ $reportType == 'valuation' ? 'btn-primary' : 'btn-outline-primary' }}"
               data-bs-toggle="tooltip" title="View inventory value by category">
                <i class="bi bi-currency-dollar"></i> Inventory Valuation
            </a>
            <a href="{{ route('admin.inventory.reports', ['report' => 'stock_status']) }}"
               class="btn {{ $reportType == 'stock_status' ? 'btn-primary' : 'btn-outline-primary' }}"
               data-bs-toggle="tooltip" title="View products by stock level status">
                <i class="bi bi-box-seam"></i> Stock Status
            </a>
            <a href="{{ route('admin.inventory.reports', ['report' => 'movement']) }}"
               class="btn {{ $reportType == 'movement' ? 'btn-primary' : 'btn-outline-primary' }}"
               data-bs-toggle="tooltip" title="View stock transactions in the last 30 days">
                <i class="bi bi-arrow-left-right"></i> Stock Movement
            </a>
            <a href="{{ route('admin.inventory.reports', ['report' => 'low_stock']) }}"
               class="btn {{ $reportType == 'low_stock' ? 'btn-primary' : 'btn-outline-primary' }}"
               data-bs-toggle="tooltip" title="View products that need reordering">
                <i class="bi bi-exclamation-triangle"></i> Low Stock
            </a>
        </div>
        <div class="mt-3">
            <button onclick="window.print()" class="btn btn-success" data-bs-toggle="tooltip" title="Print this report">
                <i class="bi bi-printer"></i> Print Report
            </button>
            <a href="{{ route('admin.inventory.export', ['report' => $reportType]) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Export report data to CSV file">
                <i class="bi bi-download"></i> Export to CSV
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to inventory dashboard">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Report Content -->
    <div class="report-card">
        <div class="report-header">
            <h2>{{ $reportTitle ?? 'Report' }}</h2>
            <p class="text-muted mb-0">{{ $reportDescription ?? '' }}</p>
            <small class="text-muted">Generated: {{ now()->format('F d, Y g:i A') }}</small>
        </div>

        @if($reportType == 'valuation')
            <!-- Valuation Report -->
            <table class="table table-striped table-hover">
                <thead style="background-color: var(--prt-brown); color: white;">
                    <tr>
                        <th>Category</th>
                        <th class="text-end">Products</th>
                        <th class="text-end">Total Units</th>
                        <th class="text-end">Cost Value</th>
                        <th class="text-end">Retail Value</th>
                        <th class="text-end">Potential Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['categories'] ?? [] as $row)
                        <tr onclick="highlightRow(event)">
                            <td>{{ $row['Category'] ?? 'Uncategorized' }}</td>
                            <td class="text-end">{{ number_format($row['product_count'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($row['total_units'] ?? 0) }}</td>
                            <td class="text-end">${{ number_format($row['cost_value'] ?? 0, 2) }}</td>
                            <td class="text-end">${{ number_format($row['retail_value'] ?? 0, 2) }}</td>
                            <td class="text-end text-success">${{ number_format($row['potential_profit'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @if(isset($data['totals']))
                <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                    <tr>
                        <td>TOTAL</td>
                        <td class="text-end">{{ number_format($data['totals']['product_count'] ?? 0) }}</td>
                        <td class="text-end">{{ number_format($data['totals']['total_units'] ?? 0) }}</td>
                        <td class="text-end">${{ number_format($data['totals']['cost_value'] ?? 0, 2) }}</td>
                        <td class="text-end">${{ number_format($data['totals']['retail_value'] ?? 0, 2) }}</td>
                        <td class="text-end text-success">${{ number_format($data['totals']['potential_profit'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>

            @if(isset($data['total_value']))
                <div class="alert alert-info mt-3">
                    <strong>Total Inventory Value at Cost:</strong> ${{ number_format($data['total_value'], 2) }}
                </div>
            @endif

        @elseif($reportType == 'stock_status')
            <!-- Stock Status Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($data['in_stock'] ?? 0) }}</h3>
                            <p class="mb-0"><i class="bi bi-check-circle"></i> In Stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning">
                        <div class="card-body text-center">
                            <h3>{{ number_format($data['low_stock'] ?? 0) }}</h3>
                            <p class="mb-0"><i class="bi bi-exclamation-triangle"></i> Low Stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($data['out_of_stock'] ?? 0) }}</h3>
                            <p class="mb-0"><i class="bi bi-x-circle"></i> Out of Stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($data['not_tracked'] ?? 0) }}</h3>
                            <p class="mb-0"><i class="bi bi-dash-circle"></i> Not Tracked</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Status Table -->
            @if(isset($data['status_breakdown']))
            <table class="table table-striped table-hover">
                <thead style="background-color: var(--prt-brown); color: white;">
                    <tr>
                        <th>Status</th>
                        <th class="text-end">Product Count</th>
                        <th class="text-end">Total Units</th>
                        <th class="text-end">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['status_breakdown'] as $row)
                        <tr onclick="highlightRow(event)">
                            <td>
                                @if($row['status'] == 'Out of Stock')
                                    <i class="bi bi-x-circle text-danger"></i>
                                @elseif($row['status'] == 'Low Stock')
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                @else
                                    <i class="bi bi-check-circle text-success"></i>
                                @endif
                                {{ $row['status'] }}
                            </td>
                            <td class="text-end">{{ number_format($row['product_count'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($row['total_units'] ?? 0) }}</td>
                            <td class="text-end">${{ number_format($row['total_value'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

        @elseif($reportType == 'low_stock')
            <!-- Low Stock Report -->
            <table class="table table-striped table-hover">
                <thead style="background-color: var(--prt-brown); color: white;">
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Available</th>
                        <th class="text-end">Threshold</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['products'] ?? [] as $product)
                        <tr onclick="highlightRow(event)">
                            <td>
                                <strong>{{ $product->ShortDescription ?? $product['ShortDescription'] ?? '' }}</strong>
                                <br><small class="text-muted">{{ $product->ItemNumber ?? $product['ItemNumber'] ?? '' }}</small>
                            </td>
                            <td class="text-end">{{ $product->availableQuantity ?? $product['available'] ?? 0 }}</td>
                            <td class="text-end">{{ $product->low_stock_threshold ?? $product['low_stock_threshold'] ?? 5 }}</td>
                            <td>
                                @php
                                    $available = $product->availableQuantity ?? $product['available'] ?? 0;
                                @endphp
                                <span class="badge bg-{{ $available <= 0 ? 'danger' : 'warning' }}">
                                    {{ $available <= 0 ? 'Out of Stock' : 'Low Stock' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.inventory.edit', $product->id ?? $product['id'] ?? 0) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit inventory to restock this product">
                                    <i class="bi bi-box-arrow-in-down"></i> Restock
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No low stock items - all products are well stocked!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(isset($data['total_reorder_cost']) && $data['total_reorder_cost'] > 0)
                <div class="alert alert-warning mt-3">
                    <strong>Estimated Reorder Cost:</strong> ${{ number_format($data['total_reorder_cost'], 2) }}
                </div>
            @endif

        @elseif($reportType == 'movement')
            <!-- Movement Report -->
            <table class="table table-striped table-hover">
                <thead style="background-color: var(--prt-brown); color: white;">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th class="text-end">Change</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['transactions'] ?? [] as $tx)
                        <tr onclick="highlightRow(event)">
                            <td>{{ \Carbon\Carbon::parse($tx->created_at ?? $tx['created_at'])->format('M j, Y H:i') }}</td>
                            <td>{{ $tx->product->ShortDescription ?? $tx['product_name'] ?? 'Unknown' }}</td>
                            <td>{{ ucfirst($tx->transaction_type ?? $tx['type'] ?? '-') }}</td>
                            <td class="text-end {{ ($tx->quantity_change ?? $tx['quantity_change'] ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($tx->quantity_change ?? $tx['quantity_change'] ?? 0) > 0 ? '+' : '' }}{{ $tx->quantity_change ?? $tx['quantity_change'] ?? 0 }}
                            </td>
                            <td>{{ $tx->reason ?? $tx['reason'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No transactions in this period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="text-muted small mt-2">
                Showing transactions from the last {{ $data['days'] ?? 30 }} days
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Row highlighting function (like prt4)
function highlightRow(event) {
    const row = event.currentTarget;
    const wasSelected = row.classList.contains('row-selected');

    // Remove selection from all rows
    document.querySelectorAll('.table tbody tr').forEach(tr => {
        tr.classList.remove('row-selected');
    });

    // Toggle selection on clicked row
    if (!wasSelected) {
        row.classList.add('row-selected');
    }
}
</script>
@endpush
