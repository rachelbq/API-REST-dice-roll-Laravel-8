<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Passport protected routes
Route::middleware(['auth:api'])->group(function () {

    // All users:
    Route::post('logout', [AuthController::class, 'logout']);

    // Players:
    Route::put('players/{id}', [UserController::class, 'editNickname']);
    Route::post('players/{id}', [PlayController::class, 'diceRoll']);
    Route::get('players/{id}', [PlayController::class, 'getOwnPlays']);
    Route::delete('players/{id}', [PlayController::class, 'removeOwnPlays']);

    // Administrators:
    Route::get('players', [PlayController::class, 'allPlayersInfo']);
    Route::get('players/ranking', [PlayController::class, 'rankingAverage']);
    Route::get('players/loser', [PlayController::class, 'loserPlayer']);
    Route::get('players/winner', [PlayController::class, 'winnerPlayer']);
});
