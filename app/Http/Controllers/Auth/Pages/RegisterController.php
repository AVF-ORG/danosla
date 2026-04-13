<?php

namespace App\Http\Controllers\Auth\Pages;

use App\Http\Controllers\Controller;

use App\Models\Sector;
use App\Models\Region;

class RegisterController extends Controller
{
    public function __invoke()
    {
        return view('pages.auth.register', [
            'title' => 'Register',
        ]);
    }
}
