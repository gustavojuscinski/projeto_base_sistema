<?php

use Illuminate\Support\Facades\Route;
use Modules\Usuarios\Http\Controllers\UsuariosController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
    Route::post('/buscar/usuarios', [UsuariosController::class, 'buscarUsuarios'])->name('usuarios.buscar');
    Route::post('/inserir/usuario', [UsuariosController::class, 'inserirUsuario'])->name('usuario.inserir');
    Route::post('/alterar/usuario', [UsuariosController::class, 'alterarUsuario'])->name('usuario.alterar');
    Route::post('/inativar/usuario', [UsuariosController::class, 'inativarUsuario'])->name('usuario.inativar');

    Route::post('/buscar/equipes', [UsuariosController::class, 'buscarEquipes'])->name('usuarios.equipes.buscar');
    Route::post('/buscar/perfis', [UsuariosController::class, 'buscarPerfis'])->name('usuarios.perfis.buscar');

    Route::post('/buscar/permissoes/usuario', [UsuariosController::class, 'buscarPermissoesUsuario'])->name('usuarios.permissoes.buscar');
    Route::post('/alterar/permissoes/usuario', [UsuariosController::class, 'alterarPermissoesUsuario'])->name('usuarios.permissoes.alterar');
});