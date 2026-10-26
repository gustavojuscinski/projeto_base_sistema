<?php

namespace Modules\Filiais\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiliaisController extends Controller
{
    public function index(){
        return view('filiais::index');
    }

    public function buscarFiliais(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroFilial = '';
        $filtroLimite = '';

        if (isset($dadosRecebidos['ID_CODIGO']) && $dadosRecebidos['ID_CODIGO'] !== '') {
            $filtroFilial = "AND ID_CODIGO = " . (int) $dadosRecebidos['ID_CODIGO'];
        }

        if (!empty($dadosRecebidos['FILTRO_BUSCA'])) {
            $termoBusca = DB::getPdo()->quote('%' . $dadosRecebidos['FILTRO_BUSCA'] . '%');
            $filtroBusca = "AND DESCRICAO LIKE $termoBusca";
        }

        if (isset($dadosRecebidos['INATIVOS']) && $dadosRecebidos['INATIVOS'] == 1) {
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

        // Paginação somente quando a tela envia POR_PAGINA
        if (isset($dadosRecebidos['POR_PAGINA'])) {
            $porPagina = (int) $dadosRecebidos['POR_PAGINA'];
            $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;

            $pagina = max(1, (int) ($dadosRecebidos['PAGINA'] ?? 1));
            $deslocamento = ($pagina - 1) * $porPagina;

            $filtroLimite = "LIMIT $porPagina OFFSET $deslocamento";
        }

        $query = "SELECT ID_CODIGO
                       , DESCRICAO
                       , DT_HORA_CADASTRO
                       , STATUS
                       , (SELECT COUNT(*)
                            FROM filiais
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroFilial) AS TOTAL_REGISTROS
                    FROM filiais
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroFilial
                   $filtroOrderBy
                   $filtroLimite";

        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Filiais buscadas com sucesso.');
    }

    public function inserirFilial(Request $request){
        $dadosRecebidos = $request->except('_token');
        $descricao = trim($dadosRecebidos['DESCRICAO'] ?? '');

        if ($descricao === '') {
            return exibirRetorno(false, false, 'Informe a descrição da filial.');
        }

        $descricao = DB::getPdo()->quote($descricao);

        $query = "INSERT INTO filiais (DESCRICAO
                                    , DT_HORA_CADASTRO
                                    , STATUS)
                  VALUES ($descricao
                        , NOW()
                        , 'A')";

        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Filial inserida com sucesso!');
    }

    public function alterarFilial(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idFilial = (int) ($dadosRecebidos['ID_CODIGO'] ?? 0);
        $descricao = trim($dadosRecebidos['DESCRICAO'] ?? '');

        if ($idFilial <= 0 || $descricao === '') {
            return exibirRetorno(false, false, 'Informe os dados da filial.');
        }

        $descricao = DB::getPdo()->quote($descricao);

        $query = "UPDATE filiais
                     SET DESCRICAO = $descricao
                   WHERE ID_CODIGO = $idFilial";

        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Filial alterada com sucesso!');
    }

    public function inativarFilial(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idFilial = (int) ($dadosRecebidos['ID_CODIGO'] ?? 0);

        if ($idFilial <= 0) {
            return exibirRetorno(false, false, 'Filial inválida.');
        }

        $query = "UPDATE filiais
                     SET STATUS = 'I'
                   WHERE ID_CODIGO = $idFilial";

        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Filial inativada com sucesso!');
    }
}
