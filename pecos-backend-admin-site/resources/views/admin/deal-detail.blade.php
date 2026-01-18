@extends('layouts.admin')

@section('title', 'Deal Details')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">{{ $deal->deal_number ?? 'Deal' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.deals') }}">Deals</a></li>
                <li class="breadcrumb-item active">{{ $deal->deal_number ?? '' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#activityModal" title="Log a new activity for this deal">
            <i class="bi bi-plus-lg me-1"></i> Add Activity
        </button>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editDealModal" title="Edit deal details">
            <i class="bi bi-pencil me-1"></i> Edit
        </button>
    </div>
</div>
@endsection

@section('content')
@if(!$deal)
<div class="alert alert-danger">Deal not found</div>
@else
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Deal Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $deal->title }}</h5>
                    <span class="badge" style="background-color: {{ $deal->stage_color ?? '#6c757d' }}">
                        {{ $deal->stage_name ?? 'Unknown Stage' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><i class="bi bi-currency-dollar me-2 text-muted"></i>Value: <strong class="text-success">${{ number_format($deal->value ?? 0, 2) }}</strong></p>
                        @if(isset($deal->customer_name) && $deal->customer_name)
                        <p class="mb-2"><i class="bi bi-person me-2 text-muted"></i>{{ $deal->customer_name }}</p>
                        @endif
                        @if(isset($deal->lead_number) && $deal->lead_number)
                        <p class="mb-2"><i class="bi bi-funnel me-2 text-muted"></i>From Lead: {{ $deal->lead_number }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><i class="bi bi-percent me-2 text-muted"></i>Probability: {{ $deal->probability ?? 0 }}%</p>
                        @if(isset($deal->expected_close_date) && $deal->expected_close_date)
                        <p class="mb-2"><i class="bi bi-calendar me-2 text-muted"></i>Expected Close: {{ \Carbon\Carbon::parse($deal->expected_close_date)->format('M d, Y') }}</p>
                        @endif
                        <p class="mb-2">
                            <i class="bi bi-calculator me-2 text-muted"></i>Weighted Value:
                            <strong>${{ number_format((($deal->value ?? 0) * ($deal->probability ?? 0)) / 100, 2) }}</strong>
                        </p>
                    </div>
                </div>
                @if(isset($deal->description) && $deal->description)
                <hr>
                <h6>Description</h6>
                <p class="text-muted mb-0">{{ $deal->description }}</p>
                @endif
            </div>
        </div>

        <!-- Pipeline Progress -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Pipeline Progress</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    @foreach($stages as $stage)
                    @php $stage = (object) $stage; @endphp
                    <div class="text-center flex-fill position-relative">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px; background-color: {{ $stage->id <= ($deal->stage_id ?? 0) ? ($stage->color ?? '#6c757d') : '#e9ecef' }}; cursor: pointer;"
                             onclick="moveToStage({{ $stage->id }})"
                             title="Move to {{ $stage->name }}">
                            @if($stage->id < ($deal->stage_id ?? 0))
                            <i class="bi bi-check text-white"></i>
                            @elseif($stage->id == ($deal->stage_id ?? 0))
                            <i class="bi bi-circle-fill text-white" style="font-size: 0.5rem;"></i>
                            @endif
                        </div>
                        <small class="text-{{ $stage->id == ($deal->stage_id ?? 0) ? 'primary fw-bold' : 'muted' }}">{{ $stage->name }}</small>
                    </div>
                    @if(!$loop->last)
                    <div class="flex-shrink-0" style="width: 50px; height: 2px; background-color: {{ $stage->id < ($deal->stage_id ?? 0) ? '#198754' : '#e9ecef' }}; margin-top: -20px;"></div>
                    @endif
                    @endforeach
                </div>
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
                            $typeIcons = ['call' => 'telephone', 'email' => 'envelope', 'meeting' => 'calendar-event', 'note' => 'sticky', 'proposal' => 'file-text', 'negotiation' => 'chat-dots', 'stage_change' => 'arrow-repeat', 'other' => 'three-dots'];
                            $typeColors = ['call' => 'success', 'email' => 'primary', 'meeting' => 'warning', 'note' => 'info', 'proposal' => 'secondary', 'negotiation' => 'danger', 'stage_change' => 'warning', 'other' => 'dark'];
                        @endphp
                        <div class="bg-{{ $typeColors[$activity->type] ?? 'secondary' }} bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-{{ $typeIcons[$activity->type] ?? 'three-dots' }} text-{{ $typeColors[$activity->type] ?? 'secondary' }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $activity->subject }}</div>
                        @if(isset($activity->description) && $activity->description)
                        <p class="text-muted small mb-1">{{ $activity->description }}</p>
                        @endif
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y g:i A') }}
                            @if(isset($activity->outcome) && $activity->outcome)
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
                    <label class="form-label">Move to Stage</label>
                    <select class="form-select" id="stageSelect" onchange="moveToStage(this.value)">
                        @foreach($stages as $stage)
                        @php $stage = (object) $stage; @endphp
                        <option value="{{ $stage->id }}" {{ ($deal->stage_id ?? 0) == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Probability (%)</label>
                    <input type="number" class="form-control" id="probabilityInput" min="0" max="100" value="{{ $deal->probability ?? 0 }}" onchange="updateDeal('probability', this.value)">
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-success" onclick="markAsWon()">
                        <i class="bi bi-trophy me-1"></i> Mark as Won
                    </button>
                    <button class="btn btn-outline-danger" onclick="markAsLost()">
                        <i class="bi bi-x-circle me-1"></i> Mark as Lost
                    </button>
                </div>
            </div>
        </div>

        <!-- Deal Value -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Deal Value</h6>
            </div>
            <div class="card-body text-center">
                <div class="display-5 fw-bold text-success mb-2">
                    ${{ number_format($deal->value ?? 0, 0) }}
                </div>
                <div class="text-muted mb-3">
                    Weighted: ${{ number_format((($deal->value ?? 0) * ($deal->probability ?? 0)) / 100, 0) }}
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: {{ $deal->probability ?? 0 }}%"></div>
                </div>
                <small class="text-muted">{{ $deal->probability ?? 0 }}% probability</small>
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
                    {{ \Carbon\Carbon::parse($deal->created_at)->format('M d, Y g:i A') }}
                </p>
                @if(isset($deal->expected_close_date) && $deal->expected_close_date)
                <p class="mb-2 small">
                    <strong>Expected Close:</strong><br>
                    {{ \Carbon\Carbon::parse($deal->expected_close_date)->format('M d, Y') }}
                    @php
                        $daysUntil = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($deal->expected_close_date), false);
                    @endphp
                    @if($daysUntil > 0)
                    <span class="badge bg-info-subtle text-info">{{ $daysUntil }} days left</span>
                    @elseif($daysUntil < 0)
                    <span class="badge bg-danger-subtle text-danger">{{ abs($daysUntil) }} days overdue</span>
                    @else
                    <span class="badge bg-warning-subtle text-warning">Due today</span>
                    @endif
                </p>
                @endif
                @if(isset($deal->closed_at) && $deal->closed_at)
                <p class="mb-2 small">
                    <strong>Closed:</strong><br>
                    {{ \Carbon\Carbon::parse($deal->closed_at)->format('M d, Y g:i A') }}
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
                            <option value="proposal">Proposal</option>
                            <option value="negotiation">Negotiation</option>
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
                            <option value="positive">Positive</option>
                            <option value="negative">Negative</option>
                            <option value="follow_up">Follow Up Needed</option>
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

<!-- Edit Deal Modal -->
<div class="modal fade" id="editDealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Edit Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDealForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editTitle" value="{{ $deal->title ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editValue" value="{{ $deal->value ?? 0 }}" required step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Close Date</label>
                        <input type="date" class="form-control" id="editExpectedClose" value="{{ isset($deal->expected_close_date) && $deal->expected_close_date ? \Carbon\Carbon::parse($deal->expected_close_date)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" rows="3">{{ $deal->description ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';
const DEAL_ID = {{ $deal->id ?? 0 }};

async function updateDeal(field, value) {
    try {
        const response = await fetch(`${API_BASE}/admin/deals/${DEAL_ID}`, {
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

async function moveToStage(stageId) {
    try {
        const response = await fetch(`${API_BASE}/admin/deals/${DEAL_ID}/stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stage_id: stageId })
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to move deal');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

async function markAsWon() {
    if (!confirm('Mark this deal as won?')) return;

    try {
        const response = await fetch(`${API_BASE}/admin/deals/${DEAL_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: 'won',
                probability: 100,
                closed_at: new Date().toISOString()
            })
        });

        if (response.ok) {
            alert('Deal marked as won!');
            location.reload();
        } else {
            alert('Failed to update');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('An error occurred');
    }
}

async function markAsLost() {
    const reason = prompt('Reason for losing this deal (optional):');
    if (reason === null) return;

    try {
        const response = await fetch(`${API_BASE}/admin/deals/${DEAL_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: 'lost',
                probability: 0,
                closed_at: new Date().toISOString(),
                loss_reason: reason || null
            })
        });

        if (response.ok) {
            alert('Deal marked as lost');
            location.reload();
        } else {
            alert('Failed to update');
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
        const response = await fetch(`${API_BASE}/admin/deals/${DEAL_ID}/activities`, {
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

document.getElementById('editDealForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = {
        title: document.getElementById('editTitle').value,
        value: document.getElementById('editValue').value,
        expected_close_date: document.getElementById('editExpectedClose').value || null,
        description: document.getElementById('editDescription').value || null
    };

    try {
        const response = await fetch(`${API_BASE}/admin/deals/${DEAL_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to update deal');
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
