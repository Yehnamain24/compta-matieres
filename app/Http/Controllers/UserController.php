<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Enregistre un nouvel utilisateur (Administrateur).
     */
    public function store(Request $request)
    {
        // 1. Validation des données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'matricule' => 'required|string|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. On force le rôle "admin"
        $validated['role'] = User::ROLE_ADMIN;

        // 3. Création de l'utilisateur
        User::create($validated);

        // 4. Redirection
        return redirect()->route('admin.users.index')->with('success', 'Administrateur créé avec succès.');
    }
}
