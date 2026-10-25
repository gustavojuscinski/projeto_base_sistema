<?php

use Illuminate\Support\Facades\Route;
use Modules\FormaPagamento\Http\Controllers\FormaPagamentoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('formapagamentos', FormaPagamentoController::class)->names('formapagamento');
});
