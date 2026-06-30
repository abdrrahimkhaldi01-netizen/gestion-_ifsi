<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'duree',
        'nombre_cc',
        'formateur_id',
        'unite_id',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

    // =========================================================
    // AUTO GENERATION CC
    // =========================================================

    protected static function booted()
    {
        static::created(function ($module) {
            $module->generateCC();
        });

        static::updated(function ($module) {
            if ($module->wasChanged('nombre_cc')) {
                $module->syncCC();
            }
        });
    }

    public function generateCC()
    {
        for ($i = 1; $i <= $this->nombre_cc; $i++) {
            Evaluation::create([
                'module_id'   => $this->id,
                'nom'         => 'CC' . $i,
                'type'        => 'CC',
                'note_sur'    => 20,
                'coefficient' => 1,
            ]);
        }
    }

    public function syncCC()
    {
        $existing = $this->evaluations()->count();

        if ($existing < $this->nombre_cc) {
            for ($i = $existing + 1; $i <= $this->nombre_cc; $i++) {
                Evaluation::create([
                    'module_id'   => $this->id,
                    'nom'         => 'CC' . $i,
                    'type'        => 'CC',
                    'note_sur'    => 20,
                    'coefficient' => 1,
                ]);
            }
        }

        if ($existing > $this->nombre_cc) {
            $this->evaluations()
                ->latest()
                ->take($existing - $this->nombre_cc)
                ->delete();
        }
    }

    // =========================================================
    // HOURS / PROGRESS
    // =========================================================

    public function heuresValidees(): float
    {
        if ($this->relationLoaded('seances')) {
            return (float) $this->seances
                ->where('statut_validation', 'validee')
                ->sum(function (Seance $seance) {
                    return \Carbon\Carbon::parse($seance->heure_debut)
                        ->diffInMinutes(\Carbon\Carbon::parse($seance->heure_fin)) / 60;
                });
        }

        return (float) $this->seances()
            ->where('statut_validation', 'validee')
            ->selectRaw("
                SUM(TIMESTAMPDIFF(MINUTE, heure_debut, heure_fin)) / 60 as total
            ")
            ->value('total') ?? 0;
    }

    public function avancement(): float
    {
        if (!$this->duree) {
            return 0;
        }

        return min(
            100,
            round(($this->heuresValidees() / $this->duree) * 100, 1)
        );
    }

    public function estTermine(): bool
    {
        return $this->avancement() >= 100;
    }
}
