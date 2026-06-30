<?php

namespace App\Services\Notes;

use App\Models\Stagiaire;
use App\Models\Unite;
use Illuminate\Support\Collection;

class StudentResultsService
{
    private const PFE_WEIGHT = 0.2;

    public function getResults(?int $filiereId = null, ?int $groupeId = null): Collection
    {
        $stagiaires = Stagiaire::query()
            ->with([
                'filiere',
                'groupe.filiere',
                'groupe.niveau.filiere',
                'notes.evaluation.module',
            ])
            ->when($filiereId, fn ($query) => $query->where('filiere_id', $filiereId))
            ->when($groupeId, fn ($query) => $query->where('group_id', $groupeId))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $results = $stagiaires
            ->map(function (Stagiaire $stagiaire) {
                $modules = $stagiaire->notes
                    ->whereNotNull('evaluation_id')
                    ->filter(fn ($note) => $note->evaluation?->module)
                    ->groupBy(fn ($note) => $note->evaluation->module_id)
                    ->map(function (Collection $notes) {
                        $module = $notes->first()->evaluation->module;
                        $moduleNotes = $notes
                            ->sortBy(fn ($note) => $note->evaluation?->nom)
                            ->values();

                        return [
                            'module' => $module,
                            'notes' => $moduleNotes,
                            'moyenne' => round((float) $moduleNotes->avg('note'), 2),
                        ];
                    })
                    ->sortBy(fn ($row) => $row['module']->titre ?? $row['module']->nom ?? '')
                    ->values();

                if ($modules->isEmpty()) {
                    return null;
                }

                $moyenne = round((float) $modules->avg('moyenne'), 2);
                $filiere = $stagiaire->filiere
                    ?? $stagiaire->groupe?->filiere
                    ?? $stagiaire->groupe?->niveau?->filiere;

                return [
                    'stagiaire' => $stagiaire,
                    'full_name' => trim($stagiaire->nom . ' ' . $stagiaire->prenom),
                    'cin' => $stagiaire->cin,
                    'groupe' => $stagiaire->groupe,
                    'filiere' => $filiere,
                    'modules' => $modules,
                    'moyenne' => $moyenne,
                    'classement' => null,
                ];
            })
            ->filter()
            ->sort(function (array $a, array $b) {
                $averageComparison = $b['moyenne'] <=> $a['moyenne'];

                return $averageComparison !== 0
                    ? $averageComparison
                    : strcasecmp($a['full_name'], $b['full_name']);
            })
            ->values();

        return $this->applyRanking($results);
    }

  public function getBulletinRows(
    ?int $filiereId = null,
    ?int $groupeId = null,
    ?int $stagiaireId = null
): Collection {
    $results = $this->getBulletinResults($filiereId, $groupeId, $stagiaireId);

    // جمع كل أسماء الوحدات من جميع الstagiaires
    $allUnitNames = $results
        ->flatMap(fn ($student) => $student['units']->map(fn ($u) => $u['unite']->nom ?? 'Unite'))
        ->unique()
        ->sort()
        ->values();

    return $results->map(function (array $student) use ($allUnitNames) {
        $row = [
            'Nom'     => $student['full_name'],
            'Groupe'  => $student['groupe']?->nom ?? '-',
            'Filiere' => $student['filiere']?->nom ?? '-',
            'Periode' => $student['periode'],
        ];

        // كل الوحدات — فارغة إلا ما كانتش عند الstagiaire
        foreach ($allUnitNames as $unitName) {
            $unit = collect($student['units'])->first(fn ($u) => ($u['unite']->nom ?? '') === $unitName);
            $notePond = $unit ? ($unit['average'] ?? 0) * $unit['coefficient'] : null;

            $row[$unitName . ' - Moyenne']    = $unit ? $this->formatNumber($unit['average']) : '';
            $row[$unitName . ' - Coef']       = $unit ? $this->formatNumber($unit['coefficient']) : '';
            $row[$unitName . ' - Note Pond.'] = $unit ? $this->formatNumber($notePond) : '';
        }

        $row['PFE - Note']       = $this->formatNumber($student['pfe_grade']);
        $row['Coef Total']       = $this->formatNumber($student['total_unit_coefficient']);
        $row['Note Pond. Total'] = $this->formatNumber(
            $student['units']->sum(fn ($u) => ($u['average'] ?? 0) * $u['coefficient'])
        );
        $row['Moyenne Generale'] = $this->formatNumber($student['general_average']);

        return $row;
    })->values();
}

