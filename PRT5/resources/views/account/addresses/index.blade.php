@extends('layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.index') }}">My Account</a></li>
            <li class="breadcrumb-item active">Addresses</li>
        </ol>
    </nav>
</div>

<div class="container my-4">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-3 mb-4">
            @include('account.partials.sidebar', ['active' => 'addresses'])
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 style="color: var(--prt-brown);"><i class="bi bi-geo-alt"></i> My Addresses</h1>
                <a href="{{ route('account.addresses.create') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Add a new shipping address">
                    <i class="bi bi-plus-lg"></i> Add New Address
                </a>
            </div>

            @if($addresses->count() > 0)
                <div class="row">
                    @foreach($addresses as $address)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>
                                        @if($address->is_default)
                                            <span class="badge bg-primary me-2">Default</span>
                                        @endif
                                        <strong>{{ $address->first_name }} {{ $address->last_name }}</strong>
                                    </span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" title="Address options">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('account.addresses.edit', $address) }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </li>
                                            @if(!$address->is_default)
                                                <li>
                                                    <form action="{{ route('account.addresses.default', $address) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-check-circle"></i> Set as Default
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('account.addresses.destroy', $address) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Delete this address?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <address class="mb-0">
                                        {{ $address->address_line1 }}<br>
                                        @if($address->address_line2)
                                            {{ $address->address_line2 }}<br>
                                        @endif
                                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                        @if($address->phone)
                                            <i class="bi bi-telephone"></i> {{ $address->phone }}
                                        @endif
                                    </address>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-geo-alt text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">No saved addresses</h3>
                        <p class="text-muted">Add an address for faster checkout.</p>
                        <a href="{{ route('account.addresses.create') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Add your first shipping address">
                            <i class="bi bi-plus-lg"></i> Add Address
                        </a>
                    </div>
                </div>
            @endif
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
