<?php

namespace Modules\Clientes\Providers;

use Illuminate\Support\ServiceProvider;

class ClientesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'clientes');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
