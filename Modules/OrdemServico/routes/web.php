<?php

use Illuminate\Support\Facades\Route;
use Modules\OrdemServico\Http\Controllers\OrdemServicoController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/ordem-servico', [OrdemServicoController::class, 'index'])->name('ordem-servico.index');
    Route::post('/buscar/ordens-servico', [OrdemServicoController::class, 'buscarOrdensServico'])->name('ordem-servico.buscar');
    Route::post('/inserir/ordem-servico', [OrdemServicoController::class, 'inserirOrdemServico'])->name('ordem-servico.inserir');
    Route::post('/alterar/ordem-servico', [OrdemServicoController::class, 'alterarOrdemServico'])->name('ordem-servico.alterar');
    Route::post('/inativar/ordem-servico', [OrdemServicoController::class, 'inativarOrdemServico'])->name('ordem-servico.inativar');
    Route::post('/buscar/clientes/ordem-servico', [OrdemServicoController::class, 'buscarClientes'])->name('ordem-servico.clientes.buscar');
    Route::post('/buscar/equipamentos/ordem-servico', [OrdemServicoController::class, 'buscarEquipamentos'])->name('ordem-servico.equipamentos.buscar');
    Route::get('/ordem-servico/{idCodigo}/impresso', [OrdemServicoController::class, 'imprimirOrdemServico'])->whereNumber('idCodigo')->name('ordem-servico.imprimir');
});
