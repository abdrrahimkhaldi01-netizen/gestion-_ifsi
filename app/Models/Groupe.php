<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [

        'nom',
        'annee',
        'filiere_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Groupe appartient à une filière
     */

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Groupe possède plusieurs stagiaires
     */

    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class);
    }

    /**
     * Groupe possède plusieurs modules
     */

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    /**
     * Groupe possède plusieurs séances
     */

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

    /**
     * Groupe possède plusieurs stages
     */

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }
}