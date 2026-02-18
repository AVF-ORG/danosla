<?php

namespace App\Http\Controllers\Auth\Pages;

use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    public function __invoke(string $token)
    {
        return view('pages.auth.reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
        ]);
    }
}
