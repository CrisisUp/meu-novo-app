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
        Schema::table('idosos', function (Blueprint $table) {
            $table->softDeletes();
            
            // Índices para performance em buscas e filtros
            $table->index('nome');
            $table->index('cpf');
            $table->index('data_desligamento');
            $table->index('grau_dependencia');
        });

        Schema::table('frequencias', function (Blueprint $table) {
            $table->index('data');
            $table->index('status');
        });

        Schema::table('encaminhamentos', function (Blueprint $table) {
            $table->index('prioridade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idosos', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['nome']);
            $table->dropIndex(['cpf']);
            $table->dropIndex(['data_desligamento']);
            $table->dropIndex(['grau_dependencia']);
        });

        Schema::table('frequencias', function (Blueprint $table) {
            $table->dropIndex(['data']);
            $table->dropIndex(['status']);
        });

        Schema::table('encaminhamentos', function (Blueprint $table) {
            $table->dropIndex(['prioridade']);
        });
    }
};
