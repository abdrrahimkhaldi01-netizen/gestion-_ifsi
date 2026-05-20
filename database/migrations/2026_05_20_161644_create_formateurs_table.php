<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formateurs', function (Blueprint $table) {

            $table->id();

            $table->string('telephone');

            $table->string('adresse');

            $table->string('specialite');

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formateurs');
    }
};