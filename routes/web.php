<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProcessController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return redirect('landing-raspadita');
});
Route::get('/participate/{ciNumber}', [MainController::class, 'findParticipate']);

Route::redirect('/', '/admin');
Route::get('/el-mundial-se-vive-en-casa', [MainController::class, 'showElMundialSeViveEnCasa']);
Route::get('/form-kernig', [MainController::class, 'showFormKernev']);
Route::get('/coupons', [MainController::class, 'showFormKernevTickets']);
Route::get('/coupons-mundial', [MainController::class, 'showFormKernevTicketsMundial']);
Route::post('/upload-kernig-form', [ProcessController::class, 'uploadKernevForm']);
Route::post('/check-coupons', [ProcessController::class, 'checkCoupons']);
Route::post('/check-coupons-mundial', [ProcessController::class, 'checkCouponsMundial']);
Route::post('/validate-coupon', [ProcessController::class, 'postValidateCoupon']);
Route::get('/landing-raspadita', [MainController::class, 'showLanding']);
Route::get('/landing-ticket-dorado', [MainController::class, 'showLandingTicketDorado']);
Route::get('/form-raspadita', [MainController::class, 'showForm']);
Route::get('/consultar-raspadita', [MainController::class, 'showConsultarRaspadita']);
Route::get('/registra-tus-compras', [MainController::class, 'showRegistraTusCompras']);
Route::post('/registra-tus-compras', [ProcessController::class, 'postRegistraTusCompras']);
Route::post('/update-coupon', [ProcessController::class, 'postUpdateCoupon']);
Route::post('/scratch-form', [ProcessController::class, 'postScratchForm']);

Route::get('/test-queries', [MainController::class, 'findTestQueries']);

// Route::middleware(['auth'])->prefix('admin')->group(function() {
//     Route::get('/', function() {
//         return redirect('/admin/node-list/home-banner');
//     });
//     Route::get('dashboard', function() {
//         return redirect('/admin/node-list/home-banner');
//     });
//     // Route::view('dashboard', 'content.admin.dashboard')->name('dashboard');
//     Route::view('account', 'content.admin.account')->name('admin-account');
//     Route::view('example', 'content.admin.example')->name('example');

//         /* LISTADOS Y CRUDS */
//     Route::get('list/contact',     [AdminController::class, 'listContact']);
//     Route::get('list/about',       [AdminController::class, 'listAbout']);
//     Route::get('sync-properties',  [AdminController::class, 'syncProperties']);

//     /* LISTADOS Y CRUDS */
//     Route::get('/node-list/{node}/{paginate?}/{excel?}',    [MainController::class,    'getNodeList']);
//     Route::get('/node/{node}/{action}',                     [MainController::class,    'getNodeAction'])->where('action', 'create')->name('form-create');
//     Route::get('/node/{node}/{action}/{id}',                [MainController::class,    'getNodeAction'])->where('action', 'read|edit|delete')->name('form-crud');
//     Route::post('/node/{node}',                             [ProcessController::class, 'postNodeAction'])->name('form');

// });

// Route::group(['prefix'=>'auth'], function(){
//     Route::get('/', function() {
//         return redirect('/auth/login');
//     });
//     Route::get('/logout', function() {
//         \Auth::logout();
// 		return redirect('admin');
//     });
// });

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    // return what you want
});

Auth::routes();

require __DIR__.'/auth.php';
