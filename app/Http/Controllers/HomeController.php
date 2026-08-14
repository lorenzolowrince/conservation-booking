<?php

namespace App\Http\Controllers;

use App\Models\ConservationArea;

class HomeController extends Controller
{
    public function index()
    {
        $areas = ConservationArea::active()->get();
        return view('home', compact('areas'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }
}
