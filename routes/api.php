<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\OldClientInfoController;


// Route for testing authenticated requests
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for clients
Route::post('/clients', [ClientController::class, 'store']);
Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/{clientId}', [ClientController::class, 'show']);
Route::delete('/clients/{clientId}', [ClientController::class, 'destroy']);

// Routes for transactions
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/{clientId}', [TransactionController::class, 'index']);

// Route for statistics
Route::get('/statistics', [StatisticController::class, 'index']);

// Route for old client info
Route::get('/old-clients/{oldClientId}', [OldClientInfoController::class, 'show']);



