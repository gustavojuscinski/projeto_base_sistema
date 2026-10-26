<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendedores\Http\Controllers\VendedoresController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/vendedores', [VendedoresController::class, 'index'])->name('vendedores.index');
    Route::post('/vendedores/buscar', [VendedoresController::class, 'buscarVendedores'])->name('vendedores.buscar');
    Route::post('/inserir/vendedor', [VendedoresController::class, 'inserirVendedor'])->name('vendedores.inserir');
    Route::post('/alterar/vendedor', [VendedoresController::class, 'alterarVendedor'])->name('vendedores.alterar');
    Route::post('/inativar/vendedor', [VendedoresController::class, 'inativarVendedor'])->name('vendedores.inativar');

    Route::post('/vendedores/horarios/buscar', [VendedoresController::class, 'buscarHorariosVendedor'])->name('vendedores.horarios.buscar');
    Route::post('/vendedores/horarios/inserir', [VendedoresController::class, 'inserirHorarioVendedor'])->name('vendedores.horarios.inserir');
    Route::post('/vendedores/horarios/inativar', [VendedoresController::class, 'inativarHorarioVendedor'])->name('vendedores.horarios.inativar');

    Route::post('/vendedores/usuarios/buscar', [VendedoresController::class, 'buscarUsuarios'])->name('vendedores.usuarios.buscar');
    Route::post('/vendedores/filiais/buscar', [VendedoresController::class, 'buscarFiliais'])->name('vendedores.filiais.buscar');
});
