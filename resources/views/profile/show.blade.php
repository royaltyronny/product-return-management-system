@extends('layouts.app')

@section('title', 'Your Profile')

@section('header', 'Your Profile Details')

@section('content')
<div class="profile-details">
    <h1>Profile Details</h1>

    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>

    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
</div>
@endsection
