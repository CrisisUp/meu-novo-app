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
        Schema::table('encaminhamentos', function (Blueprint $table) {
            // No SQLite (usado neste projeto), o Laravel lida com a recriação da tabela 
            // para aplicar alterações de chaves estrangeiras.
            // Tornamos o user_id nullable para permitir 'set null' na deleção.
            $table->foreignId('user_id')->nullable()->change()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encaminhamentos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change()->constrained('users')->onDelete('restrict');
        });
    }
};
