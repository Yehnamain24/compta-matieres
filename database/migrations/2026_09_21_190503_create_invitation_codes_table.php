<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

            Schema::create('invitation_codes', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation_codes');
    }
};
