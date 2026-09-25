<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InvitationCode;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Tableau de bord admin.
     */
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

    /**
     * Approuver un utilisateur.
     */
    public function approveUser(User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', 'Utilisateur approuvé.');
    }

    /**
     * Refuser / supprimer un utilisateur.
     */
    public function rejectUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'Inscription refusée et supprimée.');
    }

    /**
     * Liste des utilisateurs.
     */
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

        return redirect()->route('admin.users.index')
            ->with('success', 'Administrateur créé avec succès.');
    }

    /**
     * Paramètres de l'application.
     */
    public function settings()
    {
        return view('Admin.settings');
    }

    /**
     * Génère un nouveau code d'invitation.
     */
    public function generateCode()
    {
        $code = strtoupper(Str::random(8)); // ex: A1B2C3D4

        InvitationCode::create([
            'code' => $code,
            'created_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        return back()->with('success', "Code généré : $code");
    }

    /**
     * Liste des codes d'invitation.
     */
    public function codes()
    {
        return view('Admin.codes', [
            'codes' => InvitationCode::with(['creator', 'usedBy'])->latest()->get(),
        ]);
    }
}