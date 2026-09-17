<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            //  Contrainte d'unicité combinée : un utilisateur ne peut pas avoir 2 fois le même nom
            $table->unique(['name', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};