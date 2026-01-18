@extends('layouts.admin')

@section('title', 'Lead Details')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">{{ $lead->lead_number ?? 'Lead' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leads') }}">Leads</a></li>
                <li class="breadcrumb-item active">{{ $lead->lead_number ?? '' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#activityModal" title="Log a new activity for this lead">
            <i class="bi bi-plus-lg me-1"></i> Add Activity
        </button>
        <button class="btn btn-success" onclick="convertToDeal()" title="Convert this lead into a deal">
            <i class="bi bi-arrow-right-circle me-1"></i> Convert to Deal
        </button>
    </div>
</div>
@endsection

@section('content')
@if(!$lead)
<div class="alert alert-danger">Lead not found</div>
@else
@php
    // Ensure $lead is an object
    if (is_array($lead)) {
        $lead = json_decode(json_encode($lead));
    }
@endphp
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Lead Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $lead->first_name }} {{ $lead->last_name }}</h5>
                    <div class="d-flex gap-2">
                        @php
                            $priorityColors = ['hot' => 'danger', 'high' => 'warning', 'medium' => 'info', 'low' => 'secondary'];
                            $statusColors = ['new' => 'success', 'contacted' => 'info', 'qualified' => 'primary', 'proposal' => 'warning', 'negotiation' => 'warning', 'won' => 'success', 'lost' => 'danger', 'dormant' => 'secondary'];
                        @endphp
                        <span class="badge bg-{{ $priorityColors[$lead->priority] ?? 'secondary' }}">{{ ucfirst($lead->priority) }}</span>
                        <span class="badge bg-{{ $statusColors[$lead->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                            {{ ucfirst($lead->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $lead->email }}</p>
                        @if($lead->phone)
                        <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $lead->phone }}</p>
                        @endif
                        @if($lead->company)
                        <p class="mb-2"><i class="bi bi-building me-2 text-muted"></i>{{ $lead->company }}</p>
                        @endif
                        @if($lead->job_title)
                        <p class="mb-2"><i class="bi bi-person-badge me-2 text-muted"></i>{{ $lead->job_title }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><i class="bi bi-tag me-2 text-muted"></i>Source: {{ $lead->source_name ?? 'Unknown' }}</p>
                        @if($lead->estimated_value)
                        <p class="mb-2"><i class="bi bi-currency-dollar me-2 text-muted"></i>Est. Value: ${{ number_format($lead->estimated_value, 2) }}</p>
                        @endif
                        <p class="mb-2"><i class="bi bi-graph-up me-2 text-muted"></i>Lead Score: {{ $lead->lead_score ?? 0 }}/100</p>
                        @if($lead->expected_close_date)
                        <p class="mb-2"><i class="bi bi-calendar me-2 text-muted"></i>Expected Close: {{ \Carbon\Carbon::parse($lead->expected_close_date)->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
                @if($lead->notes)
                <hr>
                <h6>Notes</h6>
                <p class="text-muted mb-0">{{ $lead->notes }}</p>
                @endif
            </div>
        </div>

        <!-- Activities Timeline -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Activities</h6>
            </div>
            <div class="card-body">
                @forelse($activities as $activity)
                @php $activity = (object) $activity; @endphp
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0 me-3">
                        @php
                            $typeIcons = ['call' => 'telephone', 'email' => 'envelope', 'meeting' => 'calendar-event', 'note' => 'sticky', 'task' => 'check-square', 'status_change' => 'arrow-repeat', 'other' => 'three-dots'];
                            $typeColors = ['call' => 'success', 'email' => 'primary', 'meeting' => 'warning', 'note' => 'info', 'task' => 'secondary', 'status_change' => 'danger', 'other' => 'dark'];
                        @endphp
                        <div class="bg-{{ $typeColors[$activity->type] ?? 'secondary' }} bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-{{ $typeIcons[$activity->type] ?? 'three-dots' }} text-{{ $typeColors[$activity->type] ?? 'secondary' }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $activity->subject }}</div>
                        @if($activity->description)
                        <p class="text-muted small mb-1">{{ $activity->description }}</p>
                        @endif
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y g:i A') }}
                            @if($activity->outcome)
                            - <span class="badge bg-light text-dark">{{ ucfirst($activity->outcome) }}</span>
                            @endif
                        </small>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                    <p class="mb-0">No activities yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Related Deals -->
        @if(count($deals) > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-briefcase me-2"></i>Related Deals</h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($deals as $deal)
                @php $deal = (object) $deal; @endphp
                <a href="{{ route('admin.deals.detail', $deal->id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $deal->title }}</div>
                            <small class="text-muted">{{ $deal->deal_number }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">${{ number_format($deal->value) }}</div>
                            <span class="badge" style="background-color: {{ $deal->stage_color ?? '#6c757d' }}">{{ $deal->stage_name }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="statusSelect" onchange="updateLead('status', this.value)">
                        <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ $lead->status == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="proposal" {{ $lead->status == 'proposal' ? 'selected' : '' }}>Proposal</option>
                        <option value="negotiation" {{ $lead->status == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                        <option value="won" {{ $lead->status == 'won' ? 'selected' : '' }}>Won</option>
                        <option value="lost" {{ $lead->status == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <select class="form-select" id="prioritySelect" onchange="updateLead('priority', this.value)">
                        <option value="low" {{ $lead->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $lead->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $lead->priority == 'high' ? 'selected' : '' }}>High</option>
                        <option value="hot" {{ $lead->priority == 'hot' ? 'selected' : '' }}>Hot</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Lead Score -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Lead Score</h6>
            </div>
            <div class="card-body text-center">
                <div class="display-4 fw-bold text-{{ ($lead->lead_score ?? 0) >= 70 ? 'success' : (($lead->lead_score ?? 0) >= 40 ? 'warning' : 'danger') }}">
                    {{ $lead->lead_score ?? 0 }}
                </div>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar bg-{{ ($lead->lead_score ?? 0) >= 70 ? 'success' : (($lead->lead_score ?? 0) >= 40 ? 'warning' : 'danger') }}"
                         style="width: {{ $lead->lead_score ?? 0 }}%"></div>
                </div>
                <small class="text-muted">out of 100</small>
            </div>
        </div>

        <!-- Timeline Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <p class="mb-2 small">
                    <strong>Created:</strong><br>
                    {{ \Carbon\Carbon::parse($lead->created_at)->format('M d, Y g:i A') }}
                </p>
                @if($lead->last_contacted_at)
                <p class="mb-2 small">
                    <strong>Last Contacted:</strong><br>
                    {{ \Carbon\Carbon::parse($lead->last_contacted_at)->format('M d, Y g:i A') }}
                </p>
                @endif
                @if($lead->qualified_at)
                <p class="mb-2 small">
                    <strong>Qualified:</strong><br>
                    {{ \Carbon\Carbon::parse($lead->qualified_at)->format('M d, Y g:i A') }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Add Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="activityForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="activityType" required>
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                            <option value="task">Task</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="activitySubject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="activityDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Outcome</label>
                        <select class="form-select" id="activityOutcome">
                            <option value="">Select outcome</option>
                            <option value="completed">Completed</option>
                            <option value="no_answer">No Answer</option>
                            <option value="left_message">Left Message</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';
const LEAD_ID = {{ $lead->id ?? 0 }};

async function updateLead(field, value) {
    try {
        const response = await fetch(`${API_BASE}/admin/leads/${LEAD_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ [field]: value })
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to update');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

async function convertToDeal() {
    const title = prompt('Enter deal title:', '{{ $lead->company ?? "" }} - New Deal');
    if (!title) return;

    try {
        const response = await fetch(`${API_BASE}/admin/leads/${LEAD_ID}/convert`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ title: title })
        });

        if (response.ok) {
            const data = await response.json();
            alert('Lead converted to deal: ' + data.deal_number);
            window.location.href = '{{ route("admin.deals") }}';
        } else {
            alert('Failed to convert');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

document.getElementById('activityForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = {
        type: document.getElementById('activityType').value,
        subject: document.getElementById('activitySubject').value,
        description: document.getElementById('activityDescription').value || null,
        outcome: document.getElementById('activityOutcome').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/leads/${LEAD_ID}/activities`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to add activity');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
});
</script>
@endpush
@endif
@endsection
