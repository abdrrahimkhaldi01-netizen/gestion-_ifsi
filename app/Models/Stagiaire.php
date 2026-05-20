<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [

        'nom',
        'prenom',
        'date_naissance',
        'filiere_id',
        'group_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Un stagiaire appartient à une filière
     */

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Un stagiaire appartient à un groupe
     */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Un stagiaire possède plusieurs notes
     */

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Un stagiaire possède plusieurs absences
     */

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Un stagiaire possède plusieurs stages
     */

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }
}