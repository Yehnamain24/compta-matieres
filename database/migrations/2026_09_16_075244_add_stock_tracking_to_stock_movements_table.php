<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->integer('stock_initial')->nullable()->after('quantity');
            $table->integer('stock_final')->nullable()->after('stock_initial');
            $table->string('note')->nullable()->after('stock_final');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['stock_initial', 'stock_final', 'note']);
        });
    }
};