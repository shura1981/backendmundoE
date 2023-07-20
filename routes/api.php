<?php

use App\Http\Controllers\UsuarioController;
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

Route::get('/usuarios', [UsuarioController::class, 'index']);// devuelve todos los usuarios
Route::get('/mensajes', [UsuarioController::class, 'selectAllMessages']); // devuelve todos los mensajes
Route::post('/usuarios', [UsuarioController::class, 'store']); // crea un usuario
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']); // devuelve un usuario
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']); // actualiza un usuario
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);// elimina un usuario