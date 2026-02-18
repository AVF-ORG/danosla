<?php

namespace App\Http\Controllers\Auth\Actions;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterAction extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required', 
                'string', 
                'min:8', 
                'max:20', 
                'confirmed',
                'regex:/[a-z]/',      // at least one lowercase letter
                'regex:/[A-Z]/',      // at least one uppercase letter
                'regex:/[0-9]/',      // at least one digit
                'regex:/[@#\$%\^&\*\+]/', // at least one special character
            ],
            'country_id' => ['required', 'exists:countries,id'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'sector_id' => ['required', 'exists:sectors,id'],
            'website' => ['nullable', 'url', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_number' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'country_id' => $data['country_id'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'sector_id' => $data['sector_id'],
            'website' => $data['website'] ?? null,
            'company_name' => $data['company_name'],
            'company_number' => $data['company_number'],
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
