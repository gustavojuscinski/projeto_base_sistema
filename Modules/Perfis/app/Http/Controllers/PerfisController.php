<?php

namespace Modules\Perfis\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfisController extends Controller
{
    public function index(){
        return view('perfis::index');
    }

    public function buscarPerfis(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroPerfil = '';
        $filtroLimite = '';

        if (isset($dadosRecebidos['ID_CODIGO']) && $dadosRecebidos['ID_CODIGO'] !== '') {
            $filtroPerfil = "AND ID_CODIGO = " . $dadosRecebidos['ID_CODIGO'];
        }

        if (!empty($dadosRecebidos['FILTRO_BUSCA'])) {
            $termoBusca = DB::getPdo()->quote('%' . $dadosRecebidos['FILTRO_BUSCA'] . '%');
            $filtroBusca = "AND DESCRICAO LIKE $termoBusca";
        }

        if(isset($dadosRecebidos['INATIVOS']) && $dadosRecebidos['INATIVOS'] == 1){
            $filtroStatus = "AND STATUS = 'I'";
        } else {
            $filtroStatus = "AND STATUS = 'A'";
        }

        $colunasOrdenaveis = ['ID_CODIGO', 'DESCRICAO', 'DT_HORA_CADASTRO'];
        $filtroOrderBy = "ORDER BY DESCRICAO, ID_CODIGO";

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
                $filtroOrderBy = "ORDER BY " . implode(', ', $itensOrdenacao) . ", ID_CODIGO";
            }
        }

        // PAGINAÇÃO: só pagina quando a tela envia POR_PAGINA (outras telas continuam como antes)
        if (isset($dadosRecebidos['POR_PAGINA'])) {
            $porPagina = $dadosRecebidos['POR_PAGINA'];
            $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;

            $pagina = max(1, $dadosRecebidos['PAGINA'] ?? 1);
            $deslocamento = ($pagina - 1) * $porPagina;

            $filtroLimite = "LIMIT $porPagina OFFSET $deslocamento";
        }

        $query = "SELECT ID_CODIGO
                       , DESCRICAO
                       , (SELECT COUNT(*)
                            FROM perfis
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroPerfil) AS TOTAL_REGISTROS
                    FROM perfis
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroPerfil
                   $filtroOrderBy
                   $filtroLimite";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Perfis buscados com sucesso.');
    }

    public function inserirPerfil(Request $request){
        $dadosRecebidos = $request->except('_token');
        $descricao = $dadosRecebidos['DESCRICAO'];

        $query = "INSERT INTO perfis (DESCRICAO
                                      , DT_HORA_CADASTRO
                        ) VALUES ('$descricao'
                                , NOW())";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Perfil inserido com sucesso!');
    }

    public function alterarPerfil(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idPerfil = $dadosRecebidos['ID_CODIGO'];
        $descricao = $dadosRecebidos['DESCRICAO'];

        $query = "UPDATE perfis
                     SET DESCRICAO = '$descricao'
                   WHERE ID_CODIGO = $idPerfil";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Perfil alterado com sucesso!');
    }

    public function inativarPerfil(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idPerfil = $dadosRecebidos['ID_CODIGO'];

        $query = "UPDATE perfis
                     SET STATUS = 'I'
                   WHERE ID_CODIGO = $idPerfil";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Perfil inativado com sucesso!');
    }
}
