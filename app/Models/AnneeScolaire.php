<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class AnneeScolaire extends Model
{
    use HasFactory;

    protected $table = 'annees_scolaires';

    // =========================================
    // CONSTANTES
    // =========================================

    public const ACTIVE   = 'active';
    public const ARCHIVEE = 'archivee';

    // =========================================
    // FILLABLE
    // =========================================

    protected $fillable = [
        'nom',
        'date_debut',
        'date_fin',
        'statut',
    ];

    // =========================================
    // CASTS
    // =========================================

    protected $casts = [
        'date_debut' => 'date:Y-m-d',
        'date_fin'   => 'date:Y-m-d',
    ];

    // =========================================
    // RELATIONS
    // =========================================

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    // ❌ حذفنا semestres() — ليس لها annee_scolaire_id

    public function resultats()
    {
        return $this->hasMany(Resultat::class);
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeActive($query)
    {
        return $query->where('statut', self::ACTIVE);
    }

    // =========================================
    // HELPERS
    // =========================================

    public function isActive(): bool
    {
        return $this->statut === self::ACTIVE;
    }

    public function isArchivee(): bool
    {
        return $this->statut === self::ARCHIVEE;
    }

    // =========================================
    // ACTIVER ANNÉE
    // =========================================

    public function activate(): void
    {
        DB::transaction(function () {
            static::where('statut', self::ACTIVE)
                ->whereKeyNot($this->id)
                ->update(['statut' => self::ARCHIVEE]);

            $this->update(['statut' => self::ACTIVE]);
        });
    }

    // =========================================
    // ARCHIVER ANNÉE
    // =========================================

    public function archive(): void
    {
        if ($this->isActive() && static::active()->count() === 1) {
            throw new \Exception('Impossible d\'archiver la seule année active.');
        }

        // ❌ حذفنا كود semestres — ليس لها annee_scolaire_id
        $this->update(['statut' => self::ARCHIVEE]);
    }

    // =========================================
    // RÉCUPÉRER ANNÉE ACTIVE
    // =========================================

    public static function current(): ?self
    {
        return static::active()->first();
    }
}