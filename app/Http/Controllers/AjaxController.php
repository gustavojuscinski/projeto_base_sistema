<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function testar(Request $request): JsonResponse
    {
        $query = ' SELECT DATABASE() AS banco,
                     NOW() AS data_hora
        ';

        $resultado = executarSQL($query);

        return exibirRetorno($resultado, true, 'Consulta SQL executada com sucesso!');
    }
}