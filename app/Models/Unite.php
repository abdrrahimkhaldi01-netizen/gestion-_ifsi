<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\UnitExam;

class Unite extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'heures',
        'coefficient',
        'niveau_id',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function unitExams()
    {
        return $this->hasMany(UnitExam::class);
    }

    // =========================================================
    // BOOT LOGIC (SAFE)
    // =========================================================

    protected static function booted()
    {
        static::created(function ($unite) {
            $unite->generateExamsIfEmpty();
        });
    }

    // =========================================================
    // BUSINESS LOGIC
    // =========================================================

   public function generateExamsIfEmpty()
{
    if ($this->unitExams()->exists()) {
        return;
    }

    $exams = [
        ['type' => 'cc',        'poids' => 30], // ✅ أضفناه
        ['type' => 'theorique', 'poids' => 20],
        ['type' => 'pratique',  'poids' => 30],
    ];

    foreach ($exams as $exam) {
        $this->unitExams()->create($exam);
    }
}
}