<?php

namespace App\Http\Controllers;

use App\Models\Shoe;
use Illuminate\Http\Request;
use App\Models\ReturnModel;
use App\Models\ProductReturn;


class ReturnController extends Controller
{
    public function index()
{
    $returns = ReturnModel::all(); // Or any other query to get returns
    return view('returns.return', compact('returns'));
}
public function store(Request $request)
{
    // Validate inputs
    $validated = $request->validate([
        'reason' => 'required|string|max:1000',
        'evidence' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
    ]);

    // Handle evidence upload
    $evidencePath = null;
    if ($request->hasFile('evidence')) {
        $evidencePath = $request->file('evidence')->store('evidence', 'public');
    }

    // Create the return record
    ProductReturn::create([
        'reason' => $validated['reason'],
        'evidence' => $evidencePath,
    ]);
    $returns = ProductReturn::all(); 

    // Redirect back with success message
    return view('returns.report',compact('returns'))->with('success', 'Return request submitted successfully.');
}
public function show()
{
    $returns = ProductReturn::all(); // Fetch all returns
    return view('returns.report', compact('returns'));
}

}