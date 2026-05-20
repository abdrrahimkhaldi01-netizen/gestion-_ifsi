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
        Schema::create('seances', function (Blueprint $table) {

            $table->id();

            $table->date('date_seance');

            $table->time('heure_debut');

            $table->time('heure_fin');

            $table->text('description');

            $table->foreignId('module_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('formateur_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('group_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};