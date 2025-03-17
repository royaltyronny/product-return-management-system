@extends('layouts.app')

@section('content')
<h1 class="text-center mb-4">Return Reports</h1>

@if($returns->isEmpty())
    <p class="text-center">No return requests found.</p>
@else
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Reason</th>
                <th>Evidence</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($returns as $return)
                <tr>
                    <td>{{ $return->id }}</td>
                    <td>{{ $return->reason }}</td>
                    <td>
                        @if ($return->evidence)
                            <a href="{{ Storage::url($return->evidence) }}" target="_blank">View Evidence</a>
                        @else
                            No Evidence
                        @endif
                    </td>
                    <td>{{ $return->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
