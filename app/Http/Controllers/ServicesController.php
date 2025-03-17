<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function services(): View
    {
        return view('layouts.services');
    }
}
