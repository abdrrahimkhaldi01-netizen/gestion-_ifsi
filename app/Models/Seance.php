<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_seance',
        'heure_debut',
        'heure_fin',
        'description',
        'type',
        'statut_validation',
        'module_id',
        'formateur_id',
        'group_id',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'group_id');
    }

    // ✅ زيد هاد
    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}