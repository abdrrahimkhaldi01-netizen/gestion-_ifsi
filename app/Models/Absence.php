<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [

        'date_absence',
        'motif',
        'stagiaire_id',
        'stage_id',
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

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}