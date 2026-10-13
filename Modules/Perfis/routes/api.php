<?php

use Illuminate\Support\Facades\Route;
use Modules\Perfis\Http\Controllers\PerfisController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('perfis', PerfisController::class)->names('perfis');
});
