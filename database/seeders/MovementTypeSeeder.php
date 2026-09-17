<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MovementType;

class MovementTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Supprimer les types existants (sans casser les clés étrangères)
        MovementType::query()->delete();

        $types = ['Entrée', 'Sortie', 'Retour'];

        foreach ($types as $name) {
            MovementType::create(['name' => $name]);
        }
    }
}