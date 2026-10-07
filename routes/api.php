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

// Endpoints públicos para validación, ruleta y formulario de mercado informal (con Rate Limit por IP)
Route::middleware([\App\Http\Middleware\RateLimitByIp::class.':60,1'])->group(function () {
    Route::get('/check-code/{code}', [AppController::class, 'checkCode']);
    Route::post('/mark-as-used', [AppController::class, 'markAsUsed'])->middleware(\App\Http\Middleware\RateLimitByIp::class.':30,1');
    Route::post('/informal-market-form', [AppController::class, 'informalMarketForm'])->middleware(\App\Http\Middleware\RateLimitByIp::class.':15,1');
});

