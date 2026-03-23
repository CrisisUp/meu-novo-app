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
        Schema::table('idosos', function (Blueprint $blueprint) {
            $blueprint->string('sexo', 1)->default('F')->after('data_nascimento');
            $blueprint->date('data_admissao')->nullable()->after('sexo');
            $blueprint->date('data_desligamento')->nullable()->after('data_admissao');
        });

        // Preencher data_admissao inicial baseada no created_at para idosos existentes
        DB::table('idosos')->update(['data_admissao' => DB::raw('DATE(created_at)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idosos', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['sexo', 'data_admissao', 'data_desligamento']);
        });
    }
};
