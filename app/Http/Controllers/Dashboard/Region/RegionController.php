<?php

namespace App\Http\Controllers\Dashboard\Region;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of regions.
     */
    public function index(Request $request)
    {
        $query = Region::query();

        // Trash filter
        if ($request->get('trash') == 1) {
            $query->onlyTrashed();
        }

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.es') LIKE ?", ["%{$search}%"]);
            });
        }

        $regions = $query->latest()->paginate(15);

        return view('pages.dashboard.regions.index', compact('regions'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.regions.create', compact('languages'));
    }

    /**
     * Store region.
     */
    public function store(Request $request)
    {
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:regions,code'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
        ]);

        // Keep only active languages and remove empty values
        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        $descriptions = collect($request->input('description', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        Region::create([
            'name' => $names,
            'code' => strtoupper($request->code),
            'description' => $descriptions,
        ]);

        return redirect()
            ->route('dashboard.regions.index')
            ->with('success', 'Region created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $region = Region::findOrFail($id);
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.regions.edit', compact('region', 'languages'));
    }

    /**
     * Update region.
     */
    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:regions,code,' . $id],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
        ]);

        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        $descriptions = collect($request->input('description', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        $region->update([
            'name' => $names,
            'code' => strtoupper($request->code),
            'description' => $descriptions,
        ]);

        return redirect()
            ->route('dashboard.regions.index')
            ->with('success', 'Region updated successfully.');
    }

    /**
     * Soft delete region.
     */
    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete();

        return redirect()
            ->route('dashboard.regions.index')
            ->with('success', 'Region deleted successfully.');
    }

    /**
     * Restore soft deleted region.
     */
    public function restore($id)
    {
        Region::withTrashed()->findOrFail($id)->restore();

        return redirect()
            ->route('dashboard.regions.index')
            ->with('success', 'Region restored successfully.');
    }
}
