<?php

namespace App\Http\Controllers;

use App\Models\Shoe;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Ensure the controller is accessible only to authenticated users
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show all shoes on the home page
    public function index()
    {
        // Fetch all shoes from the database
        $shoes = Shoe::all();

        // Return the view and pass the shoes data to it
        return view('home', compact('shoes')); // Pass shoes to the view
    }
}
