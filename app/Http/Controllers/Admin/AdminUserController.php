<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn($q) => $q->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
        }

        $users = $query->paginate(25)->withQueryString();

        return \Inertia\Inertia::render('Admin/Users', compact('users'));
    }

    public function toggleRole(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $user->role === 'admin' ? 'user' : 'admin']);

        return back()->with('success', "Role changed to {$user->role}.");
    }

    public function toggleSuspend(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend yourself.');
        }

        $user->update(['suspended_at' => $user->isSuspended() ? null : now()]);

        return back()->with('success', $user->isSuspended() ? 'User suspended.' : 'User unsuspended.');
    }
}
