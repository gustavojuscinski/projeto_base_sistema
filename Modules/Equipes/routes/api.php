<?php

use Illuminate\Support\Facades\Route;
use Modules\Equipes\Http\Controllers\EquipesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('equipes', EquipesController::class)->names('equipes');
});
