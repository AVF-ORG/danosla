<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Stats for the header
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', User::STATUS_ACTIVE)->count(),
            'pending' => User::where('status', User::STATUS_PENDING)->count(),
            'inactive' => User::where('status', User::STATUS_INACTIVE)->count(),
        ];

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(15);

        return view('pages.dashboard.users.index', compact('users', 'stats'));
    }

    /**
     * Display pending users.
     */
    public function pending(Request $request)
    {
        $query = User::where('status', User::STATUS_PENDING);

        // Stats specifically for pending context if needed
        $stats = [
            'pending_count' => User::where('status', User::STATUS_PENDING)->count(),
            'total_users' => User::count(),
        ];

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('pages.dashboard.users.pending', compact('users', 'stats'));
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with(['roles', 'permissions', 'country', 'sector'])->findOrFail($id);
        return view('pages.dashboard.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        $statuses = [
            User::STATUS_PENDING => 'Pending',
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_INACTIVE => 'Inactive',
        ];

        return view('pages.dashboard.users.edit', compact('user', 'roles', 'userRoles', 'statuses'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:pending,active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update($request->only('name', 'email', 'status'));

        if ($request->has('roles')) {
            $user->syncRoles(array_map('intval', $request->roles));
        } else {
            $user->syncRoles([]);
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Update the user status.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,active,inactive',
        ]);

        $user->update(['status' => $request->status]);

        return back()->with('success', 'User status updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if (auth()->id() == $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
