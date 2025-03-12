<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reports = Report::all();
        return view('reports.index', compact('reports'));
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        return view('reports.show', compact('report'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Report::create($request->only(['title', 'content']));

        return redirect()->route('reports.index')->with('success', 'Report created successfully.');
    }

    public function edit($id)
    {
        $report = Report::findOrFail($id);
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $report = Report::findOrFail($id);
        $report->update($request->only(['title', 'content']));

        return redirect()->route('reports.index')->with('success', 'Report updated successfully.');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Report deleted successfully.');
    }
}