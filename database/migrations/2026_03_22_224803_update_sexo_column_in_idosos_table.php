<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idosos', function (Blueprint $table) {
            $table->string('sexo', 20)->change();
        });

        // Mapeia os dados antigos para o novo padrão inclusivo
        DB::table('idosos')->where('sexo', 'M')->update(['sexo' => 'cis_m']);
        DB::table('idosos')->where('sexo', 'F')->update(['sexo' => 'cis_f']);
    }

    public function down(): void
    {
        // Reverte para o padrão binário (simplificado)
        DB::table('idosos')->whereIn('sexo', ['cis_m', 'trans_m'])->update(['sexo' => 'M']);
        DB::table('idosos')->whereIn('sexo', ['cis_f', 'trans_f', 'agenero', 'nao_declarado'])->update(['sexo' => 'F']);

        Schema::table('idosos', function (Blueprint $table) {
            $table->string('sexo', 1)->change();
        });
    }
};
