<?php

namespace App\Events;

use App\Models\Candidature;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidatureDeposee
{
    use Dispatchable, SerializesModels;

    public Candidature $candidature;

    public function __construct(Candidature $candidature)
    {
        $this->candidature = $candidature;
    }
}
