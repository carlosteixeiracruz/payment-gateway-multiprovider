<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

// --- Rotas Públicas ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Rotas Protegidas ---
Route::middleware('auth:sanctum')->group(function () {

    // Lista o histórico de compras do usuário logado
    Route::get('/transactions', function () {
        return \App\Models\Transaction::where('user_id', auth()->id())->get();
    });

    Route::post('/purchase', [PaymentController::class, 'purchase']);

    Route::get('/teste-auth', function () {
        return response()->json([
            'message' => 'Você está autenticado!',
            'user' => auth()->user()
        ]);
    });
});

// Webhook permanece fora (público)
Route::post('/webhook/{provider}', [PaymentController::class, 'webhook']);
