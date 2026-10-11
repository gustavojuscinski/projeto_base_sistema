<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\LoginController;

// Rotas públicas: não exigem login
Route::group([], function () {
    Route::get('/login', function () {
        if (session()->has('usuario')) {
            return redirect()->route('home');
        }

        return view('pages.login');
    })->name('login');

    Route::post('/login/autenticar', [LoginController::class, 'autenticar'])->name('login.autenticar');
});

// Rotas protegidas: exigem autenticação
Route::middleware(\App\Http\Middleware\VerificarAutenticacao::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::post('/ajax/testar', [AjaxController::class, 'testar'])->name('ajax.testar');

    Route::post('/logout', function () {
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});