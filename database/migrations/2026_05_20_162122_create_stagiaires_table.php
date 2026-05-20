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
    Schema::create('stagiaires', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->string('prenom');
        $table->date('date_naissance');

        $table->foreignId('filiere_id')
              ->constrained('filieres')   // explicit to be safe
              ->onDelete('cascade');

        $table->foreignId('group_id')
              ->constrained('groupes')    // 👈 fix: was looking for 'groups'
              ->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};