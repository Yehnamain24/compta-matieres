<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('matricule')->unique(); // Ajout de unique() pour éviter les doublons
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // MODIFICATION ICI : On force le rôle admin par défaut
            $table->string('role')->default('admin'); 
            
            // SUPPRESSION : Ce champ ne doit pas être en base de données
            // $table->string('mot_de_passe_confirmation'); 
            
            $table->rememberToken();
            $table->timestamps();
        });

        // ... (le reste de vos tables password_reset_tokens et sessions reste identique)
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};