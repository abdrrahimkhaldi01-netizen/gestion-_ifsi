<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
   Schema::create('evaluations', function (Blueprint $table) {

    $table->id();

    $table->string('nom'); // CC1, CC2

    $table->decimal('note_sur', 5, 2)->default(20);

    $table->foreignId('module_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};