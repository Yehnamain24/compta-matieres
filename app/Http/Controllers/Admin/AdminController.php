<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import de la classe de base
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return view('Admin.dashboard', [
            'totalUsers' => User::count(),
            'pendingUsers' => User::where('is_approved', false)->count(),
            'pendingUsersList' => User::where('is_approved', false)->latest()->get(),
            'users' => User::latest()->get(),
            'totalItems' => \App\Models\Item::count(),
        ]);
    }

    public function approveUser(User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', 'Utilisateur approuvé.');
    }

    public function rejectUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'Inscription refusée et supprimée.');
    }

    public function users()
    {
        return view('Admin.users', [
            'users' => User::latest()->get(),
        ]);
    }

    /**
     * Enregistre un nouvel utilisateur (Administrateur uniquement).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'matricule' => 'required|string|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // On force le rôle Admin
        $validated['role'] = 'admin';
        // Le mot de passe est automatiquement hashé grâce au cast dans le modèle User
        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Administrateur créé avec succès.');
    }

    // La méthode updateRole a été SUPPRIMÉE car on ne change plus les rôles.

    public function settings()
    {
        return view('Admin.settings');
    }
}