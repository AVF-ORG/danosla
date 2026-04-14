<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index($tab = 'profile')
    {
        return view('pages.user.profile.index', [
            'tab' => $tab,
            'user' => auth()->user(),
            'countries' => \App\Models\Country::all(),
        ]);
    }


    public function update(Request $request, User $user)
    {
        // Ensure user can only update their own profile
        if (auth()->id() !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_number' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
        ]);

        $user->update($data);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Profile updated successfully.');
    }

}

