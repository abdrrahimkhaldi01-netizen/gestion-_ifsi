<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ زيد هذا

class Resultat extends Model
{
    use HasFactory; // ✅ زيد هذا

    protected $fillable = [
        'stagiaire_id',
        'semestre_id',
        'annee_scolaire_id',
        'moyenne_generale',
        'statut',
        'decision',
        'mention',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}