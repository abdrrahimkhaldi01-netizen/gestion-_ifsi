<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'nom',
        'note_sur',
        'module_id'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
     public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
   
