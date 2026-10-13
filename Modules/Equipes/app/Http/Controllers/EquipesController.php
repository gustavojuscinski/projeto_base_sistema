<?php

namespace Modules\Equipes\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipesController extends Controller
{
    public function index(){
        return view('equipes::index');
    }

    public function buscarEquipes(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroEquipe = '';
        $filtroLimite = '';

        if (isset($dadosRecebidos['ID_CODIGO']) && $dadosRecebidos['ID_CODIGO'] !== '') {
            $filtroEquipe = "AND ID_CODIGO = " . $dadosRecebidos['ID_CODIGO'];
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
                            FROM equipe
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroEquipe) AS TOTAL_REGISTROS
                    FROM equipe
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroEquipe
                   $filtroOrderBy
                   $filtroLimite";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Equipes buscadas com sucesso.');
    }

    public function inserirEquipe(Request $request){
        $dadosRecebidos = $request->except('_token');
        $descricao = $dadosRecebidos['DESCRICAO'];

        $query = "INSERT INTO equipe (DESCRICAO
                                      , DT_HORA_CADASTRO
                        ) VALUES ('$descricao'
                                , NOW())";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Equipe inserida com sucesso!');
    }

    public function alterarEquipe(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idEquipe = $dadosRecebidos['ID_CODIGO'];
        $descricao = $dadosRecebidos['DESCRICAO'];

        $query = "UPDATE equipe
                     SET DESCRICAO = '$descricao'
                   WHERE ID_CODIGO = $idEquipe";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Equipe alterada com sucesso!');
    }

    public function inativarEquipe(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idEquipe = $dadosRecebidos['ID_CODIGO'];

        $query = "UPDATE equipe
                     SET STATUS = 'I'
                   WHERE ID_CODIGO = $idEquipe";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Equipe inativada com sucesso!');
    }
}
