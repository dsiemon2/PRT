@extends('layouts.app')

@section('title', 'Login - Pecos River Traders')

@section('content')
<!-- Breadcrumb -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Login</li>
        </ol>
    </nav>
</div>

<!-- Main Content -->
<div class="container my-5" style="min-height: 60vh;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header text-center" style="background: var(--prt-brown); color: white;">
                    <h4 class="mb-0"><i class="bi bi-person-circle"></i> Login to Your Account</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Test Credentials Notice -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong><i class="bi bi-info-circle"></i> Test Account:</strong> Form is pre-filled with test credentials for easy testing.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        {{-- Email Address --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       placeholder="your@email.com"
                                       value="{{ old('email', 'test@pecosriver.com') }}"
                                       required
                                       autofocus>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Enter your password"
                                       value="Test1234"
                                       required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="remember_me"
                                   name="remember">
                            <label class="form-check-label" for="remember_me">
                                Remember me
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3" data-bs-toggle="tooltip" title="Sign in to your account">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>

                        <div class="text-center mb-3">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-muted small" data-bs-toggle="tooltip" title="Reset your password via email">
                                    Forgot your password?
                                </a>
                            @endif
                        </div>

                        <!-- Divider -->
                        <div class="position-relative mb-3">
                            <hr>
                            <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white text-muted small">
                                or continue with
                            </span>
                        </div>

                        <!-- Social Login Buttons -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="alert('Google login coming soon!')" data-bs-toggle="tooltip" title="Sign in with your Google account">
                                <img src="https://www.google.com/favicon.ico" alt="Google" width="16" height="16" class="me-2">
                                Continue with Google
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="alert('Facebook login coming soon!')" data-bs-toggle="tooltip" title="Sign in with your Facebook account">
                                <svg width="16" height="16" fill="#1877F2" class="me-2" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Continue with Facebook
                            </button>
                            <button type="button" class="btn btn-outline-dark" onclick="alert('Apple login coming soon!')" data-bs-toggle="tooltip" title="Sign in with your Apple ID">
                                <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 24 24">
                                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                                Continue with Apple
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0">Don't have an account? <a href="{{ route('register') }}" class="fw-bold" data-bs-toggle="tooltip" title="Create a new account">Create one now</a></p>
                </div>
            </div>

            <!-- Security Badge -->
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="bi bi-shield-check" style="color: var(--prt-green);"></i>
                    Your information is secure and encrypted
                </p>
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
