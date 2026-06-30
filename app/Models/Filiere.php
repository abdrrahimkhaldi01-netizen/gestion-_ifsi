<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'duree', 'type'];

    public function niveaux()
    {
        return $this->hasMany(Niveau::class);
    }

    public function unites()
    {
        return $this->hasMany(Unite::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }
}