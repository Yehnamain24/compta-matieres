<?php

namespace App\Http\Controllers\User\Movements;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MovementType;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementsController extends Controller
{
    /**
     * Affiche la liste des mouvements.
     */
    public function show()
    {
        $user = Auth::user();

        return view('User.Movements.index', [
            'movements' => StockMovement::with(['movementType', 'item', 'user'])
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get(),
            'movementTypes' => MovementType::latest()->get(),
            'items' => Item::where('user_id', $user->id)->latest()->get(),
        ]);
    }

    /**
     * Enregistre un nouveau mouvement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'movement_type_id' => 'required|exists:movement_types,id',
            'quantity' => 'required|integer|min:1',
            'movement_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        $movementType = MovementType::findOrFail($validated['movement_type_id']);

        $stockInitial = $item->quantity;
        $typeName = strtolower(str_replace(['é','è','ê'], 'e', $movementType->name));

        // Calcul du stock final selon le type
        if ($typeName === 'sortie') {
            if ($item->quantity < $validated['quantity']) {
                return back()->with('error', 'Stock insuffisant pour cette sortie.');
            }
            $stockFinal = $stockInitial - $validated['quantity'];
        } else {
            // Entrée et Retour
            $stockFinal = $stockInitial + $validated['quantity'];
        }

        // Mise à jour du stock du matériel
        $item->quantity = $stockFinal;
        $item->save();

        // Enregistrement du mouvement
        StockMovement::create([
            'item_id' => $validated['item_id'],
            'movement_type_id' => $validated['movement_type_id'],
            'user_id' => Auth::id(),
            'quantity' => $validated['quantity'],
            'stock_initial' => $stockInitial,
            'stock_final' => $stockFinal,
            'note' => $validated['note'] ?? null,
            'movement_date' => $validated['movement_date'] ?? now(),
        ]);

        return redirect()->route('user.movements.show')
            ->with('success', 'Mouvement enregistré avec succès.');
    }

    /**
     * Met à jour un mouvement existant.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'movement_type_id' => 'required|exists:movement_types,id',
            'quantity' => 'required|integer|min:1',
            'movement_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        $movement = StockMovement::findOrFail($id);
        $oldType = MovementType::findOrFail($movement->movement_type_id);
        $oldTypeName = strtolower(str_replace(['é','è','ê'], 'e', $oldType->name));

        // 1. Annuler l'ancien mouvement sur l'ancien item
        $oldItem = Item::findOrFail($movement->item_id);
        if ($oldTypeName === 'sortie') {
            $oldItem->quantity += $movement->quantity;
        } else {
            $oldItem->quantity -= $movement->quantity;
        }
        $oldItem->save();

        // 2. Appliquer le nouveau mouvement sur le nouvel item
        $newItem = Item::findOrFail($validated['item_id']);
        $newType = MovementType::findOrFail($validated['movement_type_id']);
        $newTypeName = strtolower(str_replace(['é','è','ê'], 'e', $newType->name));

        $stockInitial = $newItem->quantity;

        if ($newTypeName === 'sortie') {
            if ($newItem->quantity < $validated['quantity']) {
                return back()->with('error', 'Stock insuffisant pour cette sortie.');
            }
            $stockFinal = $stockInitial - $validated['quantity'];
        } else {
            $stockFinal = $stockInitial + $validated['quantity'];
        }

        $newItem->quantity = $stockFinal;
        $newItem->save();

        // 3. Mettre à jour le mouvement
        $movement->update([
            'item_id' => $validated['item_id'],
            'movement_type_id' => $validated['movement_type_id'],
            'quantity' => $validated['quantity'],
            'stock_initial' => $stockInitial,
            'stock_final' => $stockFinal,
            'note' => $validated['note'] ?? null,
            'movement_date' => $validated['movement_date'] ?? $movement->movement_date,
        ]);

        return redirect()->route('user.movements.show')
            ->with('success', 'Mouvement mis à jour avec succès.');
    }

    /**
     * Supprime un mouvement.
     */
    public function destroy($id)
    {
        $movement = StockMovement::findOrFail($id);
        $item = Item::findOrFail($movement->item_id);
        $type = MovementType::findOrFail($movement->movement_type_id);
        $typeName = strtolower(str_replace(['é','è','ê'], 'e', $type->name));

        // Annuler l'effet du mouvement sur le stock
        if ($typeName === 'sortie') {
            $item->quantity += $movement->quantity;
        } else {
            $item->quantity -= $movement->quantity;
        }
        $item->save();

        $movement->delete();

        return back()->with('success', 'Mouvement supprimé.');
    }
}