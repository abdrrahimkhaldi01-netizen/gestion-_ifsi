<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_exams', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['theorique', 'pratique', 'cc']); // ✅ زدنا cc هنا
            $table->decimal('poids', 5, 2)->default(50);
            $table->foreignId('unite_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_exams');
    }
};