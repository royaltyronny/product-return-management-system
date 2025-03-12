<?php

namespace App\Http\Controllers;

use App\Models\Shoe;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $shoe = Shoe::findOrFail($id);
        return view('products.show', compact('shoe'));
    }
}
