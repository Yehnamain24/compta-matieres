<?php

namespace App\Http\Controllers\Auth;

use App\Enum\User\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InvitationCode;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (via code d'invitation).
     */
    public function register(Request $request)
    {
        // 1. Validation
        $validateData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'matricule' => ['required', 'string', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invitation_code' => ['required', 'string', 'exists:invitation_codes,code'],
        ], [
            'invitation_code.required' => 'Le code d\'invitation est obligatoire.',
            'invitation_code.exists' => 'Ce code d\'invitation est invalide.',
        ]);

        // 2. Vérifier que le code n'est pas déjà utilisé
        $code = InvitationCode::where('code', $validateData['invitation_code'])->first();

        if ($code->used_by !== null) {
            return back()->withErrors(['invitation_code' => 'Ce code a déjà été utilisé.'])->withInput();
        }

        // 3. Vérifier que le code n'est pas expiré
        if ($code->expires_at && $code->expires_at->isPast()) {
            return back()->withErrors(['invitation_code' => 'Ce code a expiré.'])->withInput();
        }

        // 4. Création de l'utilisateur
        $user = User::create([
            'name' => $validateData['name'],
            'surname' => $validateData['surname'],
            'matricule' => $validateData['matricule'],
            'email' => $validateData['email'],
            'password' => $validateData['password'],
            'role' => UserRole::USER->value, // ✅ Rôle USER (pas admin)
            'is_approved' => true,            // Approuvé directement
        ]);

        // 5. Marquer le code comme utilisé
        $code->update([
            'used_by' => $user->id,
            'used_at' => now(),
        ]);

        return redirect()->route('connexion')->with('success', 'Inscription réussie. Vous pouvez désormais vous connecter !');
    }

    /**
     * Connexion d'un utilisateur.
     */
    public function login(Request $request)
    {
        $validateData = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($validateData)) {
            $user = Auth::user();

            // Vérification du statut d'approbation
            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est en attente d\'approbation par un administrateur.']);
            }

            // Redirection selon le rôle
            if ($user->role === UserRole::ADMIN->value) {
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenue Administrateur !');
            }

            return redirect()->route('user.dashboard')->with('success', 'Bienvenue Utilisateur !');
        }

        return redirect()->back()->withErrors(['email' => 'Email ou mot de passe incorrect !']);
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();

        return redirect()->route('connexion')->with('success', 'Vous êtes déconnecté !');
    }
}