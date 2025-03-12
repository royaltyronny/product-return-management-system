@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Reports</h1>

        @if($reports->isEmpty())
            <p>No reports available.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Title</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->title }}</td>
                            <td>{{ $report->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
