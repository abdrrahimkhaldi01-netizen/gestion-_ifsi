<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('unites', function (Blueprint $table) {

    $table->id();

    $table->string('nom');

    // code ديال unité (اختياري)
    $table->string('code')->nullable();

    // عدد الساعات (مهم جدا للحساب ديال CC)
    $table->integer('heures');

    // coefficient ديال unité
    $table->decimal('coefficient', 5, 2)->default(1);

    // الربط الصحيح
    $table->foreignId('niveau_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('unites');
    }
};