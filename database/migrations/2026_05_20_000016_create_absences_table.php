<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('absences', function (Blueprint $table) {
    $table->id();
    $table->date('date_absence');
    $table->string('motif')->nullable();
    $table->enum('type', ['seance', 'stage'])->default('seance');
    $table->boolean('justifiee')->default(false);
    
    $table->foreignId('stagiaire_id')
          ->constrained()
          ->onDelete('cascade');

    // nullable لأن كتكون إما seance أو stage
    $table->foreignId('seance_id')
          ->nullable()
          ->constrained()
          ->onDelete('cascade');
          
    $table->foreignId('stage_id')
          ->nullable()
          ->constrained()
          ->onDelete('cascade');

    $table->foreignId('formateur_id')
          ->constrained()
          ->onDelete('cascade');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};