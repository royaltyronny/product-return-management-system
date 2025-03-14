<?php

namespace App\Http\Controllers;

use App\Models\Shoe;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Show a single shoe's details
    public function show($id)
    {
        $shoe = Shoe::findOrFail($id);
    
        // Add logic to check if the shoe can be returned
        if (!$shoe->can_be_returned) {
            // You can pass a flag to the view to indicate that the item can't be returned
            return view('products.show', compact('shoe'))->with('cannot_return', true);
        }
    
        return view('products.show', compact('shoe'));
    }
    

    // Display all shoes and randomize return status
    public function index()
    {
        // Retrieve all shoes and randomize whether they can be returned
        $shoes = Shoe::all()->map(function ($shoe) {
            // Randomize returnable status, 50% chance to be returnable
            $shoe->can_return = rand(0, 1) === 1; // Random true/false
            return $shoe;
        });

        // Pass the shoes to the view
        return view('products.index', compact('shoes'));
    }
}
