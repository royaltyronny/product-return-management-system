@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="text-center mt-5">Welcome to the Product Return Management System</h1>
            <p class="lead text-center">Streamline your product return processes with our intuitive platform.</p>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h2 class="mb-4">Sign Up</h2>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection