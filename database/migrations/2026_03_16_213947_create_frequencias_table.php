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
        Schema::create('frequencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idoso_id')->constrained('idosos')->onDelete('cascade');
            $table->date('data');
            $table->time('entrada')->nullable();
            $table->time('saida')->nullable();
            $table->enum('status', ['presente', 'ausente', 'justificado'])->default('presente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Garantir que não haja duplicidade de frequência para o mesmo idoso no mesmo dia
            $table->unique(['idoso_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencias');
    }
};
