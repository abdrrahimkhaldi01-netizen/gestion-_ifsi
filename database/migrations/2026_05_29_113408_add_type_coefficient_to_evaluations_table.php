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
    Schema::table('evaluations', function (Blueprint $table) {
        $table->string('type')->default('CC')->after('nom');
        $table->decimal('coefficient', 5, 2)->default(1)->after('note_sur');
    });
}

public function down(): void
{
    Schema::table('evaluations', function (Blueprint $table) {
        $table->dropColumn(['type', 'coefficient']);
    });
}
};
