<?php

namespace App\Http\Controllers\User\Movements;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MovementType;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementsController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('User.Movements.index', [
            'movements' => StockMovement::with(['movementType', 'item', 'user'])->where('user_id', $user->id)->orderByDesc('created_at')->get(),
            'users' => User::latest('created_at')->get(),
            'movementTypes' => MovementType::latest()->get(),
            'items' => Item::latest()->get(),
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'item_id' => 'required|exists:items,id',
        'movement_type_id' => 'required|exists:movement_types,id',
        'quantity' => 'required|integer|min:1',
        'comment' => 'nullable|string|max:255',
    ]);

    $item = Item::findOrFail($validated['item_id']);
    $movementType = MovementType::findOrFail($validated['movement_type_id']);

    // Ajuste le stock selon le type de mouvement
    // Adapte les libellés ('entrée', 'sortie', 'retour') à ceux de ta table movement_types
    switch (strtolower($movementType->name)) {
        case 'entrée':
        case 'retour':
            $item->quantity += $validated['quantity'];
            break;
        case 'sortie':
            if ($item->quantity < $validated['quantity']) {
                return back()->with('error', 'Stock insuffisant pour cette sortie.');
            }
            $item->quantity -= $validated['quantity'];
            break;
    }
    $item->save();

    StockMovement::create([
        'item_id' => $validated['item_id'],
        'movement_type_id' => $validated['movement_type_id'],
        'user_id' => Auth::id(),
        'quantity' => $validated['quantity'],
        'comment' => $validated['comment'] ?? null,
    ]);

    return redirect()->route('user.movements.show')
        ->with('success', 'Mouvement enregistré avec succès.');
}

public function destroy($id)
{
    $movement = StockMovement::findOrFail($id);
    // Optionnel : recréditer/débiter le stock à l'annulation avant de supprimer
    $movement->delete();

    return back()->with('success', 'Mouvement supprimé.');
}
}
