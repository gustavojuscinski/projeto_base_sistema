<?php

use Illuminate\Support\Facades\Route;
use Modules\Equipes\Http\Controllers\EquipesController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/equipes', [EquipesController::class, 'index'])->name('equipes.index');
    Route::post('/equipes/buscar', [EquipesController::class, 'buscarEquipes'])->name('equipes.buscar');
    Route::post('/inserir/equipe', [EquipesController::class, 'inserirEquipe'])->name('equipe.inserir');
    Route::post('/alterar/equipe', [EquipesController::class, 'alterarEquipe'])->name('equipe.alterar');
    Route::post('/inativar/equipe', [EquipesController::class, 'inativarEquipe'])->name('equipe.inativar');
});
