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
            $table->string('raca_cor')->default('nao_informado')->after('sexo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idosos', function (Blueprint $table) {
            $table->dropColumn('raca_cor');
        });
    }
};
