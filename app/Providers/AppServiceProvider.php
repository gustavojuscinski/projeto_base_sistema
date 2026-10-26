<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $permissao) {
            if (!str_starts_with($permissao, 'WEB_')) {
                return null;
            }

            $idUsuario = (int) $user->ID_USUARIO;
            $nomePermissao = DB::getPdo()->quote($permissao);

            $query = "SELECT permissao_usuario.ID_CODIGO
                        FROM permissao_usuario
                        INNER JOIN permissoes
                                ON permissoes.ID_CODIGO = permissao_usuario.ID_PERMISSAO
                       WHERE permissao_usuario.ID_USUARIO = $idUsuario
                         AND permissao_usuario.STATUS = 'A'
                         AND permissoes.STATUS = 'A'
                         AND permissoes.NOME = $nomePermissao
                       LIMIT 1";

            $result = executarSQL($query);

            return !empty($result);
        });
    }
}
