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
        'stagiaire_id',
        'group_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Stage appartient à un stagiaire
     */

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    /**
     * Stage appartient à un groupe
     */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}