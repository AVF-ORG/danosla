<?php

namespace App\Http\Controllers\Dashboard\RegionCountry;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionCountryController extends Controller
{
    /**
     * Display a listing of region-country relationships.
     */
    public function index(Request $request)
    {
        $query = Region::with('countries');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"]);
            });
        }

        $regions = $query->latest()->paginate(15);

        return view('pages.dashboard.region-countries.index', compact('regions'));
    }

    /**
     * Show form to assign countries to regions.
     */
    public function create()
    {
        $regions = Region::orderBy('code')->get();
        $countries = Country::orderBy('iso2')->get();

        return view('pages.dashboard.region-countries.create', compact('regions', 'countries'));
    }

    /**
     * Store region-country relationship.
     */
    public function store(Request $request)
    {
        $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'country_ids' => ['required', 'array'],
            'country_ids.*' => ['exists:countries,id'],
        ]);

        $region = Region::findOrFail($request->region_id);

        // Sync countries (this will add new ones and keep existing)
        $region->countries()->syncWithoutDetaching($request->country_ids);

        return redirect()
            ->route('dashboard.region-countries.index')
            ->with('success', 'Countries assigned to region successfully.');
    }

    /**
     * Remove region-country relationship.
     */
    public function destroy($regionId, $countryId)
    {
        $region = Region::findOrFail($regionId);
        $region->countries()->detach($countryId);

        return redirect()
            ->route('dashboard.region-countries.index')
            ->with('success', 'Country removed from region successfully.');
    }
}
