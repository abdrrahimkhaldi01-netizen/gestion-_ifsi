<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $fillable = [
        'nom',
        'ordre',
        'filiere_id',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function unites()
    {
        return $this->hasMany(Unite::class);
    }

    // ✅ زيد هاد
    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    // ✅ زيد هاد
    public function semestres()
    {
        return $this->hasMany(Semestre::class);
    }
}