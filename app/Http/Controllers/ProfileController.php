<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the user's profile index page.
     */
    public function index()
    {
        return view('profile.index'); // Display user's profile summary page
    }

    /**
     * Show the user's profile details.
     */
    public function show()
    {
        // Fetch user data
        $user = Auth::user();

        return view('profile.show', compact('user')); // Pass user data to the view
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        // Fetch user data
        $user = Auth::user();

        return view('profile.edit', compact('user')); // Pass user data to the view
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|confirmed|min:6', // Password field (optional, confirm if present)
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'pickup_station' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.edit')
                             ->withErrors($validator)
                             ->withInput();
        }

        // Update user profile
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->location = $request->input('location');
        $user->country = $request->input('country');
        $user->city = $request->input('city');
        $user->address = $request->input('address');
        $user->phone_number = $request->input('phone_number');
        $user->pickup_station = $request->input('pickup_station');
        $user->id_number = $request->input('id_number');

        // If password is provided, update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save(); // Save the updated data

        return redirect()->route('profile.view')
                         ->with('success', 'Profile updated successfully');
    }
    
    /**
     * Show the user's order history.
     */
    public function orders(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Get the user's orders with pagination
        $orders = $user->orders()
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('profile.orders', compact('orders'));
    }
}
