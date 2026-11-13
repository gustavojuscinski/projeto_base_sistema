<?php

namespace Modules\OrdemServico\Providers;

use Illuminate\Support\ServiceProvider;

class OrdemServicoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ordemservico');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
