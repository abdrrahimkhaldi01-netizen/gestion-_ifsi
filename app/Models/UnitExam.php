<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitExam extends Model
{
    protected $fillable = [
        'type',
        'poids',
        'unite_id',
    ];

    // =========================================================
    // RELATION
    // =========================================================

    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }
}