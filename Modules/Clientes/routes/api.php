<?php

use Illuminate\Support\Facades\Route;
use Modules\Clientes\Http\Controllers\ClientesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('clientes', ClientesController::class)->names('clientes');
});
