<?php

use Illuminate\Support\Facades\Route;
use Modules\Perfis\Http\Controllers\PerfisController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/perfis', [PerfisController::class, 'index'])->name('perfis.index');
    Route::post('/perfis/buscar', [PerfisController::class, 'buscarPerfis'])->name('perfis.buscar');
    Route::post('/inserir/perfil', [PerfisController::class, 'inserirPerfil'])->name('perfil.inserir');
    Route::post('/alterar/perfil', [PerfisController::class, 'alterarPerfil'])->name('perfil.alterar');
    Route::post('/inativar/perfil', [PerfisController::class, 'inativarPerfil'])->name('perfil.inativar');
});
