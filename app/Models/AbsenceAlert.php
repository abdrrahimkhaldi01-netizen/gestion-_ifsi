<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id',
        'month',
        'year',
        'seance_absence_hours',
        'stage_absence_days',
        'phone',
        'message',
        'status',
        'sent_at',
        'provider_response',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'seance_absence_hours' => 'decimal:2',
        'stage_absence_days' => 'integer',
        'sent_at' => 'datetime',
        'provider_response' => 'array',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }
}
