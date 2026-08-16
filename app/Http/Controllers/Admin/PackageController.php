<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConservationArea;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('conservationArea')
            ->orderBy('conservation_area_id')
            ->orderBy('sort_order')
            ->paginate(20);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $areas = ConservationArea::active()->get();
        return view('admin.packages.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conservation_area_id' => 'required|exists:conservation_areas,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'min_pax' => 'required|integer|min:1',
            'max_pax' => 'nullable|integer|min:1',
            'daily_capacity' => 'nullable|integer|min:1',
            'price_per_person' => 'required|numeric|min:0',
            'price_per_person_foreigner' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        Package::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        $areas = ConservationArea::active()->get();
        return view('admin.packages.edit', compact('package', 'areas'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'min_pax' => 'required|integer|min:1',
            'max_pax' => 'nullable|integer|min:1',
            'daily_capacity' => 'nullable|integer|min:1',
            'price_per_person' => 'required|numeric|min:0',
            'price_per_person_foreigner' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }
}
