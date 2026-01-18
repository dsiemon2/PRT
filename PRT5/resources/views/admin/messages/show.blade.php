@extends('layouts.admin')

@section('title', 'View Message')

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-envelope-open"></i> View Message</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $message->subject ?? '(No subject)' }}</span>
                    @php
                        $colors = ['unread' => 'danger', 'read' => 'info', 'replied' => 'success', 'archived' => 'secondary'];
                    @endphp
                    <span class="badge bg-{{ $colors[$message->status] ?? 'secondary' }}">{{ ucfirst($message->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $message->name }}</strong><br>
                                <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                @if($message->phone)
                                    <br><small class="text-muted">{{ $message->phone }}</small>
                                @endif
                            </div>
                            <div class="text-end text-muted">
                                {{ $message->created_at->format('M j, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="message-content" style="white-space: pre-wrap;">{{ $message->message }}</div>
                </div>
            </div>

            {{-- Reply form --}}
            <div class="card mt-4">
                <div class="card-header">Reply</div>
                <div class="card-body">
                    <form action="mailto:{{ $message->email }}" method="get">
                        <input type="hidden" name="subject" value="Re: {{ $message->subject }}">
                        <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" title="Open your email client to reply to this message">
                            <i class="bi bi-reply"></i> Reply via Email
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Status Update --}}
            <div class="card mb-3">
                <div class="card-header">Update Status</div>
                <div class="card-body">
                    <form action="{{ route('admin.messages.update', $message) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="unread" {{ $message->status == 'unread' ? 'selected' : '' }}>Unread</option>
                                <option value="read" {{ $message->status == 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $message->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="archived" {{ $message->status == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add internal notes about this message...">{{ $message->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Save status and notes changes">
                            <i class="bi bi-check"></i> Update
                        </button>
                    </form>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">Actions</div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Return to messages list">
                        <i class="bi bi-arrow-left"></i> Back to Messages
                    </a>
                    <form action="{{ route('admin.messages.destroy', $message) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Delete this message?')" data-bs-toggle="tooltip" title="Permanently delete this message">
                            <i class="bi bi-trash"></i> Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
