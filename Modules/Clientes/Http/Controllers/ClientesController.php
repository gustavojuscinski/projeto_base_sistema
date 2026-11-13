<?php

namespace Modules\Clientes\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function index(){
        return view('clientes::index');
    }

    public function buscarClientes(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroCliente = '';
        $filtroLimite = '';
        $filtroStatus = !empty($dadosRecebidos['INATIVOS']) ? "AND STATUS = 'I'" : "AND STATUS = 'A'";

        if (!empty($dadosRecebidos['ID_CODIGO'])) {
            $filtroCliente = 'AND ID_CODIGO = ' . (int) $dadosRecebidos['ID_CODIGO'];
            $filtroStatus = '';
        }

        if (!empty($dadosRecebidos['FILTRO_BUSCA'])) {
            $termoBusca = DB::getPdo()->quote('%' . trim($dadosRecebidos['FILTRO_BUSCA']) . '%');
            $filtroBusca = "AND (NOME LIKE $termoBusca OR TELEFONE LIKE $termoBusca OR EMAIL LIKE $termoBusca OR CPF_CNPJ LIKE $termoBusca OR CIDADE LIKE $termoBusca OR CAST(ID_CODIGO AS CHAR) LIKE $termoBusca)";
        }

        $colunasOrdenaveis = ['ID_CODIGO', 'NOME', 'TELEFONE', 'CIDADE', 'DT_HORA_CADASTRO'];
        $filtroOrderBy = 'ORDER BY NOME, ID_CODIGO';

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

        $query = "SELECT clientes.*
                       , (SELECT COUNT(*)
                            FROM clientes
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroCliente) AS TOTAL_REGISTROS
                    FROM clientes
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroCliente
                   $filtroOrderBy
                   $filtroLimite";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Clientes buscados com sucesso.');
    }

    public function inserirCliente(Request $request){
        $dados = $this->validarDados($request);
        $colunas = array_keys($dados);
        $valores = array_map(fn ($valor) => DB::getPdo()->quote((string) $valor), array_values($dados));
        $query = 'INSERT INTO clientes (' . implode(', ', $colunas) . ', DT_HORA_CADASTRO, STATUS) '
               . 'VALUES (' . implode(', ', $valores) . ", NOW(), 'A')";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Cliente inserido com sucesso!');
    }

    public function alterarCliente(Request $request){
        $request->validate(['ID_CODIGO' => 'required|integer|min:1']);
        $idCodigo = (int) $request->input('ID_CODIGO');
        $dados = $this->validarDados($request);
        $alteracoes = [];
        foreach ($dados as $campo => $valor) {
            $alteracoes[] = $campo . ' = ' . DB::getPdo()->quote((string) $valor);
        }
        $query = 'UPDATE clientes SET ' . implode(', ', $alteracoes) . " WHERE ID_CODIGO = $idCodigo AND STATUS = 'A'";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Cliente alterado com sucesso!');
    }

    public function inativarCliente(Request $request){
        $request->validate(['ID_CODIGO' => 'required|integer|min:1']);
        $idCodigo = (int) $request->input('ID_CODIGO');
        $query = "UPDATE clientes SET STATUS = 'I' WHERE ID_CODIGO = $idCodigo AND STATUS = 'A'";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Cliente inativado com sucesso!');
    }

    private function validarDados(Request $request){
        $dados = $request->validate([
            'NOME' => 'required|string|max:255',
            'TELEFONE' => 'required|string|max:30',
            'EMAIL' => 'nullable|email|max:255',
            'CPF_CNPJ' => 'nullable|string|max:18',
            'CEP' => 'nullable|string|max:10',
            'RUA' => 'nullable|string|max:255',
            'NUMERO' => 'nullable|string|max:30',
            'COMPLEMENTO' => 'nullable|string|max:150',
            'BAIRRO' => 'nullable|string|max:150',
            'CIDADE' => 'nullable|string|max:150',
            'ESTADO' => 'nullable|string|size:2',
            'OBSERVACOES' => 'nullable|string'
        ]);

        $campos = ['NOME', 'TELEFONE', 'EMAIL', 'CPF_CNPJ', 'CEP', 'RUA', 'NUMERO', 'COMPLEMENTO', 'BAIRRO', 'CIDADE', 'ESTADO', 'OBSERVACOES'];
        $valores = [];
        foreach ($campos as $campo) {
            $valores[$campo] = trim((string) ($dados[$campo] ?? ''));
        }
        $valores['ESTADO'] = strtoupper($valores['ESTADO']);
        return $valores;
    }
}
