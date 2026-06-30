<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise',
        'date_debut',
        'date_fin',
        'group_id',
    ];

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stage_stagiaire');
    }

    // ✅ زيد 'group_id'
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