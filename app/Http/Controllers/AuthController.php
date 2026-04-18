<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'in:candidat,recruteur',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'role'     => $data['role'] ?? 'candidat',
        ]);

        $token = auth()->login($user);

        return response()->json([
            'message' => 'Inscription réussie',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user'    => auth()->user(),
            'token'   => $token,
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
