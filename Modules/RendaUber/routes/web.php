<?php

use Illuminate\Support\Facades\Route;
use Modules\RendaUber\Http\Controllers\RendaUberController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/renda-uber', [RendaUberController::class, 'index'])->name('rendauber.index');
    Route::post('/renda-uber/buscar', [RendaUberController::class, 'buscarRendas'])->name('rendauber.buscar');
    Route::post('/inserir/renda-uber', [RendaUberController::class, 'inserirRenda'])->name('rendauber.inserir');
    Route::post('/alterar/renda-uber', [RendaUberController::class, 'alterarRenda'])->name('rendauber.alterar');
    Route::post('/inativar/renda-uber', [RendaUberController::class, 'inativarRenda'])->name('rendauber.inativar');

    Route::post('/renda-uber/buscar-forma-pagamento', [RendaUberController::class, 'buscarFormaPagamento'])->name('rendauber.buscar.formapagamento');
});
