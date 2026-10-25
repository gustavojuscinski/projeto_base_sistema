<?php

namespace Modules\RendaUber\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RendaUberController extends Controller
{
    public function index(){
        return view('rendauber::index');
    }

    public function buscarRendas(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroRenda = '';
        $filtroPeriodo = '';
        $filtroLimite = '';
        $filtroStatus = "AND STATUS = 'A'";

        if (isset($dadosRecebidos['ID_CODIGO']) && $dadosRecebidos['ID_CODIGO'] !== '') {
            $idRenda = DB::getPdo()->quote($dadosRecebidos['ID_CODIGO']);
            $filtroRenda = "AND ID_CODIGO = $idRenda";
        }

        if (isset($dadosRecebidos['FILTRO_BUSCA']) && $dadosRecebidos['FILTRO_BUSCA'] !== '') {
            $termoBusca = DB::getPdo()->quote('%' . $dadosRecebidos['FILTRO_BUSCA'] . '%');
            $filtroBusca = "AND (ENDERECO_ORIGEM LIKE $termoBusca OR ENDERECO_DESTINO LIKE $termoBusca)";
        }

        if (isset($dadosRecebidos['DATA_INICIAL']) && isset($dadosRecebidos['DATA_FINAL'])) {
            $dataInicial = DB::getPdo()->quote($dadosRecebidos['DATA_INICIAL'] . ' 00:00:00');
            $dataFinal = DB::getPdo()->quote($dadosRecebidos['DATA_FINAL'] . ' 23:59:59');
            $filtroPeriodo = "AND DT_HORA_CORRIDA BETWEEN $dataInicial AND $dataFinal";
        }

        $colunasOrdenaveis = ['ID_CODIGO', 'DT_HORA_CORRIDA', 'ENDERECO_ORIGEM', 'ENDERECO_DESTINO', 'DURACAO', 'DISTANCIA', 'VALOR_BRUTO', 'TOTAL_DESCONTO', 'VALOR_LIQUIDO'];
        $filtroOrderBy = 'ORDER BY DT_HORA_CORRIDA DESC, ID_CODIGO DESC';

        if (isset($dadosRecebidos['ORDENACAO_LISTA']) && $dadosRecebidos['ORDENACAO_LISTA'] !== '') {
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
                $filtroOrderBy = 'ORDER BY ' . implode(', ', $itensOrdenacao) . ', ID_CODIGO DESC';
            }
        }

        if (isset($dadosRecebidos['POR_PAGINA'])) {
            // LIMIT/OFFSET exigem inteiros; estes casts ficam apenas na paginação.
            $porPagina = (int) $dadosRecebidos['POR_PAGINA'];
            $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;
            $pagina = max(1, (int) ($dadosRecebidos['PAGINA'] ?? 1));
            $deslocamento = ($pagina - 1) * $porPagina;
            $filtroLimite = "LIMIT $porPagina OFFSET $deslocamento";
        }

        $filtros = "$filtroBusca $filtroStatus $filtroRenda $filtroPeriodo";

