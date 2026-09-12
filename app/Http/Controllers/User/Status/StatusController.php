<?php

namespace App\Http\Controllers\User\Status;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function show()
    {
        return view('User.Status.index', [
            'statuses' => Status::with('items')->orderByDesc('created_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Status::create($validated);

        return redirect()->route('user.status.show')->with('success', 'Statut ajouté avec succès.');
    }

    public function update(Request $request, Status $status)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $status->update($validated);

        return redirect()->route('user.status.show')->with('success', 'Statut mis à jour avec succès.');
    }

    public function destroy(Status $status)
    {
        $status->delete();

        return redirect()->route('user.status.show')->with('success', 'Statut supprimé avec succès.');
    }
}