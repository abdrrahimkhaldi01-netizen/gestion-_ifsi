<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semestre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'ordre',
        'niveau_id',
        'annee_scolaire_id',
        'statut',
        'ouvert_at',
        'cloture_at',
    ];

    protected $casts = [
        'ouvert_at' => 'datetime',
        'cloture_at' => 'datetime',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function resultats()
    {
        return $this->hasMany(Resultat::class);
    }
}