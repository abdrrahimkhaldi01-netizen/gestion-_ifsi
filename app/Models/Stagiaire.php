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
        'cin',
        'adresse',
        'telephone',
        'responsable_telephone',
        'filiere_id',
        'group_id',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'group_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function absenceAlerts()
    {
        return $this->hasMany(AbsenceAlert::class);
    }

    // ✅ many-to-many
    public function stages()
    {
        return $this->belongsToMany(Stage::class, 'stage_stagiaire');
    }

    // ✅ زيد هاد
    public function pfe()
    {
        return $this->hasOne(Pfe::class);
    }

    // ✅ زيد هاد
    public function unitExams()
    {
        return $this->hasManyThrough(Note::class, UnitExam::class, 'id', 'unit_exam_id');
    }
}
