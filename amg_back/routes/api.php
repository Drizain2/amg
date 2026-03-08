<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrancheController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// ─── Auth (public) ───────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ─── Routes protégées ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('product', ProductController::class);

    // User courant
    Route::get('/user', fn(Request $request) => $request->user());

    // Branches — CRUD (admin) / lecture filtrée (manager, operator)
    Route::apiResource('branche', BrancheController::class);

        // Users de la compagnie — gestion par l'admin
    // GET    /api/user-compagnie         → liste les users de la compagnie
    // POST   /api/user-compagnie         → inviter un user (manager ou operator)
    // GET    /api/user-compagnie/{id}    → détail d'un user
    // PUT    /api/user-compagnie/{id}    → modifier rôle et/ou branches
    // DELETE /api/user-compagnie/{id}    → retirer un user
    // Route::apiResource('user-compagnie', UserCon::class);


    // Produits
    Route::apiResource('product', ProductController::class);


});

