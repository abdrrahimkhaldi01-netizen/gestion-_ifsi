<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'niveau_id',
        'filiere_id',          // ✅ مضاف
        'annee_scolaire_id',
    ];

    // =========================================
    // RELATIONS
    // =========================================

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class); // ✅ مضاف
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class, 'group_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'groupe_module');
    }

    public function seances()
    {
        return $this->hasMany(Seance::class, 'group_id');
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'group_id');
    }
}