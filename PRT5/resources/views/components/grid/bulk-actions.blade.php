{{--
    Grid Bulk Actions Component

    Usage:
    @include('components.grid.bulk-actions', [
        'actions' => [
            ['action' => 'approve', 'label' => 'Approve', 'class' => 'btn-success', 'icon' => 'check'],
            ['action' => 'reject', 'label' => 'Reject', 'class' => 'btn-warning', 'icon' => 'x'],
            ['action' => 'delete', 'label' => 'Delete', 'class' => 'btn-danger', 'icon' => 'trash'],
        ],
        'bulkActionUrl' => route('admin.reviews.bulk-action'),
    ])
--}}

@props(['actions' => [], 'bulkActionUrl' => ''])

<div class="bulk-actions mb-3" id="bulkActions">
    <span class="me-3">
        <strong id="selectedCount">0</strong> selected
    </span>

    @foreach($actions as $action)
        <button type="button"
                class="btn btn-sm {{ $action['class'] ?? 'btn-outline-secondary' }}"
                onclick="handleBulkAction('{{ $action['action'] }}', '{{ $bulkActionUrl }}', tableControls)"
                data-bs-toggle="tooltip"
                title="{{ $action['label'] }}">
            @if(isset($action['icon']))
                <i class="bi bi-{{ $action['icon'] }}"></i>
            @endif
            {{ $action['label'] }}
        </button>
    @endforeach
</div>
