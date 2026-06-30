<?php

namespace App\Services\Absences;

use App\Models\Absence;
use App\Models\Stagiaire;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AbsenceAnalyticsService
{
    public const ALERT_THRESHOLD_HOURS = 30.0;

    public function getDashboardRows(array $filters = []): Collection
    {
        [$month, $year] = $this->resolveMonthYear($filters);

        $seanceHours = $this->getSeanceAbsenceHours($filters, $month, $year);
        $stageDays = $this->getStageAbsenceDays($filters, $month, $year);
        $stagiaireIds = $seanceHours->keys()->merge($stageDays->keys())->unique()->values();

        if ($stagiaireIds->isEmpty()) {
            return collect();
        }

        $relations = [
            'filiere',
            'groupe.filiere',
            'groupe.niveau.filiere',
        ];

        if (Schema::hasTable('absence_alerts')) {
            $relations['absenceAlerts'] = fn ($query) => $query
                ->where('month', $month)
                ->where('year', $year);
        }

        $stagiaires = Stagiaire::query()
            ->with($relations)
            ->whereIn('id', $stagiaireIds)
            ->get()
            ->keyBy('id');

        return $stagiaireIds
            ->map(function (int $stagiaireId) use ($stagiaires, $seanceHours, $stageDays) {
                $stagiaire = $stagiaires->get($stagiaireId);

                if (!$stagiaire) {
                    return null;
                }

                $filiere = $stagiaire->filiere
                    ?? $stagiaire->groupe?->filiere
                    ?? $stagiaire->groupe?->niveau?->filiere;
                $hours = round((float) ($seanceHours->get($stagiaireId) ?? 0), 2);
                $days = (int) ($stageDays->get($stagiaireId) ?? 0);
                $alert = $stagiaire->relationLoaded('absenceAlerts')
                    ? $stagiaire->absenceAlerts->first()
                    : null;

                return [
                    'stagiaire' => $stagiaire,
                    'full_name' => trim($stagiaire->nom . ' ' . $stagiaire->prenom),
                    'cin' => $stagiaire->cin,
                    'groupe' => $stagiaire->groupe,
                    'filiere' => $filiere,
                    'seance_absence_hours' => $hours,
                    'stage_absence_days' => $days,
                    'alert_status' => $this->resolveAlertStatus($hours, $alert?->status),
                    'alert' => $alert,
                ];
            })
            ->filter()
            ->sortByDesc('seance_absence_hours')
            ->values();
    }

    public function getSeanceAbsenceHours(array $filters, int $month, int $year): Collection
    {
        return $this->baseAbsenceQuery($filters, $month, $year, 'seance')
            ->with('seance:id,heure_debut,heure_fin')
            ->get(['id', 'stagiaire_id', 'seance_id', 'date_absence'])
            ->groupBy('stagiaire_id')
            ->map(function (Collection $absences) {
                return $absences->sum(fn (Absence $absence) => $this->seanceDurationHours($absence));
            });
    }

    public function getStageAbsenceDays(array $filters, int $month, int $year): Collection
    {
        return $this->baseAbsenceQuery($filters, $month, $year, 'stage')
            ->get(['id', 'stagiaire_id', 'date_absence'])
            ->groupBy('stagiaire_id')
            ->map(function (Collection $absences) {
                return $absences
                    ->pluck('date_absence')
                    ->map(fn ($date) => Carbon::parse($date)->toDateString())
                    ->unique()
                    ->count();
            });
    }

    public function resolveMonthYear(array $filters = []): array
    {
        $now = now();

        return [
            (int) ($filters['month'] ?? $now->month),
            (int) ($filters['year'] ?? $now->year),
        ];
    }

    private function baseAbsenceQuery(array $filters, int $month, int $year, string $type): Builder
    {
        return Absence::query()
            ->where('type', $type)
            ->whereMonth('date_absence', $month)
            ->whereYear('date_absence', $year)
            ->whereHas('stagiaire', function (Builder $query) use ($filters) {
                $query
                    ->when($filters['groupe_id'] ?? null, fn (Builder $q, $groupeId) => $q->where('group_id', $groupeId))
                    ->when($filters['filiere_id'] ?? null, function (Builder $q, $filiereId) {
                        $q->where(function (Builder $subQuery) use ($filiereId) {
                            $subQuery
                                ->where('filiere_id', $filiereId)
                                ->orWhereHas('groupe', fn (Builder $groupQuery) => $groupQuery->where('filiere_id', $filiereId))
                                ->orWhereHas('groupe.niveau', fn (Builder $niveauQuery) => $niveauQuery->where('filiere_id', $filiereId));
                        });
                    })
                    ->when($filters['annee_scolaire_id'] ?? null, function (Builder $q, $anneeScolaireId) {
                        $q->whereHas('groupe', fn (Builder $groupQuery) => $groupQuery->where('annee_scolaire_id', $anneeScolaireId));
                    });
            });
    }

    private function seanceDurationHours(Absence $absence): float
    {
        if (!$absence->seance?->heure_debut || !$absence->seance?->heure_fin) {
            return 0.0;
        }

        $start = Carbon::parse($absence->seance->heure_debut);
        $end = Carbon::parse($absence->seance->heure_fin);
        $minutes = $start->diffInMinutes($end, false);

        return $minutes > 0 ? $minutes / 60 : 0.0;
    }

    private function resolveAlertStatus(float $hours, ?string $storedStatus): string
    {
        if ($storedStatus) {
            return $storedStatus;
        }

        return $hours > self::ALERT_THRESHOLD_HOURS
            ? 'alert_required'
            : 'normal';
    }
}
