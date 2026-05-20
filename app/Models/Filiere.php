<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [

        'titre',
        'duree',
        'type',
        'niveau',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Une filière possède plusieurs modules
     */

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    /**
     * Une filière possède plusieurs groupes
     */

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}