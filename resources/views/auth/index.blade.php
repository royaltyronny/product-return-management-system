<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Your logic to fetch and display reports
        return view('reports.index'); // Ensure you have this view created
    }
}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

