<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('model_type'); // Ex: App\Models\Idoso
            $table->unsignedBigInteger('model_id'); // ID do registro afetado
            $table->string('action'); // created, updated, deleted
            $table->json('old_values')->nullable(); // Valores antes da mudança
            $table->json('new_values')->nullable(); // Valores depois da mudança
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
