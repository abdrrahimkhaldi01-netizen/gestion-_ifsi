<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_absence',
        'motif',
        'type',
        'justifiee',    // ✅ صح — من migration
        // ❌ حذف 'statut' — ما كاينش في migration
        'stagiaire_id',
        'seance_id',
        'stage_id',
        'formateur_id',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    // ✅ زيد هاد
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}