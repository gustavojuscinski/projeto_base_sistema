<?php

namespace Modules\Equipamentos\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class EquipamentosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'equipamentos'
        );

        Route::middleware('web')
            ->group(__DIR__ . '/../../routes/web.php');
    }
}