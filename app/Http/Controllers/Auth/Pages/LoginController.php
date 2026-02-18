<?php

namespace App\Http\Controllers\Auth\Pages;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function __invoke()
    {
        return view('pages.auth.login', ['title' => 'Login']);
    }
}
