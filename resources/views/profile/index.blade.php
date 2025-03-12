@extends('layouts.app')

@section('title', 'Your Profile')

@section('header', 'Your Profile Summary')

@section('content')
<div class="profile-summary">
    <h1>Profile Summary</h1>

    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>

    <a href="{{ route('profile.show') }}" class="btn btn-primary">View Profile</a>
    <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Edit Profile</a>
</div>
@endsection
