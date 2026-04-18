<?php

namespace App\Listeners;

use App\Events\CandidatureDeposee;
use Illuminate\Support\Facades\Log;

class LogCandidatureDeposee
{
    public function handle(CandidatureDeposee $event): void
    {
        $candidature = $event->candidature->load(['profil.user', 'offre']);
        $nomCandidat = $candidature->profil->user->name;
        $titreOffre  = $candidature->offre->titre;
        $date        = now()->toDateTimeString();

        Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/candidatures.log'),
        ])->info("[{$date}] Nouvelle candidature — Candidat: {$nomCandidat} | Offre: {$titreOffre}");
    }
}
