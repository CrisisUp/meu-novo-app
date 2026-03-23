<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idosos', function (Blueprint $table) {
            $table->enum('grau_dependencia', ['I', 'II', 'III'])->default('I')->after('sexo');
        });
    }

    public function down(): void
    {
        Schema::table('idosos', function (Blueprint $table) {
            $table->dropColumn('grau_dependencia');
        });
    }
};
