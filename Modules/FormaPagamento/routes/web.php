<?php

use Illuminate\Support\Facades\Route;
use Modules\FormaPagamento\Http\Controllers\FormaPagamentoController;
use App\Http\Middleware\VerificarAutenticacao;

Route::middleware(VerificarAutenticacao::class)->group(function () {
    Route::get('/forma-pagamentos', [FormaPagamentoController::class, 'index'])->name('forma-pagamentos.index');
    Route::post('/forma-pagamentos/buscar', [FormaPagamentoController::class, 'buscarFormaPagamento'])->name('forma-pagamentos.buscar');
    Route::post('/inserir/forma-pagamento', [FormaPagamentoController::class, 'inserirFormaPagamento'])->name('forma-pagamentos.inserir');
    Route::post('/alterar/forma-pagamento', [FormaPagamentoController::class, 'alterarFormaPagamento'])->name('forma-pagamentos.alterar');
    Route::post('/inativar/forma-pagamento', [FormaPagamentoController::class, 'inativarFormaPagamento'])->name('forma-pagamentos.inativar');
});