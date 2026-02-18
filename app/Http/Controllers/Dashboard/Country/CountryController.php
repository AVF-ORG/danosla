<?php

namespace App\Http\Controllers\Dashboard\Country;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of countries.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Trash filter
        if ($request->get('trash') == 1) {
            $query->onlyTrashed();
        }

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('iso2', 'like', "%{$search}%")
                  ->orWhere('international_code', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.es') LIKE ?", ["%{$search}%"]);
            });
        }

        $countries = $query->latest()->paginate(15);

        return view('pages.dashboard.countries.index', compact('countries'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.countries.create', compact('languages'));
    }

    /**
     * Store country.
     */
    public function store(Request $request)
    {
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'iso2' => ['required', 'string', 'size:2', 'unique:countries,iso2'],
            'international_code' => ['required', 'string', 'max:10'],
            'svg' => ['nullable', 'string'],
        ]);

        // Keep only active languages and remove empty values
        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        Country::create([
            'name' => $names,
            'iso2' => strtoupper($request->iso2),
            'international_code' => $request->international_code,
            'svg' => $request->svg,
        ]);

        return redirect()
            ->route('dashboard.countries.index')
            ->with('success', 'Country created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $country = Country::findOrFail($id);
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.countries.edit', compact('country', 'languages'));
    }

    /**
     * Update country.
     */
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'iso2' => ['required', 'string', 'size:2', 'unique:countries,iso2,' . $id],
            'international_code' => ['required', 'string', 'max:10'],
            'svg' => ['nullable', 'string'],
        ]);

        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        $country->update([
            'name' => $names,
            'iso2' => strtoupper($request->iso2),
            'international_code' => $request->international_code,
            'svg' => $request->svg,
        ]);

        return redirect()
            ->route('dashboard.countries.index')
            ->with('success', 'Country updated successfully.');
    }

    /**
     * Soft delete country.
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return redirect()
            ->route('dashboard.countries.index')
            ->with('success', 'Country deleted successfully.');
    }

    /**
     * Restore soft deleted country.
     */
    public function restore($id)
    {
        Country::withTrashed()->findOrFail($id)->restore();

        return redirect()
            ->route('dashboard.countries.index')
            ->with('success', 'Country restored successfully.');
    }
}
