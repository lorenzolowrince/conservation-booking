<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConservationArea;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AreaController extends Controller
{
    public function index()
    {
        $areas = ConservationArea::withCount(['bookings', 'packages', 'accommodationTypes'])
            ->orderBy('sort_order')
            ->get();
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:conservation_areas,code',
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'description' => 'required|string',
            'about' => 'nullable|string',
            'location' => 'required|string|max:255',
            'area_hectares' => 'nullable|integer|min:0',
            'best_time_to_visit' => 'nullable|string|max:100',
            'difficulty_level' => 'required|in:easy,moderate,challenging',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug uniqueness
        $slug = $validated['slug'];
        $count = 1;
        while (ConservationArea::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $slug . '-' . $count++;
        }

        $area = ConservationArea::create($validated);

        return redirect()->route('admin.areas.show', $area)
            ->with('success', 'Conservation area created successfully.');
    }

    public function show(ConservationArea $area)
    {
        $area->load(['packages', 'accommodationTypes', 'bookings' => fn($q) => $q->latest()->take(5)]);
        return view('admin.areas.show', compact('area'));
    }

    public function edit(ConservationArea $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    public function update(Request $request, ConservationArea $area)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'about' => 'nullable|string',
            'location' => 'required|string|max:255',
            'area_hectares' => 'nullable|integer|min:0',
            'best_time_to_visit' => 'nullable|string|max:100',
            'difficulty_level' => 'required|in:easy,moderate,challenging',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $area->update($validated);

        return redirect()->route('admin.areas.show', $area)
            ->with('success', 'Conservation area updated.');
    }

    public function destroy(ConservationArea $area)
    {
        if ($area->bookings()->exists()) {
            return back()->with('error', 'Cannot delete "' . $area->short_name . '" — it has existing bookings.');
        }

        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', '"' . $area->short_name . '" has been deleted.');
    }
}
