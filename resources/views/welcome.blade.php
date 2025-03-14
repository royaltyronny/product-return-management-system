@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row align-items-center justify-content-between" style="min-height: 100vh;">
        <!-- Welcome Section -->
        <div class="col-md-6 text-center">
            <h1 class="mb-4">Welcome to the Product Return Management System</h1>
            <p class="lead">Streamline your product return processes with our intuitive platform.</p>
        </div>
        
        <!-- Login Section -->
        <div class="col-md-5">
            <div class="card shadow-lg" style="background: #e0f2f1; border-radius: 12px;">
                <div class="card-header bg-transparent text-center">
                    <h3>{{ __('Login') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Redirect authenticated users -->
                    @if (Auth::check())
                        <script>window.location = "{{ route('home') }}";</script>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>

                        <!-- Submit and Links -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a class="btn btn-outline-primary" href="{{ route('register') }}">
                                    {{ __("Don't have an account? Sign up") }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
