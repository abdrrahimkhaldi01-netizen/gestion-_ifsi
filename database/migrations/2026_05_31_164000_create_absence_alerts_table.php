<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('seance_absence_hours', 8, 2)->default(0);
            $table->unsignedInteger('stage_absence_days')->default(0);
            $table->string('phone')->nullable();
            $table->text('message');
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamps();

            $table->unique(['stagiaire_id', 'month', 'year'], 'absence_alerts_month_unique');
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_alerts');
    }
};
