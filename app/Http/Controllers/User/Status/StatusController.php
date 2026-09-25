<?php

namespace App\Http\Controllers\User\Status;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Affiche la liste des statuts.
     */
    public function show()
    {
        return view('User.Status.index', [
            'statuses' => Status::with('items')->orderByDesc('created_at')->get(),
        ]);
    }

    /**
     * Enregistre un nouveau statut.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',  // Plus de 'unique'
    ]);

    Status::create($validated);

    return redirect()->route('user.status.show')->with('success', 'Statut ajouté avec succès.');
    }

    /**
     * Met à jour un statut existant.
     */
    public function update(Request $request, Status $status)
    {
        // Validation avec unicité (en ignorant le statut actuel)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Le nom du statut est obligatoire.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ]);

        $status->update($validated);

        return redirect()->route('user.status.show')->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Supprime un statut.
     */
    public function destroy(Status $status)
    {
        $status->delete();

        return redirect()->route('user.status.show')->with('success', 'Statut supprimé avec succès.');
    }
}