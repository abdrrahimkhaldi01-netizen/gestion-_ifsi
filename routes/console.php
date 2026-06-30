<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\Absences\AbsenceAlertService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('absences:send-monthly-alerts', function (AbsenceAlertService $alerts) {
    $result = $alerts->sendMonthlyAlerts();

    $this->info(sprintf(
        'Absence alerts checked for %02d/%d: %d candidate(s), %d new alert(s).',
        $result['month'],
        $result['year'],
        $result['candidates'],
        $result['created'],
    ));
})->purpose('Send monthly absence WhatsApp alerts in log-only mode');

Schedule::command('absences:send-monthly-alerts')->dailyAt('18:00');
