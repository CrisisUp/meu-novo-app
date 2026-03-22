<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividade_idoso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atividade_id')->constrained('atividades')->onDelete('cascade');
            $table->foreignId('idoso_id')->constrained('idosos')->onDelete('cascade');
            $table->timestamps();
            
            // Impede duplicidade de vínculo
            $table->unique(['atividade_id', 'idoso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividade_idoso');
    }
};
