{{--
    Grid Pagination Component

    Usage:
    @include('components.grid.pagination', [
        'paginator' => $items,
        'perPage' => $perPage,
    ])
--}}

@props(['paginator', 'perPage' => 20])

<div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    {{-- Showing info --}}
    <div class="text-muted small">
        @if($paginator->total() > 0)
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        @else
            No entries to display
        @endif
    </div>

    {{-- Pagination links --}}
    <nav>
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}">&laquo;</a>
            </li>

            {{-- Page numbers --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $startPage + 4);
                if ($endPage - $startPage < 4) {
                    $startPage = max(1, $endPage - 4);
                }
            @endphp

            @if($startPage > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($startPage > 2)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endif

            @for($i = $startPage; $i <= $endPage; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- Next --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}">&raquo;</a>
            </li>
        </ul>
    </nav>

    {{-- Per page selector --}}
    <select class="form-select form-select-sm" id="pageSize" style="width: auto;" onchange="changePerPage(this.value)">
        @foreach([10, 20, 50, 100] as $option)
            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                {{ $option }} / page
            </option>
        @endforeach
    </select>
</div>

<script>
function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
