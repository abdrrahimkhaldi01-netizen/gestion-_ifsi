<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('semestres', function (Blueprint $table) {
        // امسح اللي زدنا غلط
        if (Schema::hasColumn('semestres', 'date_debut')) {
            $table->dropColumn('date_debut');
        }
        if (Schema::hasColumn('semestres', 'date_fin')) {
            $table->dropColumn('date_fin');
        }
        // زيد الأعمدة الصحيحة
        if (!Schema::hasColumn('semestres', 'statut')) {
            $table->string('statut')->default('inactif'); // inactif | ouvert | cloture
        }
        if (!Schema::hasColumn('semestres', 'ouvert_at')) {
            $table->timestamp('ouvert_at')->nullable();
        }
        if (!Schema::hasColumn('semestres', 'cloture_at')) {
            $table->timestamp('cloture_at')->nullable();
        }
    });
}
};
