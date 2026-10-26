<?php

use Illuminate\Support\Facades\Route;
use Modules\Filiais\Http\Controllers\FiliaisController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/filiais', [FiliaisController::class, 'index'])->name('filiais.index');
    Route::post('/filiais/buscar', [FiliaisController::class, 'buscarFiliais'])->name('filiais.buscar');
    Route::post('/inserir/filial', [FiliaisController::class, 'inserirFilial'])->name('filiais.inserir');
    Route::post('/alterar/filial', [FiliaisController::class, 'alterarFilial'])->name('filiais.alterar');
    Route::post('/inativar/filial', [FiliaisController::class, 'inativarFilial'])->name('filiais.inativar');
});
