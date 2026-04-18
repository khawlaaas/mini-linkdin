<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offre;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // GET /api/admin/users
    public function listUsers()
    {
        $users = User::with('profil')->get();
        return response()->json($users);
    }

    // DELETE /api/admin/users/{user}
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Impossible de supprimer un admin'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    // PATCH /api/admin/offres/{offre}
    public function toggleOffre(Offre $offre)
    {
        $offre->update(['actif' => !$offre->actif]);
        $etat = $offre->actif ? 'activée' : 'désactivée';
        return response()->json([
            'message' => "Offre {$etat}",
            'offre'   => $offre,
        ]);
    }
}
