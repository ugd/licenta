<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\BileteController;
use App\Http\Controllers\EvenimenteController;
use App\Http\Controllers\IntrariController;
use App\Http\Controllers\InvitatiiController;
use App\Http\Controllers\VendoriBileteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('register', [ApiAuthController::class, 'register'])->name('auth.register');
    Route::post('login', [ApiAuthController::class, 'login'])->name('auth.login');
    Route::middleware('auth:sanctum')->post('logout', [ApiAuthController::class, 'logout'])->name('auth.logout');
    Route::post('/check-token', [ApiAuthController::class, 'checkToken']);
    Route::middleware('auth:sanctum')->post('/logout-me', [ApiAuthController::class, 'logoutMe'])->name('auth.logoutMe');
});

Route::prefix('admin')->group(function () {
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->get('/users', [ApiAuthController::class, 'users'])->name('auth.users');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->post('/user', [ApiAuthController::class, 'addUser'])->name('auth.addUser');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->put('/user/{id}', [ApiAuthController::class, 'editUser'])->name('auth.editUser');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->post('/user/{id}/logout', [ApiAuthController::class, 'logoutUser'])->name('auth.logoutUser');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->get('/permisiuni', [ApiAuthController::class, 'permisiuni'])->name('auth.permisiuni');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->post('/permisiune', [ApiAuthController::class, 'addPermisiune'])->name('auth.addPermisiune');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->put('/permisiune/{id}', [ApiAuthController::class, 'editPermisiune'])->name('auth.editPermisiune');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->get('/events', [ApiAuthController::class, 'getEvents'])->name('auth.evenimente');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->get('/events/user/{id}', [IntrariController::class, 'getEventsForUser'])->name('auth.evenimenteForUser');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->post('/events/user/{id}/permissions', [IntrariController::class, 'updateEventPermissions'])->name('auth.updateEventPermissions');
    Route::middleware(['auth:sanctum', 'checkAdminPriv'])->post('/events/statistics', [EvenimenteController::class, 'getStatistics'])->name('auth.getEventStatistics');
    Route::prefix('events')->group(function () {
        Route::middleware('auth:sanctum')->get('/', [EvenimenteController::class, 'index'])->name('evenimente.index');
        Route::middleware('auth:sanctum')->get('/show/{id}', [EvenimenteController::class, 'show'])->name('evenimente.show');
        Route::middleware('auth:sanctum')->post('/create', [EvenimenteController::class, 'create'])->name('evenimente.create');
        Route::middleware('auth:sanctum')->get('/create/generateUUID', [EvenimenteController::class, 'generateUUID'])->name('evenimente.create.generateUUID');
        Route::middleware('auth:sanctum')->post('/upload', [EvenimenteController::class, 'uploadFilesToEvent'])->name('evenimente.uploadFilesToEvent');
        Route::middleware('auth:sanctum')->put('/{id}/update', [EvenimenteController::class, 'update'])->name('evenimente.update');
        Route::middleware('auth:sanctum')->post('/delete', [EvenimenteController::class, 'destroy'])->name('evenimente.destroy');
        Route::middleware('auth:sanctum')->get('/test', [EvenimenteController::class, 'test'])->name('evenimente.test');
    });
    Route::prefix('invitations')->group(function () {
        Route::middleware('auth:sanctum')->get('/', [InvitatiiController::class, 'index'])->name('invitatii.index');
        Route::middleware('auth:sanctum')->get('/getEvents', [InvitatiiController::class, 'getEventNames'])->name('invitatii.getEventNames');
        Route::middleware('auth:sanctum')->get('/getInviteTypes', [InvitatiiController::class, 'getInviteTypes'])->name('invitatii.getInviteTypes');
        Route::middleware('auth:sanctum')->put('/{id}/update', [InvitatiiController::class, 'update'])->name('invitatii.update');
        Route::middleware('auth:sanctum')->post('/create', [InvitatiiController::class, 'store'])->name('invitatii.create');
        Route::middleware('auth:sanctum')->post('/storeBatch', [InvitatiiController::class, 'storeBatch'])->name('invitatii.storeBatch');
        Route::middleware('auth:sanctum')->post('/sendMail', [InvitatiiController::class, 'sendMail'])->name('invitatii.sendMail');
        Route::middleware('auth:sanctum')->post('/sendBatchMail', [InvitatiiController::class, 'sendBatchMail'])->name('invitatii.sendBatchMail');
        Route::middleware('auth:sanctum')->delete('/{id}', [InvitatiiController::class, 'destroy'])->name('invitatii.destroy');
    });
    Route::prefix('tickets')->group(function () {
        Route::middleware('auth:sanctum')->get('/', [BileteController::class, 'index'])->name('intrari.index');
        Route::middleware('auth:sanctum')->put('/{id}/update', [BileteController::class, 'update'])->name('intrari.update');
        Route::middleware('auth:sanctum')->post('/storeBatch', [BileteController::class, 'storeBatch'])->name('intrari.storeBatch');
        Route::middleware('auth:sanctum')->delete('/{id}', [BileteController::class, 'destroy'])->name('intrari.destroy');
    });
    Route::prefix('vendors')->group(function () {
        Route::middleware('auth:sanctum')->get('/', [VendoriBileteController::class, 'index'])->name('vendors.index');
        Route::middleware('auth:sanctum')->post('/create', [VendoriBileteController::class, 'store'])->name('vendors.create');
        Route::middleware('auth:sanctum')->put('/{id}/update', [VendoriBileteController::class, 'update'])->name('vendors.update');
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('entrance')->group(function () {
    Route::middleware('auth:sanctum')->post('/scan', [IntrariController::class, 'apiScan'])->name('entrance.apiScan');
    Route::middleware(['auth:sanctum', 'checkLevel2Priv'])->post('/entry', [IntrariController::class, 'apiStore'])->name('entrance.apiStore');
    Route::middleware('auth:sanctum')->get('/eventsAccess', [IntrariController::class, 'eventsAccess'])->name('entrance.apiIndex');
    Route::middleware('auth:sanctum')->post('/event/addTicket/{id}', [IntrariController::class, 'addTicket'])->name('entrance.apiAddTicket');
    Route::middleware('auth:sanctum')->get('/events/showLocalTickets', [IntrariController::class, 'index'])->name('entrance.apiShowLocalTickets');
});

Route::get('/assets/{filename}', [AssetController::class, 'getAsset'])->name('file.getAsset');

Route::get('/apple-pass/{suffixPath}', [AssetController::class, 'getApplePass'])->name('file.getApplePass');
