<?php

use Illuminate\Support\Facades\Route;
use Modules\Usuarios\Http\Controllers\UsuariosController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('usuarios', UsuariosController::class)->names('usuarios');
});
