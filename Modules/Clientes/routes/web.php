<?php

use Illuminate\Support\Facades\Route;
use Modules\Clientes\Http\Controllers\ClientesController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
    Route::post('/buscar/clientes', [ClientesController::class, 'buscarClientes'])->name('clientes.buscar');
    Route::post('/inserir/cliente', [ClientesController::class, 'inserirCliente'])->name('cliente.inserir');
    Route::post('/alterar/cliente', [ClientesController::class, 'alterarCliente'])->name('cliente.alterar');
    Route::post('/inativar/cliente', [ClientesController::class, 'inativarCliente'])->name('cliente.inativar');
});
