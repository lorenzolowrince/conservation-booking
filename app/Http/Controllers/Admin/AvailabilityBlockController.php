<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationType;
use App\Models\ActivityLog;
use App\Models\AvailabilityBlock;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AvailabilityBlockController extends Controller
{
    public function index()
    {
        $blocks = AvailabilityBlock::with(['package.conservationArea', 'accommodationType.conservationArea', 'creator'])
            ->orderByDesc('start_date')
            ->paginate(20);

        return view('admin.blocked-dates.index', compact('blocks'));
    }

    public function create()
    {
        $packages = Package::with('conservationArea')->orderBy('name')->get();
        $accommodationTypes = AccommodationType::with('conservationArea')->orderBy('name')->get();

        return view('admin.blocked-dates.create', compact('packages', 'accommodationTypes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['created_by'] = $request->user()->id;

        $block = AvailabilityBlock::create($validated);

        ActivityLog::record(
            ActivityLog::ACTION_AVAILABILITY_BLOCKED,
            $block,
            'Dates blocked for ' . ($block->package?->name ?? $block->accommodationType?->name) . " ({$block->start_date->format('d M Y')} – {$block->end_date->format('d M Y')}).",
            reason: $block->reason,
        );

        return redirect()->route('admin.blocked-dates.index')->with('success', 'Dates blocked.');
    }

    public function edit(AvailabilityBlock $blockedDate)
    {
        $packages = Package::with('conservationArea')->orderBy('name')->get();
        $accommodationTypes = AccommodationType::with('conservationArea')->orderBy('name')->get();

        return view('admin.blocked-dates.edit', ['block' => $blockedDate, 'packages' => $packages, 'accommodationTypes' => $accommodationTypes]);
    }

    public function update(Request $request, AvailabilityBlock $blockedDate)
    {
        $validated = $this->validated($request);

        $blockedDate->update($validated);

        return redirect()->route('admin.blocked-dates.index')->with('success', 'Block updated.');
    }

    public function destroy(AvailabilityBlock $blockedDate)
    {
        $description = 'Block removed for ' . ($blockedDate->package?->name ?? $blockedDate->accommodationType?->name)
            . " ({$blockedDate->start_date->format('d M Y')} – {$blockedDate->end_date->format('d M Y')}).";

        // Log before deleting: subject_type/subject_id are stored either
        // way, but this keeps the timeline consistent with how other
        // actions are logged (subject captured while it still exists).
        ActivityLog::record(ActivityLog::ACTION_AVAILABILITY_UNBLOCKED, $blockedDate, $description);

        $blockedDate->delete();

        return redirect()->route('admin.blocked-dates.index')->with('success', 'Block removed — those dates are open again.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'target' => 'required|in:package,accommodation',
            'package_id' => 'nullable|exists:packages,id',
            'accommodation_type_id' => 'nullable|exists:accommodation_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ]);

        if ($validated['target'] === 'package') {
            if (empty($validated['package_id'])) {
                throw ValidationException::withMessages(['package_id' => 'Select which package to block.']);
            }
            $validated['accommodation_type_id'] = null;
        } else {
            if (empty($validated['accommodation_type_id'])) {
                throw ValidationException::withMessages(['accommodation_type_id' => 'Select which accommodation to block.']);
            }
            $validated['package_id'] = null;
        }

        unset($validated['target']);

        return $validated;
    }
}
