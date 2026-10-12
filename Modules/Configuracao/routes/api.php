<?php

use Illuminate\Support\Facades\Route;
use Modules\Configuracao\Http\Controllers\ConfiguracaoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('configuracaos', ConfiguracaoController::class)->names('configuracao');
});
