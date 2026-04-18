<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Offre;

class OffreController extends Controller
{
    // GET /api/offres : List active offers with filters + pagination
    public function index(Request $request)
    {
        $query = Offre::where('actif', true)
                      ->with('recruteur:id,name,email') 
                      ->orderBy('created_at', 'desc');

        // Filter by localisation
        if ($request->filled('localisation')) {
            $query->where('localisation', 'like', '%' . $request->localisation . '%');
        }

        // Filter by type (CDI, CDD, stage)
        if ($request->filled('type')) {
            $request->validate(['type' => 'in:CDI,CDD,stage']);
            $query->where('type', $request->type);
        }

        // Paginate: 10 per page
        $offres = $query->paginate(10);

        return response()->json($offres);
    }

    // GET /api/offres/{offre} : Get one offer detail
    public function show(Offre $offre)
    {
        $offre->load('recruteur:id,name,email');
        return response()->json($offre);
    }

    // POST /api/offres : Create an offer (recruteur only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'description'  => 'required|string',
            'localisation' => 'nullable|string|max:255',
            'type'         => 'required|in:CDI,CDD,stage',
            'actif'        => 'nullable|boolean',
        ]);

        $offre = Offre::create(array_merge($validated, ['user_id' => Auth::id()]));

        return response()->json($offre, 201);
    }

    // PUT /api/offres/{offre} : Update an offer (owner only)
    public function update(Request $request, Offre $offre)
    {
        if ($offre->user_id !== Auth::id()) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $validated = $request->validate([
            'titre'        => 'sometimes|string|max:255',
            'description'  => 'sometimes|string',
            'localisation' => 'nullable|string|max:255',
            'type'         => 'sometimes|in:CDI,CDD,stage',
            'actif'        => 'nullable|boolean',
        ]);

        $offre->update($validated);

        return response()->json($offre);
    }

    // DELETE /api/offres/{offre} : Delete an offer (owner only)
    public function destroy(Offre $offre)
    {
        if ($offre->user_id !== Auth::id()) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $offre->delete();

        return response()->json(['message' => 'Offre supprimée avec succès.']);
    }
}
