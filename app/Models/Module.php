<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [

        'titre',
        'description',
        'duree',
        'formateur_id',
        'filiere_id',
        'group_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Module appartient à un formateur
     */

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    /**
     * Module appartient à une filière
     */

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    /**
     * Module appartient à un groupe
     */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function seances()
{
    return $this->hasMany(Seance::class);
}
}