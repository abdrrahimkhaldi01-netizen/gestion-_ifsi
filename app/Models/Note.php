<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;
 protected $fillable = [
        'note',
        'statut',
        'validee_at',
        'stagiaire_id',
        'evaluation_id',
        'unit_exam_id',
        'pfe_id',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function unitExam()
    {
        return $this->belongsTo(UnitExam::class);
    }

    public function pfe()
    {
        return $this->belongsTo(Pfe::class);
    }
}
