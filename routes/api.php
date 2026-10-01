<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Middleware\ValidateCustomHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([ValidateCustomHeader::class])->group(function () {
    Route::post('/register-participant', [AppController::class, 'registerParticipant']);
    Route::get('/participant/{document}', [AppController::class, 'getDataParticipant']);
});

Route::get('/get-prices', [AppController::class, 'getPrices']);
Route::post('/update-winner', [AppController::class, 'postUpdateWinner']);
Route::get('/get-json-participants', [AppController::class, 'getJsonParticipants']);
Route::get('/get-winners/{price_id}', [AppController::class, 'getWinnersByPrice']);
