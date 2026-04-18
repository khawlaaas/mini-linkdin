<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\OffreController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::middleware('isAdmin')->prefix('admin')->group(function () {
        Route::get('/users',            [AdminController::class, 'listUsers']);
        Route::delete('/users/{user}',  [AdminController::class, 'deleteUser']);
        Route::patch('/offres/{offre}', [AdminController::class, 'toggleOffre']);
    });

    Route::middleware('isCandidat')->group(function () {
        Route::post('/profil', [ProfilController::class, 'store']);
        Route::get('/profil', [ProfilController::class, 'show']);
        Route::put('/profil', [ProfilController::class, 'update']);
        Route::post('/profil/competences', [ProfilController::class, 'addCompetence']);
        Route::delete('/profil/competences/{competence}', [ProfilController::class, 'removeCompetence']);
    });
});
