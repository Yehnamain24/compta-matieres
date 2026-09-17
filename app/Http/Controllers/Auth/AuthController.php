<?php

namespace App\Http\Controllers\Auth;

use App\Enum\User\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur.
     */
    public function register(Request $request)
    {
        // 1. Validation
        $validateData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'matricule' => ['required', 'string', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' vérifie password_confirmation
        ]);

        // 2. Création de l'utilisateur
        // Le mot de passe est hashé automatiquement grâce au cast 'hashed' dans le modèle User.
        User::create([
            'name' => $validateData['name'],
            'surname' => $validateData['surname'],
            'matricule' => $validateData['matricule'],
            'email' => $validateData['email'],
            'password' => $validateData['password'],
            'role' => UserRole::ADMIN->value, // ou 'admin' si vous n'utilisez pas l'Enum
            'is_approved' => true, // ✅ Approuvé directement (ou false pour approbation manuelle)
        ]);

        return redirect()->route('connexion')->with('success', 'Inscription réussie. Vous pouvez désormais vous connecter !');
    }

    /**
     * Connexion d'un utilisateur.
     */
    public function login(Request $request)
    {
        // 1. Validation des champs
        $validateData = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Tentative de connexion
        if (Auth::attempt($validateData)) {
            $user = Auth::user();

            // 3. Vérification du statut d'approbation AVANT toute redirection
            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est en attente d\'approbation par un administrateur.']);
            }

            // 4. Redirection selon le rôle
            if ($user->role === UserRole::ADMIN->value) {
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenue Administrateur !');
            }

            return redirect()->route('user.dashboard')->with('success', 'Bienvenue Utilisateur !');
        }

        // 5. Échec de connexion
        return redirect()->back()->withErrors(['email' => 'Email ou mot de passe incorrect !']);
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Sécurité : régénérer le token CSRF
        Auth::logout();

        return redirect()->route('connexion')->with('success', 'Vous êtes déconnecté !');
    }
}