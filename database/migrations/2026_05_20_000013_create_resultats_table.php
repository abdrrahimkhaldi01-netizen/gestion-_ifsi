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
    Schema::create('resultats', function (Blueprint $table) {
        $table->id();
        $table->foreignId('stagiaire_id')
              ->constrained()
              ->cascadeOnDelete();
        $table->foreignId('semestre_id')
              ->constrained()
              ->cascadeOnDelete();
        $table->foreignId('annee_scolaire_id')  // ✅ مضاف
              ->constrained('annees_scolaires')
              ->cascadeOnDelete();
        $table->decimal('moyenne_generale', 5, 2)->nullable();
        $table->enum('statut', ['valide', 'non_valide', 'en_attente'])->default('en_attente');
        $table->string('decision')->nullable();
        $table->string('mention')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultats');
    }
};
