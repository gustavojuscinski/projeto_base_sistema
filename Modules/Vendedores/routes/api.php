<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendedores\Http\Controllers\VendedoresController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('vendedores', VendedoresController::class)->names('vendedores');
});
