<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('notes', function (Blueprint $table) {

    $table->id();

    $table->decimal('note', 5, 2);

    $table->string('statut')->default('en_attente');

    $table->timestamp('validee_at')->nullable();

    $table->foreignId('stagiaire_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('evaluation_id')->nullable()
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('unit_exam_id')->nullable()
          ->constrained()
          ->cascadeOnDelete();

   $table->foreignId('pfe_id')->nullable()
      ->constrained('pfes')
      ->cascadeOnDelete();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};