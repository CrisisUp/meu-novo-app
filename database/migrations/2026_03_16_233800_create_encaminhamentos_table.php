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
        Schema::create('encaminhamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idoso_id')->constrained('idosos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Profissional que encaminhou
            $table->string('instituicao_destino'); // Hospital, UPA, CRAS, etc.
            $table->string('especialidade')->nullable(); // Dentista, Oftalmo, etc.
            $table->text('motivo');
            $table->enum('prioridade', ['urgente', 'programado', 'rotina'])->default('rotina');
            $table->date('data_encaminhamento');
            $table->enum('status', ['aberto', 'concluido', 'cancelado'])->default('aberto');
            $table->text('observacoes_retorno')->nullable(); // Contrarreferência
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encaminhamentos');
    }
};
