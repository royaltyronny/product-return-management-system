<?php

namespace App\Http\Controllers;

use App\Models\{ReturnRequest, Shoe, PickupStation};
use Illuminate\Http\{Request};
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
        $returnRequests = ReturnRequest::where('user_id', Auth::id())->paginate(10);
        return view('returns.index', compact('returnRequests'));
    }

    // Show the return form
    public function create(Shoe $shoe)
    {
        if (!$shoe->can_be_returned) {
            return redirect()->route('products.show', $shoe->id)
                ->with('error', 'This item cannot be returned.');
        }

        // Fetch pickup stations dynamically (optional)
        $pickupStations = PickupStation::pluck('name')->toArray();

        return view('returns.create', compact('shoe', 'pickupStations'));
    }

    // Store the return request
    public function store(Request $request, Shoe $shoe)
    {
        if (!$shoe->can_be_returned) {
            return redirect()->route('products.show', $shoe->id)
                ->with('error', 'This item cannot be returned.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|in:Defective,Wrong Size,Wrong Product,Other|max:255',
            'image_evidence' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pickup_station' => 'required|string|max:100',
            'refund_method' => 'required|string|in:Store Credit,Original Payment Method|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_evidence')) {
            $imagePath = $request->file('image_evidence')->store('returns', 'public');
        }

        try {
            ReturnRequest::create([
                'shoe_id' => $shoe->id,
                'user_id' => Auth::id(),
                'reason' => $validated['reason'],
                'image_path' => $imagePath,
                'pickup_station' => $validated['pickup_station'],
                'refund_method' => $validated['refund_method'],
                'status' => 'pending',
            ]);

            $shoe->update(['status' => 'returned']);

            return redirect()->route('returns.index')
                ->with('success', 'Return request created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create return request: ' . $e->getMessage());
            return redirect()->route('returns.index')
                ->with('error', 'Failed to create return request. Please try again.');
        }
    }
}
