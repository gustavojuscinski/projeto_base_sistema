<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

if (!function_exists('exibirRetorno')) {
    function exibirRetorno(
        mixed $dados = null,
        bool $sucesso = true,
        string $mensagem = '',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'sucesso' => $sucesso,
            'mensagem' => $mensagem,
            'dados' => $dados,
        ], $status);
    }
}

if (!function_exists('executarSQL')) {
    function executarSQL(string $query, array $parametros = []): mixed
    {
        $tipoSQL = strtoupper(
            strtok(ltrim($query), " \t\n\r")
        );

        return match ($tipoSQL) {
            'SELECT' => DB::select($query, $parametros),
            'INSERT' => DB::insert($query, $parametros),
            'UPDATE' => DB::update($query, $parametros),
            'DELETE' => DB::delete($query, $parametros),

            default => throw new InvalidArgumentException(
                'Tipo de comando SQL não suportado.'
            ),
        };
    }
}

if (!function_exists('usuarioAutenticado')) {
    function usuarioAutenticado(): ?array
    {
        return session('usuario');
    }
}   
