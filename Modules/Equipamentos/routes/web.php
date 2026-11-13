<?php

use Illuminate\Support\Facades\Route;
use Modules\Equipamentos\Http\Controllers\EquipamentosController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/equipamentos', [EquipamentosController::class, 'index'])->name('equipamentos.index');
    Route::post('/buscar/equipamentos', [EquipamentosController::class, 'buscarEquipamentos'])->name('equipamentos.buscar');
    Route::post('/inserir/equipamento', [EquipamentosController::class, 'inserirEquipamento'])->name('equipamento.inserir');
    Route::post('/alterar/equipamento', [EquipamentosController::class, 'alterarEquipamento'])->name('equipamento.alterar');
    Route::post('/inativar/equipamento', [EquipamentosController::class, 'inativarEquipamento'])->name('equipamento.inativar');
});