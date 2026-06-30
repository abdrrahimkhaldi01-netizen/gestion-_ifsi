<?php

namespace App\Services\Absences;

use App\Models\AbsenceAlert;
use App\Services\Notifications\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AbsenceAlertService
{
    public function __construct(
        private readonly AbsenceAnalyticsService $analytics,
        private readonly WhatsAppService $whatsApp,
    ) {
    }

    public function sendMonthlyAlerts(?Carbon $date = null): array
    {
        $date ??= now();
        $filters = [
            'month' => $date->month,
            'year' => $date->year,
        ];

        $candidates = $this->analytics
            ->getDashboardRows($filters)
            ->filter(fn (array $row) => $row['seance_absence_hours'] > AbsenceAnalyticsService::ALERT_THRESHOLD_HOURS);

        return [
            'month' => $date->month,
            'year' => $date->year,
            'threshold_hours' => AbsenceAnalyticsService::ALERT_THRESHOLD_HOURS,
            'candidates' => $candidates->count(),
            'created' => $this->createMissingAlerts($candidates, $date)->count(),
        ];
    }

    private function createMissingAlerts(Collection $candidates, Carbon $date): Collection
    {
        return $candidates
            ->reject(function (array $row) use ($date) {
                return AbsenceAlert::query()
                    ->where('stagiaire_id', $row['stagiaire']->id)
                    ->where('month', $date->month)
                    ->where('year', $date->year)
                    ->exists();
            })
            ->map(fn (array $row) => $this->createAlert($row, $date))
            ->values();
    }

    private function createAlert(array $row, Carbon $date): AbsenceAlert
    {
        $stagiaire = $row['stagiaire'];
        $phone = $this->resolvePhone($row);
        $message = $this->messageFor($row);

        if (!$phone) {
            return AbsenceAlert::create([
                'stagiaire_id' => $stagiaire->id,
                'month' => $date->month,
                'year' => $date->year,
                'seance_absence_hours' => $row['seance_absence_hours'],
                'stage_absence_days' => $row['stage_absence_days'],
                'phone' => null,
                'message' => $message,
                'status' => 'failed_no_phone',
                'sent_at' => null,
                'provider_response' => ['reason' => 'No student or responsible phone number available.'],
            ]);
        }

        $providerResponse = $this->whatsApp->send($phone, $message);

        return AbsenceAlert::create([
            'stagiaire_id' => $stagiaire->id,
            'month' => $date->month,
            'year' => $date->year,
            'seance_absence_hours' => $row['seance_absence_hours'],
            'stage_absence_days' => $row['stage_absence_days'],
            'phone' => $phone,
            'message' => $message,
            'status' => $providerResponse['status'] === 'logged' ? 'sent' : $providerResponse['status'],
            'sent_at' => $providerResponse['status'] === 'logged' ? now() : null,
            'provider_response' => $providerResponse,
        ]);
    }

    private function messageFor(array $row): string
    {
        return "Bonjour {$row['full_name']}, vous avez depasse 30 heures d'absence ce mois-ci. Merci de contacter l'administration.";
    }

    private function resolvePhone(array $row): ?string
    {
        $stagiaire = $row['stagiaire'];

        return $stagiaire->telephone ?: $stagiaire->responsable_telephone;
    }
}
