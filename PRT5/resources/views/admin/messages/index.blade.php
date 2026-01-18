@extends('layouts.admin')

@section('title', 'Messages')

@section('styles')
<style>
    .message-card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .message-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s; }
    .message-item:hover { background: #f8f9fa; }
    .message-item.active { background: #e3f2fd; border-left: 3px solid var(--prt-red); }
    .message-item.unread { font-weight: 600; }
    .stat-card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .stat-value { font-size: 1.8rem; font-weight: bold; color: var(--prt-brown); }
    .message-detail { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-envelope"></i> Messages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Messages</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card text-center">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="text-muted">Total</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card text-center">
                <div class="stat-value text-primary">{{ $stats['unread'] }}</div>
                <div class="text-muted">Unread</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card text-center">
                <div class="stat-value text-success">{{ $stats['replied'] }}</div>
                <div class="text-muted">Replied</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card text-center">
                <div class="stat-value text-secondary">{{ $stats['archived'] }}</div>
                <div class="text-muted">Archived</div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Message List --}}
        <div class="col-lg-5 mb-4">
            <div class="message-card">
                {{-- Filters --}}
                <div class="p-3 border-bottom">
                    <form method="GET" class="row g-2">
                        @if($selectedMessage)
                            <input type="hidden" name="id" value="{{ $selectedMessage->id }}">
                        @endif
                        <div class="col-md-5">
                            <select name="status" class="form-select form-select-sm">
                                <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Messages</option>
                                <option value="unread" {{ $filters['status'] == 'unread' ? 'selected' : '' }}>Unread</option>
                                <option value="read" {{ $filters['status'] == 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $filters['status'] == 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="archived" {{ $filters['status'] == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ $filters['search'] }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100" data-bs-toggle="tooltip" title="Search messages"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>

                {{-- Message List --}}
                <div style="max-height: 600px; overflow-y: auto;">
                    @forelse($messages as $msg)
                        <a href="?id={{ $msg->id }}&status={{ $filters['status'] }}&search={{ urlencode($filters['search']) }}" class="text-decoration-none">
                            <div class="message-item {{ $msg->status == 'unread' ? 'unread' : '' }} {{ $selectedMessage && $selectedMessage->id == $msg->id ? 'active' : '' }}">
                                <div class="d-flex justify-content-between">
                                    <span class="text-dark">{{ $msg->name }}</span>
                                    <small class="text-muted">{{ $msg->created_at->format('M d') }}</small>
                                </div>
                                <div class="text-dark small">{{ Str::limit($msg->subject, 50) }}</div>
                                <div class="text-muted small">{{ Str::limit($msg->message, 60) }}...</div>
                                <div class="mt-1">
                                    @php
                                        $badgeClass = match($msg->status) {
                                            'unread' => 'bg-primary',
                                            'read' => 'bg-secondary',
                                            'replied' => 'bg-success',
                                            'archived' => 'bg-dark',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} small">{{ ucfirst($msg->status) }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">No messages found</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($messages->hasPages())
                    <div class="p-3 border-top">
                        <nav>
                            <ul class="pagination pagination-sm mb-0 justify-content-center">
                                <li class="page-item {{ $messages->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $messages->previousPageUrl() }}&status={{ $filters['status'] }}&search={{ urlencode($filters['search']) }}{{ $selectedMessage ? '&id='.$selectedMessage->id : '' }}" data-bs-toggle="tooltip" title="Previous page">Prev</a>
                                </li>
                                <li class="page-item disabled"><span class="page-link">{{ $messages->currentPage() }} / {{ $messages->lastPage() }}</span></li>
                                <li class="page-item {{ !$messages->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $messages->nextPageUrl() }}&status={{ $filters['status'] }}&search={{ urlencode($filters['search']) }}{{ $selectedMessage ? '&id='.$selectedMessage->id : '' }}" data-bs-toggle="tooltip" title="Next page">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>

        {{-- Message Detail --}}
        <div class="col-lg-7">
            @if($selectedMessage)
                <div class="message-detail">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="mb-1">{{ $selectedMessage->subject ?? '(No subject)' }}</h4>
                            <div class="text-muted">
                                From: <strong>{{ $selectedMessage->name }}</strong>
                                &lt;{{ $selectedMessage->email }}&gt;
                            </div>
                            <small class="text-muted">
                                {{ $selectedMessage->created_at->format('F d, Y \a\t g:i A') }}
                            </small>
                        </div>
                        <div>
                            @php
                                $badgeClass = match($selectedMessage->status) {
                                    'unread' => 'bg-primary',
                                    'read' => 'bg-secondary',
                                    'replied' => 'bg-success',
                                    'archived' => 'bg-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($selectedMessage->status) }}</span>
                        </div>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded">
                        {!! nl2br(e($selectedMessage->message)) !!}
                    </div>

                    {{-- Actions --}}
                    <div class="mb-4">
                        <a href="mailto:{{ $selectedMessage->email }}?subject=Re: {{ urlencode($selectedMessage->subject) }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Open your email client to reply to this message">
                            <i class="bi bi-reply"></i> Reply via Email
                        </a>

                        @if($selectedMessage->status != 'replied')
                            <form method="POST" action="{{ route('admin.messages.update', $selectedMessage) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="replied">
                                <input type="hidden" name="admin_notes" value="{{ $selectedMessage->admin_notes }}">
                                <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" title="Mark this message as replied"><i class="bi bi-check"></i> Mark Replied</button>
                            </form>
                        @endif

                        @if($selectedMessage->status != 'archived')
                            <form method="POST" action="{{ route('admin.messages.update', $selectedMessage) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="archived">
                                <input type="hidden" name="admin_notes" value="{{ $selectedMessage->admin_notes }}">
                                <button type="submit" class="btn btn-secondary" data-bs-toggle="tooltip" title="Move this message to archive"><i class="bi bi-archive"></i> Archive</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.messages.destroy', $selectedMessage) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this message permanently?');" data-bs-toggle="tooltip" title="Permanently delete this message">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>

                    {{-- Admin Notes --}}
                    <div class="border-top pt-4">
                        <h5><i class="bi bi-sticky"></i> Admin Notes</h5>
                        <form method="POST" action="{{ route('admin.messages.update', $selectedMessage) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $selectedMessage->status }}">
                            <textarea name="admin_notes" class="form-control mb-2" rows="3" placeholder="Add internal notes about this message...">{{ $selectedMessage->admin_notes }}</textarea>
                            <button type="submit" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Save admin notes for this message"><i class="bi bi-save"></i> Save Notes</button>
                        </form>
                    </div>

                    {{-- Meta Info --}}
                    <div class="border-top pt-4 mt-4">
                        <small class="text-muted">
                            @if($selectedMessage->user_id)
                                <i class="bi bi-person-check"></i> Sent by registered user (ID: {{ $selectedMessage->user_id }})<br>
                            @endif
                            @if($selectedMessage->ip_address)
                                <i class="bi bi-globe"></i> IP: {{ $selectedMessage->ip_address }}<br>
                            @endif
                            @if($selectedMessage->read_at)
                                <i class="bi bi-eye"></i> Read: {{ $selectedMessage->read_at->format('M d, Y g:i A') }}<br>
                            @endif
                            @if($selectedMessage->replied_at)
                                <i class="bi bi-reply"></i> Replied: {{ $selectedMessage->replied_at->format('M d, Y g:i A') }}<br>
                            @endif
                        </small>
                    </div>
                </div>
            @else
                <div class="message-detail text-center py-5">
                    <i class="bi bi-envelope-open" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">Select a message to view</h5>
                    <p class="text-muted">Click on a message from the list to view its contents</p>
                </div>
            @endif
        </div>
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
</script>
@endpush
