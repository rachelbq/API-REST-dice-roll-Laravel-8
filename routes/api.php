<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PlayController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('players', [AuthController::class, 'register'])->name('register');
Route::post('players/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Passport protected routes
Route::middleware(['auth:api'])->group(function () {

    // All users:
    Route::post('players/logout', [AuthController::class, 'logout'])->name('logout');
    Route::put('players/{id}', [UserController::class, 'editNickname'])->name('editNickname');
    Route::post('players/{id}/games', [PlayController::class, 'diceRoll'])->name('diceRoll');
    Route::get('players/{id}/games', [PlayController::class, 'getOwnPlays'])->name('getOwnPlays');
    Route::delete('players/{id}/games', [PlayController::class, 'removeOwnPlays'])->name('removeOwnPlays');

    // Administrators only:
    Route::get('players', [UserController::class, 'allPlayersInfo'])->name('allPlayersInfo');
    Route::get('players/ranking', [PlayController::class, 'rankingAverage'])->name('rankingAverage');
    Route::get('players/ranking/loser', [PlayController::class, 'loserPlayer'])->name('loserPlayer');
    Route::get('players/ranking/winner', [PlayController::class, 'winnerPlayer'])->name('winnerPlayer');
});
