<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationType;
use App\Models\ConservationArea;
use Illuminate\Http\Request;

class AccommodationTypeController extends Controller
{
    public const TYPES = ['chalet', 'suite', 'lodge', 'guesthouse', 'hostel', 'dormitory', 'camp', 'camping'];

    public function index()
    {
        $accommodationTypes = AccommodationType::with('conservationArea')
            ->orderBy('conservation_area_id')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.accommodation-types.index', compact('accommodationTypes'));
    }

    public function create()
    {
        $areas = ConservationArea::active()->get();
        $types = self::TYPES;

        return view('admin.accommodation-types.create', compact('areas', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        AccommodationType::create($validated);

        return redirect()->route('admin.accommodation-types.index')->with('success', 'Accommodation type created.');
    }

    public function edit(AccommodationType $accommodationType)
    {
        $areas = ConservationArea::active()->get();
        $types = self::TYPES;

        return view('admin.accommodation-types.edit', compact('accommodationType', 'areas', 'types'));
    }

    public function update(Request $request, AccommodationType $accommodationType)
    {
        $validated = $this->validated($request);
        $accommodationType->update($validated);

        return redirect()->route('admin.accommodation-types.index')->with('success', 'Accommodation type updated.');
    }

    public function destroy(AccommodationType $accommodationType)
    {
        if ($accommodationType->bookings()->exists()) {
            return back()->with('error', '"' . $accommodationType->name . '" has existing bookings and cannot be deleted.');
        }

        $accommodationType->delete();

        return redirect()->route('admin.accommodation-types.index')->with('success', 'Accommodation type deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'conservation_area_id' => 'required|exists:conservation_areas,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', self::TYPES),
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'total_units' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'price_per_night_foreigner' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $validated['amenities'] = collect(explode("\n", (string) $request->input('amenities')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return $validated;
    }
}
