<?php

use Illuminate\Support\Facades\Route;
use Modules\OrdemServico\Http\Controllers\OrdemServicoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ordemservicos', OrdemServicoController::class)->names('ordemservico');
});
