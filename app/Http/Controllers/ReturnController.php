<?php

namespace App\Http\Controllers;

use App\Models\{ReturnRequest, Shoe};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, Log, Auth};

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show all return requests
    public function index()
    {
        $returnRequests = ReturnRequest::paginate(10);
        return view('manage-returns', compact('returnRequests'));
    }

    // Create a return request for a shoe
    public function create(Shoe $shoe, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|in:Defective,Wrong Size,Wrong Product,Other|max:255',
            'image_evidence' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_evidence')) {
            $imagePath = $request->file('image_evidence')->store('returns', 'public');
        }

        try {
            $returnRequest = ReturnRequest::create([
                'shoe_id' => $shoe->id,
                'reason' => $request->reason,
                'image_path' => $imagePath,
                'status' => 'pending',
                'user_id' => Auth::id(),
            ]);

            // Optionally mark the shoe as returned
            $shoe->update(['status' => 'returned']);

            return redirect()->route('returns.index')->with('success', 'Return request created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create return request: ' . $e->getMessage());
            return redirect()->route('returns.index')->with('error', 'Failed to create return request. Please try again.');
        }
    }

    // Delete a return request
    public function destroy($id)
    {
        $returnRequest = ReturnRequest::findOrFail($id);
        if ($returnRequest->image_path) {
            Storage::disk('public')->delete($returnRequest->image_path);
        }
        $returnRequest->delete();

        return redirect()->route('returns.index')->with('success', 'Return request deleted successfully');
    }
}
