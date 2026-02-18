<?php

namespace App\Http\Controllers\Dashboard\Sector;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectorController extends Controller
{
    /**
     * Display a listing of sectors.
     */
    public function index(Request $request)
    {
        $query = Sector::query();

        // Trash filter
        if ($request->get('trash') == 1) {
            $query->onlyTrashed();
        }

        $sectors = $query->latest()->paginate(15);

        return view('pages.dashboard.sectors.index', compact('sectors'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.sectors.create', compact('languages'));
    }

    /**
     * Store sector.
     */
    public function store(Request $request)
    {
        // dd("hello");
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Keep only active languages and remove empty values
        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        // Generate slugs
        $slugs = [];
        foreach ($names as $code => $value) {
            $slugs[$code] = Str::slug($value);
        }

        Sector::create([
            'name' => $names,
            'slug' => $slugs,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('dashboard.sectors.index')
            ->with('success', 'Sector created successfully.');
    }

    /**
     * Show sector details.
     */
    public function show($id)
    {
        $sector = Sector::findOrFail($id);

        return view('pages.dashboard.sectors.show', compact('sector'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $sector = Sector::findOrFail($id);
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.sectors.edit', compact('sector', 'languages'));
    }

    /**
     * Update sector.
     */
    public function update(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        $slugs = [];
        foreach ($names as $code => $value) {
            $slugs[$code] = Str::slug($value);
        }

        $sector->update([
            'name' => $names,
            'slug' => $slugs,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('dashboard.sectors.index')
            ->with('success', 'Sector updated successfully.');
    }

    /**
     * Soft delete sector.
     */
    public function destroy($id)
    {
        $sector = Sector::findOrFail($id);
        $sector->delete();

        return redirect()
            ->route('dashboard.sectors.index')
            ->with('success', 'Sector deleted successfully.');
    }

    /**
     * Restore soft deleted sector.
     */
    public function restore($id)
    {
        Sector::withTrashed()->findOrFail($id)->restore();

        return redirect()
            ->route('dashboard.sectors.index')
            ->with('success', 'Sector restored successfully.');
    }

    /**
     * Permanently delete sector.
     */
    public function forceDelete($id)
    {
        Sector::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()
            ->route('dashboard.sectors.index')
            ->with('success', 'Sector permanently deleted.');
    }

    public function json(Sector $sector)
    {
        return response()->json([
            'id' => $sector->id,
            'is_active' => (bool) $sector->is_active,
            'name' => $sector->getTranslations('name') ?? [],
        ]);
    }
}