    public function getBulletinResults(
        ?int $filiereId = null,
        ?int $groupeId = null,
        ?int $stagiaireId = null
    ): Collection {
        return $this->bulletinQuery($filiereId, $groupeId, $stagiaireId)
            ->get()
            ->map(fn (Stagiaire $stagiaire) => $this->buildStudentBulletin($stagiaire))
            ->filter(fn (?array $student) => $student !== null)
            ->sortBy('full_name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    private function applyRanking(Collection $results): Collection
    {
        $previousAverage = null;
        $previousRank = 0;

        return $results->map(function (array $row, int $index) use (&$previousAverage, &$previousRank) {
            $rank = ($previousAverage !== null && $row['moyenne'] === $previousAverage)
                ? $previousRank
                : $index + 1;

            $row['classement'] = $rank;
            $previousAverage = $row['moyenne'];
            $previousRank = $rank;

            return $row;
        });
    }

    private function bulletinQuery(?int $filiereId, ?int $groupeId, ?int $stagiaireId)
    {
        return Stagiaire::query()
            ->with([
                'filiere',
                'groupe.filiere',
                'groupe.anneeScolaire',
                'groupe.niveau.filiere',
                'groupe.niveau.semestres',
                'pfe',
                'notes.evaluation.module.unite.unitExams',
                'notes.unitExam.unite.modules.evaluations',
                'notes.unitExam.unite.unitExams',
            ])
            ->when($stagiaireId, fn ($query) => $query->whereKey($stagiaireId))
            ->when($groupeId, fn ($query) => $query->where('group_id', $groupeId))
            ->when($filiereId, function ($query) use ($filiereId) {
                $query->where(function ($subQuery) use ($filiereId) {
                    $subQuery
                        ->where('filiere_id', $filiereId)
                        ->orWhereHas('groupe', fn ($groupQuery) => $groupQuery->where('filiere_id', $filiereId))
                        ->orWhereHas('groupe.niveau', fn ($niveauQuery) => $niveauQuery->where('filiere_id', $filiereId));
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom');
    }

    private function buildStudentBulletin(Stagiaire $stagiaire): ?array
    {
        $moduleUnitRows = $this->buildModuleBasedUnitRows($stagiaire);
        $examUnitRows = $this->buildExamBasedUnitRows($stagiaire, $moduleUnitRows);

        $units = $moduleUnitRows
            ->merge($examUnitRows)
            ->groupBy(fn (array $row) => $row['unite']->id)
            ->map(fn (Collection $rows) => $this->mergeUnitRows($rows))
            ->filter(fn (array $row) => $row['average'] !== null)
            ->sortBy(fn (array $row) => $row['unite']->nom ?? '')
            ->values();

        if ($units->isEmpty()) {
            return null;
        }

        $totalCoefficient = (float) $units->sum('coefficient');
        $unitWeightedAverage = $totalCoefficient > 0
            ? round((float) $units->sum(fn (array $unit) => $unit['average'] * $unit['coefficient']) / $totalCoefficient, 2)
            : null;

        $pfeGrade = $stagiaire->pfe?->note !== null ? (float) $stagiaire->pfe->note : null;
        $generalAverage = $this->computeGeneralAverage($unitWeightedAverage, $pfeGrade);
        $filiere = $stagiaire->filiere
            ?? $stagiaire->groupe?->filiere
            ?? $stagiaire->groupe?->niveau?->filiere;

        return [
            'stagiaire' => $stagiaire,
            'full_name' => trim($stagiaire->nom . ' ' . $stagiaire->prenom),
            'groupe' => $stagiaire->groupe,
            'filiere' => $filiere,
            'periode' => $this->resolvePeriode($stagiaire),
            'units' => $units,
            'total_unit_coefficient' => $totalCoefficient,
            'pfe_grade' => $pfeGrade,
            'general_average' => $generalAverage,
        ];
    }

    private function buildModuleBasedUnitRows(Stagiaire $stagiaire): Collection
    {
        return $stagiaire->notes
            ->whereNotNull('evaluation_id')
            ->filter(fn ($note) => $note->evaluation?->module?->unite)
            ->groupBy(fn ($note) => $note->evaluation->module->unite_id)
            ->map(function (Collection $unitNotes) {
                $unite = $unitNotes->first()->evaluation->module->unite;

                $modules = $unitNotes
                    ->groupBy(fn ($note) => $note->evaluation->module_id)
                    ->map(function (Collection $moduleNotes) {
                        $module = $moduleNotes->first()->evaluation->module;
                        $weighted = $this->weightedAverage(
                            $moduleNotes,
                            fn ($note) => (float) $note->note,
                            fn ($note) => (float) ($note->evaluation?->coefficient ?? 1)
                        );

                        return [
                            'average' => $weighted,
                            'coefficient' => (float) ($module->coefficient ?? 1),
                        ];
                    })
                    ->filter(fn (array $module) => $module['average'] !== null)
                    ->values();

                if ($modules->isEmpty()) {
                    return null;
                }

                return [
                    'unite' => $unite,
                    'average' => round((float) $this->weightedAverage(
                        $modules,
                        fn (array $module) => $module['average'],
                        fn (array $module) => $module['coefficient']
                    ), 2),
                    'coefficient' => (float) ($unite->coefficient ?? $modules->sum('coefficient') ?: 1),
                    'source' => 'modules',
                ];
            })
            ->filter()
            ->values();
    }

    private function buildExamBasedUnitRows(Stagiaire $stagiaire, Collection $moduleUnitRows): Collection
    {
        return $stagiaire->notes
            ->whereNotNull('unit_exam_id')
            ->filter(fn ($note) => $note->unitExam?->unite)
            ->groupBy(fn ($note) => $note->unitExam->unite_id)
            ->map(function (Collection $examNotes) use ($moduleUnitRows) {
                $unite = $examNotes->first()->unitExam->unite;
                $weights = $this->unitExamWeights($unite);
                $components = collect();

                $ccAverage = $moduleUnitRows->first(fn (array $row) => $row['unite']->id === $unite->id)['average'] ?? null;
                if ($ccAverage !== null && $weights['cc'] > 0) {
                    $components->push(['average' => $ccAverage, 'coefficient' => $weights['cc']]);
                }

                foreach (['theorique', 'pratique'] as $type) {
                    $note = $examNotes->first(fn ($examNote) => $examNote->unitExam?->type === $type);

                    if ($note?->note !== null && $weights[$type] > 0) {
                        $components->push(['average' => (float) $note->note, 'coefficient' => $weights[$type]]);
                    }
                }

                if ($components->isEmpty()) {
                    return null;
                }

                return [
                    'unite' => $unite,
                    'average' => round((float) $this->weightedAverage(
                        $components,
                        fn (array $component) => $component['average'],
                        fn (array $component) => $component['coefficient']
                    ), 2),
                    'coefficient' => (float) ($unite->coefficient ?? 1),
                    'source' => 'exams',
                ];
            })
            ->filter()
            ->values();
    }

    private function mergeUnitRows(Collection $rows): array
    {
        $examRow = $rows->first(fn (array $row) => $row['source'] === 'exams');

        return $examRow ?? $rows->first();
    }

    private function unitExamWeights(Unite $unite): array
    {
        return [
            'cc' => (float) ($unite->unitExams->firstWhere('type', 'cc')?->poids ?? 0),
            'theorique' => (float) ($unite->unitExams->firstWhere('type', 'theorique')?->poids ?? 0),
            'pratique' => (float) ($unite->unitExams->firstWhere('type', 'pratique')?->poids ?? 0),
        ];
    }

    private function computeGeneralAverage(?float $unitAverage, ?float $pfeGrade): ?float
    {
        if ($unitAverage === null) {
            return null;
        }

        if ($pfeGrade === null) {
            return round($unitAverage, 2);
        }

        return round(($unitAverage * (1 - self::PFE_WEIGHT)) + ($pfeGrade * self::PFE_WEIGHT), 2);
    }

    private function weightedAverage(Collection $items, callable $valueResolver, callable $coefficientResolver): ?float
    {
        $total = 0.0;
        $coefficients = 0.0;

        foreach ($items as $item) {
            $value = $valueResolver($item);
            $coefficient = (float) $coefficientResolver($item);

            if ($value === null || $coefficient <= 0) {
                continue;
            }

            $total += ((float) $value) * $coefficient;
            $coefficients += $coefficient;
        }

        return $coefficients > 0 ? $total / $coefficients : null;
    }

    private function resolvePeriode(Stagiaire $stagiaire): string
    {
        $semestre = $stagiaire->groupe?->niveau?->semestres
            ?->sortByDesc('ordre')
            ->first();

        return $semestre?->nom
            ?? $stagiaire->groupe?->anneeScolaire?->nom
            ?? 'Periode actuelle';
    }

    private function formatNumber(?float $value): string
    {
        return $value === null ? '' : number_format($value, 2, '.', '');
    }
}