        $query = "SELECT ID_CODIGO
                       , ENDERECO_ORIGEM
                       , ENDERECO_DESTINO
                       , DURACAO
                       , DISTANCIA
                       , VALOR_BRUTO
                       , VALOR_ISS
                       , VALOR_PIS_COFINS
                       , TOTAL_DESCONTO
                       , (COALESCE(VALOR_BRUTO, 0) - COALESCE(TOTAL_DESCONTO, 0)) AS VALOR_LIQUIDO
                       , DT_HORA_CORRIDA
                       , TAXA
                       , ID_FORMA_PAGAMENTO
                       , FORMA_PAGAMENTO
                       , STATUS
                       , (SELECT COUNT(*) FROM renda_uber WHERE 1 = 1 $filtros) AS TOTAL_REGISTROS
                       , (SELECT COALESCE(SUM(VALOR_BRUTO), 0) FROM renda_uber WHERE 1 = 1 $filtros) AS RESUMO_BRUTO
                       , (SELECT COALESCE(SUM(TOTAL_DESCONTO), 0) FROM renda_uber WHERE 1 = 1 $filtros) AS RESUMO_DESCONTOS
                       , (SELECT COALESCE(SUM(COALESCE(VALOR_BRUTO, 0) - COALESCE(TOTAL_DESCONTO, 0)), 0) FROM renda_uber WHERE 1 = 1 $filtros) AS RESUMO_LIQUIDO
                       , (SELECT COALESCE(SUM(DURACAO), 0) FROM renda_uber WHERE 1 = 1 $filtros) AS RESUMO_DURACAO
                       , (SELECT COALESCE(SUM(DISTANCIA), 0) FROM renda_uber WHERE 1 = 1 $filtros) AS RESUMO_DISTANCIA
                    FROM renda_uber
                   WHERE 1 = 1
                   $filtros
                   $filtroOrderBy
                   $filtroLimite";

        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Rendas buscadas com sucesso.');
    }

    public function inserirRenda(Request $request){
        $dadosRecebidos = $request->except('_token');

        // Quote evita inserir texto da requisição diretamente no SQL, sem alterar os valores recebidos.
        $origem = DB::getPdo()->quote($dadosRecebidos['ENDERECO_ORIGEM'] ?? '');
        $destino = DB::getPdo()->quote($dadosRecebidos['ENDERECO_DESTINO'] ?? '');
        $duracao = DB::getPdo()->quote($dadosRecebidos['DURACAO'] ?? 0);
        $distancia = DB::getPdo()->quote($dadosRecebidos['DISTANCIA'] ?? 0);
        $bruto = DB::getPdo()->quote($dadosRecebidos['VALOR_BRUTO'] ?? 0);
        $iss = DB::getPdo()->quote($dadosRecebidos['VALOR_ISS'] ?? 0);
        $pisCofins = DB::getPdo()->quote($dadosRecebidos['VALOR_PIS_COFINS'] ?? 0);
        $desconto = DB::getPdo()->quote($dadosRecebidos['TOTAL_DESCONTO'] ?? 0);
        $data = DB::getPdo()->quote($dadosRecebidos['DT_HORA_CORRIDA'] ?? '');
        $taxa = DB::getPdo()->quote($dadosRecebidos['TAXA'] ?? 0);
        $idFormaPagamento = isset($dadosRecebidos['ID_FORMA_PAGAMENTO']) && $dadosRecebidos['ID_FORMA_PAGAMENTO'] !== ''
            ? DB::getPdo()->quote($dadosRecebidos['ID_FORMA_PAGAMENTO']) : 'NULL';
        $formaPagamento = DB::getPdo()->quote($dadosRecebidos['FORMA_PAGAMENTO'] ?? '');

        $query = "INSERT INTO renda_uber (ENDERECO_ORIGEM
                                        , ENDERECO_DESTINO
                                        , DURACAO
                                        , DISTANCIA
                                        , VALOR_BRUTO
                                        , VALOR_ISS
                                        , VALOR_PIS_COFINS
                                        , TOTAL_DESCONTO
                                        , DT_HORA_CORRIDA
                                        , TAXA
                                        , ID_FORMA_PAGAMENTO
                                        , FORMA_PAGAMENTO
                                        , STATUS)
                  VALUES ($origem
                        , $destino
                        , $duracao
                        , $distancia
                        , $bruto
                        , $iss
                        , $pisCofins
                        , $desconto
                        , $data
                        , $taxa
                        , $idFormaPagamento
                        , $formaPagamento
                        , 'A')";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Renda inserida com sucesso!');
    }

    public function alterarRenda(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idRenda = DB::getPdo()->quote($dadosRecebidos['ID_CODIGO']);
        $origem = DB::getPdo()->quote($dadosRecebidos['ENDERECO_ORIGEM'] ?? '');
        $destino = DB::getPdo()->quote($dadosRecebidos['ENDERECO_DESTINO'] ?? '');
        $duracao = DB::getPdo()->quote($dadosRecebidos['DURACAO'] ?? 0);
        $distancia = DB::getPdo()->quote($dadosRecebidos['DISTANCIA'] ?? 0);
        $bruto = DB::getPdo()->quote($dadosRecebidos['VALOR_BRUTO'] ?? 0);
        $iss = DB::getPdo()->quote($dadosRecebidos['VALOR_ISS'] ?? 0);
        $pisCofins = DB::getPdo()->quote($dadosRecebidos['VALOR_PIS_COFINS'] ?? 0);
        $desconto = DB::getPdo()->quote($dadosRecebidos['TOTAL_DESCONTO'] ?? 0);
        $data = DB::getPdo()->quote($dadosRecebidos['DT_HORA_CORRIDA'] ?? '');
        $taxa = DB::getPdo()->quote($dadosRecebidos['TAXA'] ?? 0);
        $idFormaPagamento = isset($dadosRecebidos['ID_FORMA_PAGAMENTO']) && $dadosRecebidos['ID_FORMA_PAGAMENTO'] !== ''
            ? DB::getPdo()->quote($dadosRecebidos['ID_FORMA_PAGAMENTO']) : 'NULL';
        $formaPagamento = DB::getPdo()->quote($dadosRecebidos['FORMA_PAGAMENTO'] ?? '');

        $query = "UPDATE renda_uber
                     SET ENDERECO_ORIGEM = $origem
                       , ENDERECO_DESTINO = $destino
                       , DURACAO = $duracao
                       , DISTANCIA = $distancia
                       , VALOR_BRUTO = $bruto
                       , VALOR_ISS = $iss
                       , VALOR_PIS_COFINS = $pisCofins
                       , TOTAL_DESCONTO = $desconto
                       , DT_HORA_CORRIDA = $data
                       , TAXA = $taxa
                       , ID_FORMA_PAGAMENTO = $idFormaPagamento
                       , FORMA_PAGAMENTO = $formaPagamento
                   WHERE ID_CODIGO = $idRenda";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Renda alterada com sucesso!');
    }

    public function inativarRenda(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idRenda = DB::getPdo()->quote($dadosRecebidos['ID_CODIGO']);

        $query = "UPDATE renda_uber
                     SET STATUS = 'I'
                   WHERE ID_CODIGO = $idRenda";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Renda inativada com sucesso!');
    }

    public function buscarFormaPagamento(Request $request){
        $query = "SELECT ID_CODIGO
                       , DESCRICAO
                    FROM forma_pagamento
                   WHERE STATUS = 'A'
                   ORDER BY DESCRICAO";
        $result = executarSQL($query);

        return exibirRetorno($result, true, 'Formas de pagamento buscadas com sucesso.');
    }
}
