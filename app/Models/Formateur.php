<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Formateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'telephone',
        'adresse', 
        'specialite',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}