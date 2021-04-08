<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\TestSquareController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [GameController::class, 'index'])->name('game.index');
Route::get('/create', [GameController::class, 'create'])->name('game.create');
Route::post('/create', [GameController::class, 'store'])->name('game.store');
Route::get('/{game}', [GameController::class, 'show'])->name('game.show');


