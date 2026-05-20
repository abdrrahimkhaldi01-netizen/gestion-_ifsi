<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [

        'cc1',
        'cc2',
        'cc3',
        'examen_final',
        'moyenne',
        'resultat',
        'stagiaire_id',
        'groupe_id',
        'filiere_id',
        'module_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}