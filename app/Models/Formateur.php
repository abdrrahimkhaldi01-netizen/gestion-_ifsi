<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Formateur extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [

        'telephone',
        'adresse',
        'specialite',
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Un formateur appartient à un user
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un formateur possède plusieurs formations
     */

    public function modules()
{
    return $this->hasMany(Module::class);
}

    /**
     * Un formateur possède plusieurs notes
     */

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Un formateur possède plusieurs absences
     */

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Un formateur possède plusieurs séances
     */

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
    
}