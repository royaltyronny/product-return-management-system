@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Return Requests</h1>

    <!-- Display success and error messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form to create a new return request -->
    <form id="createReturnRequest" method="POST" action="{{ route('returns.create') }}">
        @csrf
        <div class="form-group">
            <label for="order_id">Order ID</label>
            <input type="number" class="form-control" id="order_id" name="order_id" required>
        </div>
        <div class="form-group">
            <label for="reason">Reason for Return</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Return Request</button>
    </form>

    <hr>

    <h2>Existing Return Requests</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($returnRequests as $returnRequest)
            <tr>
                <td>{{ $returnRequest->id }}</td>
                <td>{{ $returnRequest->order_id }}</td>
                <td>{{ $returnRequest->reason }}</td>
                <td>{{ ucfirst($returnRequest->status) }}</td>
                <td>
                    <a href="{{ route('returns.show', $returnRequest->id) }}" class="btn btn-info">View</a>
                    <form action="{{ route('returns.destroy', $returnRequest->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
