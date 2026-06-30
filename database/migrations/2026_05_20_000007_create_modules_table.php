<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('modules', function (Blueprint $table) {

            $table->id();

            $table->string('titre');
            $table->text('description')->nullable();
            $table->integer('duree')->nullable();

            // nombre des CC auto
            $table->integer('nombre_cc')->default(1);

            $table->foreignId('formateur_id')
                  ->constrained('formateurs')
                  ->cascadeOnDelete();

            $table->foreignId('unite_id')
                  ->constrained('unites')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};