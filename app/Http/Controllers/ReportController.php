<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use Illuminate\Http\Request;
use App\Http\Controllers\ReturnReportController;

class ReturnReportController extends Controller
{
    // Display all return reports
    public function index()
    {
        $returns = ProductReturn::all(); // Fetch all returns
        return view('returns.report', compact('returns'));
    }
}
