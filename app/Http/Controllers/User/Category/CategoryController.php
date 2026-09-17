<?php

namespace App\Http\Controllers\User\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories de l'utilisateur connecté.
     */
    public function show()
    {
        $user = Auth::user();
        return view('User\Category\index', [
            'categories' => Category::with(['items'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * Enregistre une nouvelle catégorie.
     */
    public function save(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unicité limitée aux catégories de cet utilisateur
                Rule::unique('categories', 'name')->where('user_id', $user->id),
            ],
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Cette catégorie existe déjà dans votre liste.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ]);

        Category::create([
            'name' => $data['name'],
            'user_id' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Catégorie ajoutée avec succès !');
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, mixed $id)
    {
        $user = Auth::user();
        $category = Category::where('id', $id)
            ->where('user_id', $user->id) // Sécurité : on ne modifie que ses propres catégories
            ->firstOrFail();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unicité en ignorant la catégorie actuelle
                Rule::unique('categories', 'name')
                    ->where('user_id', $user->id)
                    ->ignore($category->id),
            ],
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Cette catégorie existe déjà dans votre liste.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ]);

        $category->update($data);

        return redirect()->back()->with('success', 'Catégorie mise à jour avec succès !');
    }

    /**
     * Supprime une catégorie.
     */
    public function delete(mixed $id)
    {
        $user = Auth::user();
        $category = Category::where('id', $id)
            ->where('user_id', $user->id) // Sécurité : on ne supprime que ses propres catégories
            ->firstOrFail();

        $category->delete();

        return back()->with('success', 'Suppression réussie !');
    }
}