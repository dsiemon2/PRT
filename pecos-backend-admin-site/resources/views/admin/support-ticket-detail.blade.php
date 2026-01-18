@extends('layouts.admin')

@section('title', 'Ticket ' . ($ticket->ticket_number ?? ''))

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Ticket {{ $ticket->ticket_number ?? '' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.support') }}">Support Tickets</a></li>
                <li class="breadcrumb-item active">{{ $ticket->ticket_number ?? '' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button class="btn btn-outline-success" onclick="updateStatus('resolved')">
            <i class="bi bi-check-lg me-1"></i> Resolve
        </button>
        <button class="btn btn-outline-secondary" onclick="updateStatus('closed')">
            <i class="bi bi-x-lg me-1"></i> Close
        </button>
    </div>
</div>
@endsection

@section('content')
@if(!$ticket)
<div class="alert alert-danger">Ticket not found</div>
@else
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Ticket Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $ticket->subject }}</h5>
                    <div class="d-flex gap-2">
                        @php
                            $priorityColors = ['urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', 'low' => 'secondary'];
                            $statusColors = ['open' => 'warning', 'in_progress' => 'info', 'pending_customer' => 'secondary', 'resolved' => 'success', 'closed' => 'dark'];
                        @endphp
                        <span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">{{ ucfirst($ticket->priority) }}</span>
                        <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$ticket->status] ?? 'secondary' }}">
                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($ticket->category) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-muted small mb-3">
                    <div class="col-auto">
                        <i class="bi bi-calendar me-1"></i>
                        Created: {{ \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y g:i A') }}
                    </div>
                    @if($ticket->first_response_at)
                    <div class="col-auto">
                        <i class="bi bi-reply me-1"></i>
                        First Response: {{ \Carbon\Carbon::parse($ticket->first_response_at)->diffForHumans() }}
                    </div>
                    @endif
                    @if($ticket->resolved_at)
                    <div class="col-auto">
                        <i class="bi bi-check-circle me-1"></i>
                        Resolved: {{ \Carbon\Carbon::parse($ticket->resolved_at)->format('M d, Y') }}
                    </div>
                    @endif
                </div>

                @if($ticket->order_number)
                <div class="alert alert-info d-flex align-items-center py-2">
                    <i class="bi bi-box-seam me-2"></i>
                    Related Order: <a href="{{ route('admin.orders.detail', $ticket->order_id) }}" class="ms-1 fw-semibold">{{ $ticket->order_number }}</a>
                    @if($ticket->order_total)
                    <span class="ms-2 text-muted">({{ number_format($ticket->order_total, 2) }})</span>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Conversation</h6>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="messagesContainer">
                @forelse($messages as $message)
                <div class="d-flex mb-3 {{ $message->sender_type === 'staff' ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $message->sender_type === 'staff' ? 'bg-primary' : 'bg-secondary' }}"
                             style="width: 40px; height: 40px;">
                            <i class="bi bi-{{ $message->sender_type === 'staff' ? 'headset' : 'person' }} text-white"></i>
                        </div>
                    </div>
                    <div class="mx-3 {{ $message->sender_type === 'staff' ? 'text-end' : '' }}" style="max-width: 75%;">
                        <div class="rounded-3 p-3 {{ $message->sender_type === 'staff' ? 'bg-primary text-white' : 'bg-light' }} {{ $message->is_internal ? 'border border-warning' : '' }}">
                            @if($message->is_internal)
                            <small class="d-block text-warning mb-1"><i class="bi bi-lock me-1"></i>Internal Note</small>
                            @endif
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                        </div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($message->created_at)->format('M d, g:i A') }}
                        </small>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                    <p>No messages yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Reply Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-reply me-2"></i>Send Reply</h6>
            </div>
            <div class="card-body">
                <form id="replyForm">
                    @if(count($cannedResponses) > 0)
                    <div class="mb-3">
                        <label class="form-label">Quick Response</label>
                        <select class="form-select" id="cannedResponseSelect" onchange="insertCannedResponse()">
                            <option value="">-- Select a canned response --</option>
                            @foreach($cannedResponses as $response)
                            <option value="{{ $response->id }}" data-content="{{ $response->content }}">
                                {{ $response->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="mb-3">
                        <textarea class="form-control" id="replyMessage" rows="4" placeholder="Type your reply..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isInternal">
                            <label class="form-check-label" for="isInternal">
                                <i class="bi bi-lock me-1"></i> Internal note (not visible to customer)
                            </label>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary" onclick="submitReply('pending_customer')">
                                Reply & Set Pending
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Send Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Customer</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-person-fill text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $ticket->customer_first_name }} {{ $ticket->customer_last_name }}</h6>
                        <small class="text-muted">{{ $ticket->customer_email }}</small>
                    </div>
                </div>
                @if($ticket->customer_phone)
                <p class="mb-2">
                    <i class="bi bi-telephone me-2 text-muted"></i>
                    {{ $ticket->customer_phone }}
                </p>
                @endif
                <a href="{{ route('admin.customers.detail', $ticket->customer_id) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-person-badge me-1"></i> View Customer Profile
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Change Status</label>
                    <select class="form-select" id="statusSelect" onchange="updateStatus(this.value)">
                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="pending_customer" {{ $ticket->status == 'pending_customer' ? 'selected' : '' }}>Pending Customer</option>
                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Change Priority</label>
                    <select class="form-select" id="prioritySelect" onchange="updatePriority(this.value)">
                        <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Other Tickets from Customer -->
        @if(count($otherTickets) > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-collection me-2"></i>Other Tickets</h6>
            </div>
            <div class="list-group list-group-flush">
                @foreach($otherTickets as $other)
                <a href="{{ route('admin.support.detail', $other->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-medium">{{ $other->ticket_number }}</div>
                        <small class="text-muted">{{ Str::limit($other->subject, 30) }}</small>
                    </div>
                    <span class="badge bg-{{ $statusColors[$other->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$other->status] ?? 'secondary' }}">
                        {{ ucfirst($other->status) }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Satisfaction Rating -->
        @if($ticket->satisfaction_rating)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="bi bi-emoji-smile me-2"></i>Customer Feedback</h6>
            </div>
            <div class="card-body text-center">
                <div class="fs-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= $ticket->satisfaction_rating ? '-fill text-warning' : '' }}"></i>
                    @endfor
                </div>
                @if($ticket->satisfaction_comment)
                <p class="text-muted mb-0">"{{ $ticket->satisfaction_comment }}"</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const API_BASE = '{{ config("services.api.base_url") }}/api/v1';
const TICKET_ID = {{ $ticket->id ?? 0 }};

// Insert canned response
function insertCannedResponse() {
    const select = document.getElementById('cannedResponseSelect');
    const option = select.options[select.selectedIndex];
    const content = option.dataset.content;
    if (content) {
        document.getElementById('replyMessage').value = content;
    }
}

// Submit reply
document.getElementById('replyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitReply();
});

async function submitReply(newStatus = null) {
    const message = document.getElementById('replyMessage').value;
    const isInternal = document.getElementById('isInternal').checked;

    if (!message.trim()) {
        alert('Please enter a message');
        return;
    }

    try {
        // Add message
        const response = await fetch(`${API_BASE}/admin/support/tickets/${TICKET_ID}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                sender_type: 'staff',
                sender_id: 1,
                is_internal: isInternal
            })
        });

        if (response.ok) {
            // Update status if specified
            if (newStatus) {
                await updateStatus(newStatus);
            }
            showToast('Reply sent successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to send reply', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// Update status
async function updateStatus(status) {
    try {
        const response = await fetch(`${API_BASE}/admin/support/tickets/${TICKET_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });

        if (response.ok) {
            showToast('Status updated', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to update status', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// Update priority
async function updatePriority(priority) {
    try {
        const response = await fetch(`${API_BASE}/admin/support/tickets/${TICKET_ID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ priority: priority })
        });

        if (response.ok) {
            showToast('Priority updated', 'success');
        } else {
            showToast('Failed to update priority', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('An error occurred', 'error');
    }
}

// Scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>
@endpush
