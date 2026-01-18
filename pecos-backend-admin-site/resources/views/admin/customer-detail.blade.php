@extends('layouts.admin')

@section('title', 'Customer Details')

@section('styles')
<style>
    /* Customer 360 Styles */
    .customer-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .customer-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #8B4513;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        color: white;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .customer-tier-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .tier-bronze { background: #cd7f32; color: white; }
    .tier-silver { background: #c0c0c0; color: #333; }
    .tier-gold { background: linear-gradient(135deg, #f5af19 0%, #f12711 100%); color: white; }
    .tier-platinum { background: linear-gradient(135deg, #667db6 0%, #0082c8 100%); color: white; }

    /* Customer Tags */
    .customer-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.6rem;
        border-radius: 15px;
        font-size: 0.8rem;
        margin: 0.15rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .customer-tag:hover {
        transform: scale(1.05);
    }

    .customer-tag .remove-tag {
        margin-left: 0.4rem;
        opacity: 0.7;
        cursor: pointer;
    }

    .customer-tag .remove-tag:hover {
        opacity: 1;
    }

    /* Metrics Cards */
    .metric-card {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }

    .metric-card:hover {
        transform: translateY(-2px);
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .metric-label {
        font-size: 0.8rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-change {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .metric-change.positive { color: #27ae60; }
    .metric-change.negative { color: #e74c3c; }

    /* Health Score Gauge */
    .health-gauge {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }

    .health-gauge svg {
        transform: rotate(-90deg);
    }

    .health-gauge-bg {
        fill: none;
        stroke: #ecf0f1;
        stroke-width: 10;
    }

    .health-gauge-value {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dasharray 0.5s;
    }

    .health-score-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.25rem;
        font-weight: bold;
    }

    /* RFM Segment Badge */
    .rfm-segment {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .rfm-champion { background: #27ae60; color: white; }
    .rfm-loyal { background: #3498db; color: white; }
    .rfm-bigspender { background: #9b59b6; color: white; }
    .rfm-promising { background: #f39c12; color: white; }
    .rfm-atrisk { background: #e74c3c; color: white; }
    .rfm-hibernating { background: #95a5a6; color: white; }
    .rfm-regular { background: #34495e; color: white; }

    /* Churn Risk */
    .churn-indicator {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.7rem;
        border-radius: 15px;
        font-size: 0.85rem;
    }

    .churn-low { background: #d4edda; color: #155724; }
    .churn-medium { background: #fff3cd; color: #856404; }
    .churn-high { background: #f8d7da; color: #721c24; }

    /* Activity Timeline */
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .activity-item {
        position: relative;
        padding-bottom: 1.25rem;
    }

    .activity-item:last-child {
        padding-bottom: 0;
    }

    .activity-icon {
        position: absolute;
        left: -25px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: white;
    }

    .activity-icon.order { background: #27ae60; }
    .activity-icon.email { background: #3498db; }
    .activity-icon.note { background: #f39c12; }
    .activity-icon.support { background: #e74c3c; }
    .activity-icon.loyalty { background: #9b59b6; }
    .activity-icon.review { background: #1abc9c; }
    .activity-icon.login { background: #7f8c8d; }
    .activity-icon.other { background: #34495e; }

    .activity-content {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 8px;
    }

    .activity-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        font-size: 0.75rem;
        color: #7f8c8d;
    }

    /* Notes Section */
    .note-card {
        background: #fffef0;
        border-left: 3px solid #f39c12;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border-radius: 0 8px 8px 0;
    }

    .note-card.pinned {
        background: #fff9e6;
        border-left-color: #e74c3c;
    }

    .note-card.pinned::before {
        content: '📌';
        margin-right: 0.5rem;
    }

    /* Segment Badges */
    .segment-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        background: #e9ecef;
        border-radius: 12px;
        font-size: 0.8rem;
        margin: 0.15rem;
    }

    /* Quick Actions */
    .quick-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        color: white;
        text-decoration: none;
        transition: all 0.2s;
        margin-right: 0.5rem;
    }

    .quick-action-btn:hover {
        background: rgba(255,255,255,0.25);
        color: white;
        transform: scale(1.1);
    }

    /* Tag Modal */
    .tag-option {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        margin: 0.25rem;
        border-radius: 20px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .tag-option:hover {
        border-color: #333;
    }

    .tag-option.selected {
        border-color: #27ae60;
    }
</style>
@endsection

@section('content')
@php
    $firstName = $customer['first_name'] ?? '';
    $lastName = $customer['last_name'] ?? '';
    $name = trim("$firstName $lastName") ?: ($customer['UserName'] ?? $customer['name'] ?? 'Unknown');
    $initials = strtoupper(substr($firstName ?: 'U', 0, 1) . substr($lastName ?: 'N', 0, 1));

    $tier = strtolower($customer['loyalty_tier'] ?? 'bronze');
    $tierName = ucfirst($tier);

    // CRM Data
    $metrics = $crm['metrics'] ?? null;
    $tags = $crm['tags'] ?? [];
    $activities = $crm['activities'] ?? [];
    $pinnedNotes = $crm['pinned_notes'] ?? [];
    $segments = $crm['segments'] ?? [];

    // Health score color
    $healthScore = $metrics['health_score'] ?? 50;
    if ($healthScore >= 70) $healthColor = '#27ae60';
    elseif ($healthScore >= 40) $healthColor = '#f39c12';
    else $healthColor = '#e74c3c';

    // Churn risk level
    $churnRisk = $metrics['churn_risk_score'] ?? 0.5;
    if ($churnRisk <= 0.3) { $churnLevel = 'low'; $churnText = 'Low Risk'; }
    elseif ($churnRisk <= 0.6) { $churnLevel = 'medium'; $churnText = 'Medium Risk'; }
    else { $churnLevel = 'high'; $churnText = 'High Risk'; }

    // RFM segment class
    $rfmSegment = strtolower(str_replace(' ', '', $metrics['rfm_segment'] ?? 'regular'));
@endphp

<div class="page-header">
    <h1>Customer 360</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.customers') }}">Customers</a></li>
            <li class="breadcrumb-item active">{{ $name }}</li>
        </ol>
    </nav>
</div>

@if($customer)
<!-- Customer Header -->
<div class="customer-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="customer-avatar">{{ $initials }}</div>
        </div>
        <div class="col">
            <h2 class="mb-1">{{ $name }}</h2>
            <p class="mb-2 opacity-75">
                <i class="bi bi-envelope me-2"></i>{{ $customer['email'] ?? $customer['Email'] ?? 'N/A' }}
                @if($customer['phone'] ?? $customer['Phone'] ?? null)
                    <span class="ms-3"><i class="bi bi-telephone me-2"></i>{{ $customer['phone'] ?? $customer['Phone'] }}</span>
                @endif
            </p>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="customer-tier-badge tier-{{ $tier }}">
                    <i class="bi bi-star-fill me-1"></i>{{ $tierName }} Member
                </span>
                <span class="opacity-75">Customer since {{ isset($customer['created_at']) ? date('M Y', strtotime($customer['created_at'])) : 'N/A' }}</span>

                <!-- Customer Tags -->
                @foreach($tags as $tag)
                <span class="customer-tag" style="background-color: {{ $tag->color ?? '#6c757d' }}; color: white;">
                    {{ $tag->name }}
                    <span class="remove-tag" onclick="removeTag({{ $customer['id'] }}, {{ $tag->id }})" title="Remove tag">&times;</span>
                </span>
                @endforeach
                <button class="btn btn-sm btn-outline-light" onclick="showAddTagModal()" title="Add Tag">
                    <i class="bi bi-tag-fill"></i> +
                </button>
            </div>
        </div>
        <div class="col-auto">
            <!-- Quick Actions -->
            <div class="d-flex">
                <a href="mailto:{{ $customer['email'] ?? $customer['Email'] ?? '' }}" class="quick-action-btn" title="Send Email">
                    <i class="bi bi-envelope"></i>
                </a>
                <a href="tel:{{ $customer['phone'] ?? $customer['Phone'] ?? '' }}" class="quick-action-btn" title="Call">
                    <i class="bi bi-telephone"></i>
                </a>
                <button class="quick-action-btn" onclick="showAddNoteModal()" title="Add Note">
                    <i class="bi bi-journal-plus"></i>
                </button>
                <button class="quick-action-btn" onclick="editCustomer()" title="Edit Customer">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Key Metrics Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-value">${{ number_format($metrics['lifetime_value'] ?? $customer['total_spent'] ?? 0, 0) }}</div>
                    <div class="metric-label">Lifetime Value</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-value">{{ $metrics['total_orders'] ?? $customer['order_count'] ?? 0 }}</div>
                    <div class="metric-label">Total Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-value">${{ number_format($metrics['avg_order_value'] ?? $customer['avg_order_value'] ?? 0, 0) }}</div>
                    <div class="metric-label">Avg Order</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-value">{{ $metrics['days_since_last_order'] ?? '-' }}</div>
                    <div class="metric-label">Days Since Order</div>
                </div>
            </div>
        </div>

        <!-- RFM & Health Metrics -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customer Health</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="refreshMetrics()" title="Refresh Metrics">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <!-- Health Score Gauge -->
                        <div class="health-gauge">
                            <svg width="100" height="100">
                                <circle class="health-gauge-bg" cx="50" cy="50" r="40"></circle>
                                <circle class="health-gauge-value"
                                        cx="50" cy="50" r="40"
                                        stroke="{{ $healthColor }}"
                                        stroke-dasharray="{{ $healthScore * 2.51 }}, 251"></circle>
                            </svg>
                            <div class="health-score-text" style="color: {{ $healthColor }}">{{ $healthScore }}</div>
                        </div>
                        <div class="mt-2 text-muted small">Health Score</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="rfm-segment rfm-{{ $rfmSegment }}">
                            {{ $metrics['rfm_segment'] ?? 'Regular' }}
                        </div>
                        <div class="mt-2 text-muted small">RFM Segment</div>
                        <div class="mt-1 small">
                            R: {{ $metrics['rfm_recency_score'] ?? '-' }} |
                            F: {{ $metrics['rfm_frequency_score'] ?? '-' }} |
                            M: {{ $metrics['rfm_monetary_score'] ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="churn-indicator churn-{{ $churnLevel }}">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $churnText }}
                        </div>
                        <div class="mt-2 text-muted small">Churn Risk</div>
                        <div class="mt-1 small">{{ round(($metrics['churn_risk_score'] ?? 0.5) * 100) }}% probability</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Activity Timeline</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="filterActivities('all')">All Activities</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterActivities('order')">Orders</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterActivities('email')">Emails</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterActivities('note')">Notes</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterActivities('support')">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-timeline" id="activityTimeline">
                    @forelse($activities as $activity)
                    @php
                        $iconClass = $activity->activity_type ?? 'other';
                        $icons = [
                            'order' => 'bi-box',
                            'email' => 'bi-envelope',
                            'note' => 'bi-journal-text',
                            'support' => 'bi-headset',
                            'loyalty' => 'bi-gift',
                            'review' => 'bi-star',
                            'login' => 'bi-box-arrow-in-right',
                            'other' => 'bi-circle'
                        ];
                    @endphp
                    <div class="activity-item" data-type="{{ $activity->activity_type }}">
                        <div class="activity-icon {{ $iconClass }}">
                            <i class="bi {{ $icons[$iconClass] ?? 'bi-circle' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity->title }}</div>
                            @if($activity->description)
                            <div class="small text-muted">{{ $activity->description }}</div>
                            @endif
                            <div class="activity-time">{{ date('M d, Y g:i A', strtotime($activity->created_at)) }}</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted mb-0">No activity recorded yet</p>
                    @endforelse
                </div>
                @if(count($activities) >= 10)
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadMoreActivities()">Load More</button>
                </div>
                @endif
            </div>
        </div>

        <!-- Order History -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order History</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders['data'] ?? $orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.detail', $order['order_id'] ?? 0) }}">#{{ $order['order_number'] ?? $order['order_id'] ?? 'N/A' }}</a></td>
                            <td>{{ isset($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : 'N/A' }}</td>
                            <td>{{ $order['item_count'] ?? '-' }}</td>
                            <td>${{ number_format($order['total'] ?? 0, 2) }}</td>
                            <td>
                                <span class="status-badge {{ ($order['status'] ?? '') == 'delivered' ? 'active' : (($order['status'] ?? '') == 'cancelled' ? 'inactive' : 'pending') }}">
                                    {{ ucfirst($order['status'] ?? 'Unknown') }}
                                </span>
                            </td>
                            <td><a href="{{ route('admin.orders.detail', $order['order_id'] ?? 0) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Notes Section -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Notes</h5>
                <button class="btn btn-sm btn-primary" onclick="showAddNoteModal()">
                    <i class="bi bi-plus"></i> Add
                </button>
            </div>
            <div class="card-body" id="notesContainer">
                @forelse($pinnedNotes as $note)
                <div class="note-card {{ $note->is_pinned ? 'pinned' : '' }}">
                    <div class="small">{{ $note->note }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-muted small">{{ date('M d, Y', strtotime($note->created_at)) }}</span>
                        <div>
                            <button class="btn btn-sm btn-link p-0 me-2" onclick="togglePinNote({{ $note->id }})" title="Pin/Unpin">
                                <i class="bi {{ $note->is_pinned ? 'bi-pin-fill' : 'bi-pin' }}"></i>
                            </button>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteNote({{ $note->id }})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0">No notes yet. Add one to keep track of important information.</p>
                @endforelse
                <button class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="loadAllNotes()">View All Notes</button>
            </div>
        </div>

        <!-- Segments -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Customer Segments</h5>
            </div>
            <div class="card-body">
                @forelse($segments as $segment)
                <span class="segment-badge">{{ $segment->name }}</span>
                @empty
                <p class="text-muted mb-0">Not in any segments</p>
                @endforelse
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <p><i class="bi bi-envelope me-2"></i> {{ $customer['email'] ?? $customer['Email'] ?? 'N/A' }}</p>
                <p><i class="bi bi-telephone me-2"></i> {{ $customer['phone'] ?? $customer['Phone'] ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Address -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Address</h5>
            </div>
            <div class="card-body">
                @if($customer['address'] ?? null)
                <p class="mb-0">
                    {{ $customer['address']['address1'] ?? '' }}<br>
                    @if($customer['address']['address2'] ?? null)
                    {{ $customer['address']['address2'] }}<br>
                    @endif
                    {{ $customer['address']['city'] ?? '' }}, {{ $customer['address']['state'] ?? '' }} {{ $customer['address']['zip'] ?? '' }}
                </p>
                @else
                <p class="text-muted mb-0">No address on file</p>
                @endif
            </div>
        </div>

        <!-- Loyalty -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Loyalty Program</h5>
            </div>
            <div class="card-body">
                <p><strong>Tier:</strong>
                    <span class="badge bg-{{ $tier == 'gold' ? 'warning' : ($tier == 'silver' ? 'secondary' : ($tier == 'platinum' ? 'info' : 'dark')) }}">
                        {{ $tierName }}
                    </span>
                </p>
                <p><strong>Points Balance:</strong> {{ number_format($customer['loyalty_points'] ?? 0) }}</p>
                <hr>
                <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">Adjust Points</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Tag Modal -->
<div class="modal fade" id="addTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tag to Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="availableTags" class="mb-3">
                    <!-- Tags will be loaded here -->
                    <p class="text-muted">Loading tags...</p>
                </div>
                <hr>
                <h6>Create New Tag</h6>
                <div class="row g-2">
                    <div class="col-8">
                        <input type="text" class="form-control" id="newTagName" placeholder="Tag name">
                    </div>
                    <div class="col-4">
                        <input type="color" class="form-control form-control-color w-100" id="newTagColor" value="#6c757d">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNewTag()">Add Tag</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="noteContent" rows="4" placeholder="Enter note..."></textarea>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="pinNote">
                    <label class="form-check-label" for="pinNote">Pin this note</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNote()">Save Note</button>
            </div>
        </div>
    </div>
</div>

@else
<div class="alert alert-warning">Customer not found</div>
@endif
@endsection

@section('scripts')
<script>
    const customerId = {{ $customer['id'] ?? 0 }};
    const apiUrl = '{{ rtrim(env("API_PUBLIC_URL", "http://localhost:8300/api/v1"), "/") }}';

    // Tag functions
    function showAddTagModal() {
        loadAvailableTags();
        new bootstrap.Modal(document.getElementById('addTagModal')).show();
    }

    async function loadAvailableTags() {
        try {
            const response = await fetch(`${apiUrl}/admin/crm/tags`);
            const data = await response.json();
            const container = document.getElementById('availableTags');

            if (data.data && data.data.length > 0) {
                container.innerHTML = data.data.map(tag => `
                    <span class="tag-option" style="background-color: ${tag.color}; color: white;"
                          onclick="assignTag(${tag.id})">${tag.name}</span>
                `).join('');
            } else {
                container.innerHTML = '<p class="text-muted">No tags available. Create one below.</p>';
            }
        } catch (error) {
            console.error('Error loading tags:', error);
        }
    }

    async function assignTag(tagId) {
        try {
            await fetch(`${apiUrl}/admin/crm/tags/assign`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ customer_id: customerId, tag_id: tagId })
            });
            location.reload();
        } catch (error) {
            console.error('Error assigning tag:', error);
        }
    }

    async function removeTag(customerId, tagId) {
        if (!confirm('Remove this tag?')) return;
        try {
            await fetch(`${apiUrl}/admin/crm/tags/remove`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ customer_id: customerId, tag_id: tagId })
            });
            location.reload();
        } catch (error) {
            console.error('Error removing tag:', error);
        }
    }

    async function saveNewTag() {
        const name = document.getElementById('newTagName').value.trim();
        const color = document.getElementById('newTagColor').value;

        if (!name) {
            alert('Please enter a tag name');
            return;
        }

        try {
            const response = await fetch(`${apiUrl}/admin/crm/tags`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, color })
            });
            const data = await response.json();

            if (data.id) {
                await assignTag(data.id);
            }
        } catch (error) {
            console.error('Error creating tag:', error);
        }
    }

    // Note functions
    function showAddNoteModal() {
        document.getElementById('noteContent').value = '';
        document.getElementById('pinNote').checked = false;
        new bootstrap.Modal(document.getElementById('addNoteModal')).show();
    }

    async function saveNote() {
        const note = document.getElementById('noteContent').value.trim();
        const isPinned = document.getElementById('pinNote').checked;

        if (!note) {
            alert('Please enter a note');
            return;
        }

        try {
            await fetch(`${apiUrl}/admin/crm/notes`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customer_id: customerId,
                    note: note,
                    is_pinned: isPinned,
                    created_by: 1 // TODO: Get actual admin user ID
                })
            });
            location.reload();
        } catch (error) {
            console.error('Error saving note:', error);
        }
    }

    async function deleteNote(noteId) {
        if (!confirm('Delete this note?')) return;
        try {
            await fetch(`${apiUrl}/admin/crm/notes/${noteId}`, { method: 'DELETE' });
            location.reload();
        } catch (error) {
            console.error('Error deleting note:', error);
        }
    }

    async function togglePinNote(noteId) {
        try {
            await fetch(`${apiUrl}/admin/crm/notes/${noteId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_pinned: true }) // Toggle would need current state
            });
            location.reload();
        } catch (error) {
            console.error('Error toggling pin:', error);
        }
    }

    // Activity filter
    function filterActivities(type) {
        const items = document.querySelectorAll('.activity-item');
        items.forEach(item => {
            if (type === 'all' || item.dataset.type === type) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Refresh metrics
    async function refreshMetrics() {
        try {
            await fetch(`${apiUrl}/admin/crm/customers/${customerId}/metrics`);
            location.reload();
        } catch (error) {
            console.error('Error refreshing metrics:', error);
        }
    }

    function editCustomer() {
        // TODO: Implement edit modal
        alert('Edit functionality coming soon');
    }

    function loadMoreActivities() {
        // TODO: Implement pagination
        alert('Loading more activities...');
    }

    function loadAllNotes() {
        // TODO: Implement notes modal
        alert('All notes view coming soon');
    }
</script>
@endsection
