<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pfe extends Model
{
    protected $table = 'pfes'; // أو pfe_notes حسب DB ديالك

    protected $fillable = [
        'stagiaire_id',
        'titre',
        'note',
    ];

    protected $attributes = [
        'titre' => 'PFE',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}