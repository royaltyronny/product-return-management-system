@extends('layouts.app')

@section('title', 'Edit Profile')

@section('header', 'Edit Your Profile')

@section('content')
<div class="profile-edit">
    <h1>Edit Profile</h1>
    
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('POST')  <!-- Or use @method('PUT') depending on your route -->

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password">
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
@endsection
