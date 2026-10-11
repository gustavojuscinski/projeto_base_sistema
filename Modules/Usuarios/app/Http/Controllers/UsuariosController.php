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
}