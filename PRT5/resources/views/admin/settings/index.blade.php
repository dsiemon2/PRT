@extends('layouts.admin')

@section('title', 'Store Settings')

@section('content')
<div class="container-fluid my-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-gear"></i> Store Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                {{-- Store Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-shop"></i> Store Information</h5>
                    </div>
                    <div class="card-body">
                        @foreach($groups['store'] as $key => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <input type="text" name="settings[{{ $key }}]" class="form-control"
                                       value="{{ $settings[$key] ?? '' }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Email Settings --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-envelope"></i> Email Settings</h5>
                    </div>
                    <div class="card-body">
                        @foreach($groups['email'] as $key => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <input type="text" name="settings[{{ $key }}]" class="form-control"
                                       value="{{ $settings[$key] ?? '' }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Inventory Settings --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Inventory Settings</h5>
                    </div>
                    <div class="card-body">
                        @foreach($groups['inventory'] as $key => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                @if(str_contains($key, 'by_default'))
                                    <select name="settings[{{ $key }}]" class="form-select">
                                        <option value="1" {{ ($settings[$key] ?? '') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ ($settings[$key] ?? '') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                @else
                                    <input type="number" name="settings[{{ $key }}]" class="form-control"
                                           value="{{ $settings[$key] ?? '' }}" min="0">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Checkout Settings --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-cart-check"></i> Checkout Settings</h5>
                    </div>
                    <div class="card-body">
                        @foreach($groups['checkout'] as $key => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <input type="number" name="settings[{{ $key }}]" class="form-control" step="0.01"
                                       value="{{ $settings[$key] ?? '' }}" min="0">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Feature Toggles --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-toggles"></i> Features</h5>
                    </div>
                    <div class="card-body">
                        @foreach($groups['features'] as $key => $label)
                            <div class="form-check form-switch mb-3">
                                <input type="hidden" name="settings[{{ $key }}]" value="0">
                                <input type="checkbox" class="form-check-input" name="settings[{{ $key }}]"
                                       id="{{ $key }}" value="1"
                                       {{ ($settings[$key] ?? '') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check"></i> Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
