<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index($tab = 'profile')
    {
        return view('pages.user.profile.index', compact('tab'));
    }


    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $user->update($data);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Profile updated.');
    }

}

