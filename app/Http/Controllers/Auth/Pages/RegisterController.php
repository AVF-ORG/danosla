<?php

namespace App\Http\Controllers\Auth\Pages;

use App\Http\Controllers\Controller;

use App\Models\Sector;
use App\Models\Region;

class RegisterController extends Controller
{
    public function __invoke()
    {
        $sectors = Sector::where('is_active', true)->get();
        $regions = Region::with('countries')->get();

        return view('pages.auth.register', [
            'title' => 'Register',
            'sectors' => $sectors,
            'regions' => $regions,
        ]);
    }
}
