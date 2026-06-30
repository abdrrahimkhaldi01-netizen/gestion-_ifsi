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
Schema::create('pfes', function (Blueprint $table) {

    $table->id();

    $table->foreignId('stagiaire_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->string('titre');

    $table->text('description')->nullable();

    $table->decimal('note', 5,2)->nullable();

    $table->date('date_soutenance')->nullable();

    $table->enum('statut', [
        'en_preparation',
        'depose',
        'soutenance',
        'valide'
    ])->default('en_preparation');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfes');
    }
};
