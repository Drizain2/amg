<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrancheController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockMovementController;
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
    Route::apiResource('user-compagnie', UserController::class);


    // Produits
    Route::apiResource('product', ProductController::class);

    // Mouvements de stock
    // GET  /api/mouvement              → historique filtré par rôle (avec ?stock_id=, ?type=, ?branche_id=)
    // POST /api/mouvement              → créer in / out / adjustment
    // GET  /api/mouvement/{id}         → détail d'un mouvement
    // POST /api/mouvement/transfert    → transfert inter-branches
    Route::get('mouvement',                [StockMovementController::class, 'index']);
    Route::post('mouvement',               [StockMovementController::class, 'store']);
    Route::post('mouvement/transfert',     [StockMovementController::class, 'transfert']);
    Route::get('mouvement/{stockMovement}',[StockMovementController::class, 'show']);


});

