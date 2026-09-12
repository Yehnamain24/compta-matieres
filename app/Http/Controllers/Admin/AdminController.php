<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('Admin.dashboard', [
            'totalUsers' => \App\Models\User::count(),
            'pendingUsers' => \App\Models\User::where('is_approved', false)->count(),
            'pendingUsersList' => \App\Models\User::where('is_approved', false)->latest()->get(),
            'users' => \App\Models\User::latest()->get(),
            'totalItems' => \App\Models\Item::count(),
        ]);
    }

    public function approveUser(\App\Models\User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', 'Utilisateur approuvé.');
    }

    public function rejectUser(\App\Models\User $user)
    {
        $user->delete();
        return back()->with('success', 'Inscription refusée et supprimée.');
    }

    public function users()
    {
        return view('Admin.users', [
            'users' => \App\Models\User::latest()->get(),
        ]);
    }

    public function updateRole(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,comptable_matieres',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Rôle mis à jour pour ' . $user->name . '.');
    }

    public function settings()
    {
        return view('Admin.settings');
    }
}