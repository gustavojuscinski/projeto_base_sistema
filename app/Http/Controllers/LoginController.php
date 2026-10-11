<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function autenticar(Request $request): JsonResponse{
        $usuario = trim($request->input('inputUsuario', ''));
        $senha = $request->input('senhaUsuario', '');

        if ($usuario === '' || $senha === '') {
            return exibirRetorno(null, false, 'Informe o usuário e a senha.', 422);
        }

        $query = "SELECT ID_USUARIO
                       , NOME_USUARIO
                       , ID_PERFIL
                       , ID_EQUIPE            
                   FROM usuarios
                  WHERE NOME_USUARIO = ?
                    AND SENHA = ?
                    AND STATUS = 'A'
                    LIMIT 1";

        $resultado = executarSQL($query, [$usuario,
            $senha,]);

        if (empty($resultado)) {
            return exibirRetorno(null, false, 'Usuário ou senha inválidos.', 401);
        }

        $dadosUsuario = $resultado[0];

        session()->regenerate();

        session()->put('usuario', [
            'ID_USUARIO' => $dadosUsuario->ID_USUARIO,
            'NOME_USUARIO' => $dadosUsuario->NOME_USUARIO,
            'ID_PERFIL' => $dadosUsuario->ID_PERFIL,
            'ID_EQUIPE' => $dadosUsuario->ID_USUARIO,
        ]);

        return exibirRetorno(['redirect' => route('home'), 'bemVindo' => $dadosUsuario->NOME_USUARIO,], true, 'Login realizado com sucesso!');
    }
}