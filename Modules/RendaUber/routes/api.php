<?php

use Illuminate\Support\Facades\Route;
use Modules\RendaUber\Http\Controllers\RendaUberController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rendaubers', RendaUberController::class)->names('rendauber');
});
