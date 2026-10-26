<?php

namespace Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function index(){
        return view('usuarios::index');
    }

    public function buscarUsuarios(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroUsuario = '';
        $filtroLimite = '';

        if (isset($dadosRecebidos['ID_USUARIO']) && $dadosRecebidos['ID_USUARIO'] !== '') {
            $filtroUsuario = "AND ID_USUARIO = " . (int) $dadosRecebidos['ID_USUARIO'];
        }

        if (!empty($dadosRecebidos['FILTRO_BUSCA'])) {
            // quote() escapa o texto digitado e já coloca as aspas
            $termoBusca = DB::getPdo()->quote('%' . $dadosRecebidos['FILTRO_BUSCA'] . '%');
            $filtroBusca = "AND NOME_USUARIO LIKE $termoBusca";
        }

        if(isset($dadosRecebidos['INATIVOS']) && $dadosRecebidos['INATIVOS'] == 1){
            $filtroStatus = "AND STATUS = 'I'";
        } else {
            $filtroStatus = "AND STATUS = 'A'";
        }

        $colunasOrdenaveis = ['ID_USUARIO', 'NOME_USUARIO', 'DESCRICAO_PERFIL', 'DESCRICAO_EQUIPE', 'DT_HORA_CADASTRO'];
        $filtroOrderBy = "ORDER BY NOME_USUARIO, ID_USUARIO";

        if (!empty($dadosRecebidos['ORDENACAO_LISTA'])) {
            $itensOrdenacao = [];

            foreach (explode(',', $dadosRecebidos['ORDENACAO_LISTA']) as $item) {
                $partes = preg_split('/\s+/', trim($item));
                $coluna = $partes[0] ?? '';
                $direcao = strtoupper($partes[1] ?? 'ASC');

                if (in_array($coluna, $colunasOrdenaveis, true) && in_array($direcao, ['ASC', 'DESC'], true)) {
                    $itensOrdenacao[] = "$coluna $direcao";
                }
            }

            if (!empty($itensOrdenacao)) {
                // ID_USUARIO no final mantém a ordem estável entre as páginas
                $filtroOrderBy = "ORDER BY " . implode(', ', $itensOrdenacao) . ", ID_USUARIO";
            }
        }

        // PAGINAÇÃO: só pagina quando a tela envia POR_PAGINA (outras telas continuam como antes)
        if (isset($dadosRecebidos['POR_PAGINA'])) {
            $porPagina = (int) $dadosRecebidos['POR_PAGINA'];
            $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;

            $pagina = max(1, (int) ($dadosRecebidos['PAGINA'] ?? 1));
            $deslocamento = ($pagina - 1) * $porPagina;

            $filtroLimite = "LIMIT $porPagina OFFSET $deslocamento";
        }

        $query = "SELECT ID_USUARIO
                       , NOME_USUARIO
                       , ID_PERFIL
                       , ID_EQUIPE
                       , DT_HORA_CADASTRO
                       , SENHA
                       , STATUS
                       , (SELECT DESCRICAO
                           FROM perfis
                          WHERE perfis.ID_CODIGO = usuarios.ID_PERFIL) AS DESCRICAO_PERFIL
                       , (SELECT DESCRICAO
                           FROM equipe
                          WHERE equipe.ID_CODIGO = usuarios.ID_EQUIPE) AS DESCRICAO_EQUIPE
                       , (SELECT COUNT(*)
                            FROM usuarios
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroUsuario) AS TOTAL_REGISTROS
                    FROM usuarios
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroUsuario
                   $filtroOrderBy
                   $filtroLimite";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Usuários buscados com sucesso.');
    }

    public function inserirUsuario(Request $request){
        $dadosRecebidos = $request->except('_token');
        $nomeUsuario = $dadosRecebidos['NOME_USUARIO'];
        $senhaUsuario = $dadosRecebidos['SENHA_USUARIO'];
        $perfilUsuario = $dadosRecebidos['ID_PERFIL'];
        $equipeUsuario = $dadosRecebidos['ID_EQUIPE'];

        $query = "INSERT INTO usuarios (NOME_USUARIO
                                      , SENHA
                                      , ID_PERFIL
                                      , ID_EQUIPE
                                      , DT_HORA_CADASTRO
                        ) VALUES ('$nomeUsuario'
                                , '$senhaUsuario'
                                , '$perfilUsuario'
                                , '$equipeUsuario'
                                , NOW())";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Usuário inserido com sucesso!');
    }

    public function alterarUsuario(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idUsuario = $dadosRecebidos['ID_USUARIO'];
        $nomeUsuario = $dadosRecebidos['NOME_USUARIO'];
        $senhaUsuario = $dadosRecebidos['SENHA_USUARIO'];
        $perfilUsuario = $dadosRecebidos['ID_PERFIL'];
        $equipeUsuario = $dadosRecebidos['ID_EQUIPE'];

        $query = "UPDATE usuarios
                     SET NOME_USUARIO = '$nomeUsuario'
                       , SENHA = '$senhaUsuario'
                       , ID_PERFIL = '$perfilUsuario'
                       , ID_EQUIPE = '$equipeUsuario'
                   WHERE ID_USUARIO = $idUsuario";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Usuário alterado com sucesso!');
    }

    public function inativarUsuario(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idUsuario = $dadosRecebidos['ID_USUARIO'];

        $query = "UPDATE usuarios
                     SET STATUS = 'I'
                   WHERE ID_USUARIO = $idUsuario";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Usuário inativado com sucesso!');
    }

    public function buscarEquipes(Request $request){
        $dadosRecebidos = $request->except('_token');

        $query = "SELECT ID_CODIGO
                       , DESCRICAO
                    FROM equipe
                   WHERE STATUS = 'A'
                   ORDER BY DESCRICAO";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Equipes buscadas com sucesso.');
    }

    public function buscarPerfis(Request $request){
        $dadosRecebidos = $request->except('_token');

        $query = "SELECT ID_CODIGO
                       , DESCRICAO
                    FROM perfis
                   WHERE STATUS = 'A'
                   ORDER BY DESCRICAO";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Perfis buscadas com sucesso.');
    }

    public function buscarPermissoesUsuario(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idUsuario = (int) $dadosRecebidos['ID_USUARIO'];

        $query = "SELECT permissoes.ID_CODIGO
                       , permissoes.NOME
                       , permissoes.DESCRICAO
                       , CASE
                           WHEN EXISTS (SELECT 1
                                          FROM permissao_usuario
                                         WHERE permissao_usuario.ID_PERMISSAO = permissoes.ID_CODIGO
                                           AND permissao_usuario.ID_USUARIO = $idUsuario
                                           AND permissao_usuario.STATUS = 'A') THEN 1
                           ELSE 0
                           END AS USUARIO_POSSUI_PERMISSAO
                    FROM permissoes
                   WHERE permissoes.STATUS = 'A'
                   ORDER BY permissoes.NOME";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Permissões do usuário buscadas com sucesso.');
    }

    public function alterarPermissoesUsuario(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idUsuario = $dadosRecebidos['ID_USUARIO'] ?? 0;
        $idPermissao = $dadosRecebidos['ID_PERMISSAO'] ?? 0;
        $permitido = $dadosRecebidos['PERMITIDO'] ?? null;

        if ($idUsuario <= 0 || $idPermissao <= 0 || !in_array($permitido, ['0', '1'], true)) {
            return exibirRetorno(false, false, 'Dados da permissão inválidos.');
        }

        $query = "SELECT ID_USUARIO
                    FROM usuarios
                   WHERE ID_USUARIO = $idUsuario
                     AND STATUS = 'A'";
        $usuario = executarSQL($query);

        $query = "SELECT ID_CODIGO
                    FROM permissoes
                   WHERE ID_CODIGO = $idPermissao
                     AND STATUS = 'A'";
        $permissao = executarSQL($query);

        if (empty($usuario) || empty($permissao)) {
            return exibirRetorno(false, false, 'Usuário ou permissão não encontrado.');
        }

        $query = "SELECT ID_CODIGO
                    FROM permissao_usuario
                   WHERE ID_USUARIO = $idUsuario
                     AND ID_PERMISSAO = $idPermissao";
        $result = executarSQL($query);

        if (!empty($result)) {
            $status = $permitido == 1 ? 'A' : 'I';

            $query = "UPDATE permissao_usuario
                         SET STATUS = '$status'
                       WHERE ID_USUARIO = $idUsuario
                         AND ID_PERMISSAO = $idPermissao";
            $result = executarSQL($query);
        } else {
            if ($permitido == 0) {
                return exibirRetorno(true, true, 'Permissão revogada com sucesso!');
            }

            $query = "INSERT INTO permissao_usuario (ID_USUARIO
                                                   , ID_PERMISSAO
                                                   , DT_HORA_CADASTRO
                                                   , STATUS)
                    VALUES ($idUsuario
                          , $idPermissao
                          , NOW()
                          , 'A')";
            $result = executarSQL($query);
        }

        $mensagem = $permitido == 1 ? 'Permissão liberada com sucesso!' : 'Permissão revogada com sucesso!';

        return exibirRetorno($result, true, $mensagem);
    }
}