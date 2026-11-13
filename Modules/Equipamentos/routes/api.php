<?php

use Illuminate\Support\Facades\Route;
use Modules\Equipamentos\Http\Controllers\EquipamentosController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('equipamentos', EquipamentosController::class)->names('equipamentos');
});
