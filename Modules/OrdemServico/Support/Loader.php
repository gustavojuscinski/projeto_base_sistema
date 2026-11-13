<?php

namespace Modules\OrdemServico\Support;

use Barryvdh\DomPDF\Facade\Pdf;

class Loader
{
    public static function PDF(string $view, array $dados)
    {
        return Pdf::loadView($view, $dados)->setPaper('a4', 'portrait');
    }
}
