<?php

namespace Modules\Equipamentos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipamentosController extends Controller
{
    public function index(){
        return view('equipamentos::index');
    }

    public function buscarEquipamentos(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroEquipamento = '';
        $filtroLimite = '';
        $filtroStatus = !empty($dadosRecebidos['INATIVOS']) ? "AND STATUS = 'I'" : "AND STATUS = 'A'";

        if (!empty($dadosRecebidos['ID_CODIGO'])) {
            $filtroEquipamento = 'AND ID_CODIGO = ' . (int) $dadosRecebidos['ID_CODIGO'];
            $filtroStatus = '';
        }

        if (!empty($dadosRecebidos['FILTRO_BUSCA'])) {
            $termoBusca = DB::getPdo()->quote('%' . trim($dadosRecebidos['FILTRO_BUSCA']) . '%');
            $filtroBusca = "AND (MARCA LIKE $termoBusca OR MODELO LIKE $termoBusca OR TIPO LIKE $termoBusca OR CAST(ID_CODIGO AS CHAR) LIKE $termoBusca)";
        }

        $colunasOrdenaveis = ['ID_CODIGO', 'MARCA', 'MODELO', 'TIPO', 'DT_HORA_CADASTRO'];
        $filtroOrderBy = 'ORDER BY MARCA, MODELO, ID_CODIGO';

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
                $filtroOrderBy = 'ORDER BY ' . implode(', ', $itensOrdenacao) . ', ID_CODIGO';
            }
        }

        if (isset($dadosRecebidos['POR_PAGINA'])) {
            $porPagina = (int) $dadosRecebidos['POR_PAGINA'];
            $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;
            $pagina = max(1, (int) ($dadosRecebidos['PAGINA'] ?? 1));
            $deslocamento = ($pagina - 1) * $porPagina;
            $filtroLimite = "LIMIT $porPagina OFFSET $deslocamento";
        }

        $query = "SELECT equipamentos.*
                       , (SELECT COUNT(*)
                            FROM equipamentos
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroEquipamento) AS TOTAL_REGISTROS
                    FROM equipamentos
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroEquipamento
                   $filtroOrderBy
                   $filtroLimite";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Equipamentos buscados com sucesso.');
    }

    public function inserirEquipamento(Request $request){
        $dados = $this->validarDados($request);
        $colunas = array_keys($dados);
        $valores = array_map(fn ($valor) => DB::getPdo()->quote((string) $valor), array_values($dados));
        $query = 'INSERT INTO equipamentos (' . implode(', ', $colunas) . ', DT_HORA_CADASTRO, STATUS) '
               . 'VALUES (' . implode(', ', $valores) . ", NOW(), 'A')";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Equipamento inserido com sucesso!');
    }

    public function alterarEquipamento(Request $request){
        $request->validate(['ID_CODIGO' => 'required|integer|min:1']);
        $idCodigo = (int) $request->input('ID_CODIGO');
        $dados = $this->validarDados($request);
        $alteracoes = [];
        foreach ($dados as $campo => $valor) {
            $alteracoes[] = $campo . ' = ' . DB::getPdo()->quote((string) $valor);
        }
        $query = 'UPDATE equipamentos SET ' . implode(', ', $alteracoes) . " WHERE ID_CODIGO = $idCodigo AND STATUS = 'A'";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Equipamento alterado com sucesso!');
    }

    public function inativarEquipamento(Request $request){
        $request->validate(['ID_CODIGO' => 'required|integer|min:1']);
        $idCodigo = (int) $request->input('ID_CODIGO');
        $query = "UPDATE equipamentos SET STATUS = 'I' WHERE ID_CODIGO = $idCodigo AND STATUS = 'A'";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Equipamento inativado com sucesso!');
    }

    private function validarDados(Request $request){
        $dados = $request->validate([
            'MARCA' => 'required|string|max:150',
            'MODELO' => 'required|string|max:150',
            'TIPO' => 'required|string|max:150',
            'OBSERVACOES' => 'nullable|string'
        ]);

        $campos = ['MARCA', 'MODELO', 'TIPO', 'OBSERVACOES'];
        $valores = [];
        foreach ($campos as $campo) {
            $valores[$campo] = trim((string) ($dados[$campo] ?? ''));
        }
        return $valores;
    }
}
