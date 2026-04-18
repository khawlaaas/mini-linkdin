<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Offre;
use App\Events\CandidatureDeposee;
use App\Events\StatutCandidatureMis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    // POST /api/offres/{offre}/candidater
    public function postuler(Request $request, Offre $offre)
    {
        $user  = Auth::user();
        $profil = $user->profil;

        if (!$profil) {
            return response()->json(['message' => 'Vous devez créer un profil avant de postuler.'], 422);
        }

        if (!$offre->actif) {
            return response()->json(['message' => 'Cette offre n\'est plus active.'], 422);
        }

        $existe = Candidature::where('offre_id', $offre->id)
            ->where('profil_id', $profil->id)
            ->exists();
        if ($existe) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 422);
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        $candidature = Candidature::create([
            'offre_id'  => $offre->id,
            'profil_id' => $profil->id,
            'message'   => $request->message,
            'statut'    => 'en_attente',
        ]);

        event(new CandidatureDeposee($candidature));

        return response()->json($candidature->load(['offre', 'profil']), 201);
    }

    // GET /api/mes-candidatures
    public function mesCandidatures()
    {
        $profil = Auth::user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé.'], 404);
        }

        $candidatures = Candidature::where('profil_id', $profil->id)
            ->with(['offre'])
            ->latest()
            ->get();

        return response()->json($candidatures);
    }

    // GET /api/offres/{offre}/candidatures
    public function candidaturesDeLOffre(Offre $offre)
    {
        $user = Auth::user();

        // uses recruteur() relation name from Offre model
        if ($offre->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $candidatures = Candidature::where('offre_id', $offre->id)
            ->with(['profil.user', 'profil.competences'])
            ->latest()
            ->get();

        return response()->json($candidatures);
    }

    // PATCH /api/candidatures/{candidature}/statut
    public function changerStatut(Request $request, Candidature $candidature)
    {
        $user = Auth::user();

        // load the offre then check its recruteur
        if ($candidature->offre->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee',
        ]);

        $ancienStatut = $candidature->statut;
        $candidature->update(['statut' => $request->statut]);

        event(new StatutCandidatureMis($candidature, $ancienStatut, $request->statut));

        return response()->json($candidature->fresh());
    }
}
