<?php

namespace App\Listeners;

use App\Events\StatutCandidatureMis;
use Illuminate\Support\Facades\Log;

class LogStatutCandidatureMis
{
    public function handle(StatutCandidatureMis $event): void
    {
        $date          = now()->toDateTimeString();
        $ancienStatut  = $event->ancienStatut;
        $nouveauStatut = $event->nouveauStatut;
        $candidatureId = $event->candidature->id;

        Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/candidatures.log'),
        ])->info("[{$date}] Statut modifié — Candidature #{$candidatureId} | {$ancienStatut} → {$nouveauStatut}");
    }
}
