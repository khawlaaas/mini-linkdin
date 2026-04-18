<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profil;
use App\Models\Competence;

class ProfilController extends Controller
{
    // POST /api/profil — Create profile (only once)
    public function store(Request $request)
    {
        $user = Auth::user();

        // Prevent creating a second profile
        if (Profil::where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Profil déjà existant.'], 422);
        }

        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'bio'          => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'disponible'   => 'nullable|boolean',
        ]);

        $profil = new Profil($validated);
        $profil->user_id = $user->id;
        $profil->save();

        return response()->json($profil, 201);
    }

    // GET /api/profil — View own profile
    public function show()
    {
        $profil = Profil::where('user_id', Auth::id())->first();

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé.'], 404);
        }

        // Load the competences with their pivot data (niveau)
        $profil->load('competences');

        return response()->json($profil);
    }

    // PUT /api/profil — Update own profile
    public function update(Request $request)
    {
        $profil = Profil::where('user_id', Auth::id())->first();

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé.'], 404);
        }

        $validated = $request->validate([
            'titre'        => 'sometimes|string|max:255',
            'bio'          => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'disponible'   => 'nullable|boolean',
        ]);

        $profil->update($validated);

        return response()->json($profil);
    }

    // POST /api/profil/competences — Add a skill to profile
    public function addCompetence(Request $request)
    {
        $profil = Profil::where('user_id', Auth::id())->first();

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé.'], 404);
        }

        $validated = $request->validate([
            'competence_id' => 'required|exists:competences,id',
            'niveau'        => 'required|in:débutant,intermédiaire,expert',
        ]);

        // Prevent duplicate competence on the same profile
        if ($profil->competences()->where('competence_id', $validated['competence_id'])->exists()) {
            return response()->json(['message' => 'Compétence déjà ajoutée.'], 422);
        }

        $profil->competences()->attach($validated['competence_id'], [
            'niveau' => $validated['niveau'],
        ]);

        return response()->json(['message' => 'Compétence ajoutée avec succès.'], 201);
    }

    // DELETE /api/profil/competences/{competence} — Remove a skill
    public function removeCompetence(Competence $competence)
    {
        $profil = Profil::where('user_id', Auth::id())->first();

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé.'], 404);
        }

        $profil->competences()->detach($competence->id);

        return response()->json(['message' => 'Compétence retirée avec succès.']);
    }
}
