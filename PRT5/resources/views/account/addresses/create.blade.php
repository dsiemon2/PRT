@extends('layouts.app')

@section('title', 'Add Address')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.index') }}">My Account</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.addresses.index') }}">Addresses</a></li>
            <li class="breadcrumb-item active">Add Address</li>
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
            <h1 style="color: var(--prt-brown);"><i class="bi bi-plus-lg"></i> Add New Address</h1>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('account.addresses.store') }}" method="POST">
                        @csrf
                        @include('account.addresses.partials.form')
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
