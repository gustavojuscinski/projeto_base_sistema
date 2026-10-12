<?php

use Illuminate\Support\Facades\Route;
use Modules\Configuracao\Http\Controllers\ConfiguracaoController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
});
