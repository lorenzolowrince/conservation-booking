<?php

namespace App\Http\Controllers;

use App\Models\ConservationArea;

class ConservationAreaController extends Controller
{
    public function index()
    {
        $areas = ConservationArea::active()->get();
        return view('areas.index', compact('areas'));
    }

    public function show(string $slug)
    {
        $area = ConservationArea::where('slug', $slug)->where('is_active', true)
            ->with(['packages' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
                    'accommodationTypes' => fn($q) => $q->where('is_active', true)])
            ->firstOrFail();

        return view('areas.show', compact('area'));
    }
}
