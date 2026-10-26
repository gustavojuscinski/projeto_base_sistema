<?php

use Illuminate\Support\Facades\Route;
use Modules\Filiais\Http\Controllers\FiliaisController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('filiais', FiliaisController::class)->names('filiais');
});
