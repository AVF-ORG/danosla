<?php

namespace App\Http\Controllers\Auth\Pages;

use App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    public function __invoke()
    {
        return view('pages.auth.forgot-password', ['title' => 'Forgot Password']);
    }
}
