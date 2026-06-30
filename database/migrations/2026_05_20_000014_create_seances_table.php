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
           $table->enum('type', [
                'cours',
                'td',
                'tp',
                'controle',
                'examen',
                'rattrapage'
            ])->default('cours');

        $table->foreignId('module_id')
              ->constrained('modules')       // ✅ explicit
              ->onDelete('cascade');

        $table->foreignId('formateur_id')
              ->constrained('formateurs')    // ✅ explicit (safe)
              ->onDelete('cascade');

        $table->foreignId('group_id')
              ->constrained('groupes')       // ✅ fix: was looking for 'groups'
              ->onDelete('cascade');
 $table->enum('statut_validation', [
    'en_attente',
    'validee',
    'refusee'
])->default('en_attente'); // ← حذفنا ->after('status')
        
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