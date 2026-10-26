<?php

namespace Modules\Vendedores\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendedoresController extends Controller
{
    public function index(){
        return view('vendedores::index');
    }

    public function buscarVendedores(Request $request){
        $dadosRecebidos = $request->except('_token');
        $filtroBusca = '';
        $filtroVendedor = '';
        $filtroLimite = '';

        if (isset($dadosRecebidos['ID_VENDEDOR']) && $dadosRecebidos['ID_VENDEDOR'] !== '') {
            $filtroVendedor = 'AND vendedores.ID_VENDEDOR = ' . (int) $dadosRecebidos['ID_VENDEDOR'];
        }

        if (!empty($dadosRecebidos['FILTRO_BUSCA'])) {
            $termoBusca = DB::getPdo()->quote('%' . $dadosRecebidos['FILTRO_BUSCA'] . '%');
            $filtroBusca = "AND (vendedores.NOME_VENDEDOR LIKE $termoBusca
                              OR usuarios.NOME_USUARIO LIKE $termoBusca
                              OR filial.DESCRICAO LIKE $termoBusca)";
        }

        $filtroStatus = (isset($dadosRecebidos['INATIVOS']) && $dadosRecebidos['INATIVOS'] == 1)
            ? "AND vendedores.STATUS = 'I'" : "AND vendedores.STATUS = 'A'";

        $colunasOrdenaveis = ['ID_VENDEDOR', 'NOME_VENDEDOR', 'NOME_USUARIO', 'DESCRICAO_FILIAL', 'QTDE_HORARIOS'];
        $colunasSQL = [
            'ID_VENDEDOR' => 'vendedores.ID_VENDEDOR',
            'NOME_VENDEDOR' => 'vendedores.NOME_VENDEDOR',
            'NOME_USUARIO' => 'usuarios.NOME_USUARIO',
            'DESCRICAO_FILIAL' => 'filial.DESCRICAO',
            'QTDE_HORARIOS' => 'QTDE_HORARIOS'
        ];
        $filtroOrderBy = 'ORDER BY vendedores.NOME_VENDEDOR, vendedores.ID_VENDEDOR';

        if (!empty($dadosRecebidos['ORDENACAO_LISTA'])) {
            $itensOrdenacao = [];
            foreach (explode(',', $dadosRecebidos['ORDENACAO_LISTA']) as $item) {
                $partes = preg_split('/\s+/', trim($item));
                $coluna = $partes[0] ?? '';
                $direcao = strtoupper($partes[1] ?? 'ASC');
                if (in_array($coluna, $colunasOrdenaveis, true) && in_array($direcao, ['ASC', 'DESC'], true)) {
                    $itensOrdenacao[] = $colunasSQL[$coluna] . ' ' . $direcao;
                }
            }
            if ($itensOrdenacao) {
                $filtroOrderBy = 'ORDER BY ' . implode(', ', $itensOrdenacao) . ', vendedores.ID_VENDEDOR';
            }
        }

        if (isset($dadosRecebidos['POR_PAGINA'])) {
            $porPagina = (int) $dadosRecebidos['POR_PAGINA'];
            $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;
            $pagina = max(1, (int) ($dadosRecebidos['PAGINA'] ?? 1));
            $deslocamento = ($pagina - 1) * $porPagina;
            $filtroLimite = "LIMIT $porPagina OFFSET $deslocamento";
        }

        $query = "SELECT vendedores.ID_VENDEDOR
                       , vendedores.ID_USUARIO_VINCULADO
                       , vendedores.ID_FILIAL
                       , vendedores.NOME_VENDEDOR
                       , vendedores.DT_HORA_CADASTRO
                       , usuarios.NOME_USUARIO
                       , filial.DESCRICAO AS DESCRICAO_FILIAL
                       , (SELECT COUNT(*)
                            FROM horario_vendedor
                           WHERE horario_vendedor.ID_VENDEDOR = vendedores.ID_VENDEDOR
                             AND horario_vendedor.STATUS = 'A') AS QTDE_HORARIOS
                       , (SELECT COUNT(*)
                            FROM vendedores
                            LEFT JOIN usuarios ON usuarios.ID_USUARIO = vendedores.ID_USUARIO_VINCULADO
                            LEFT JOIN filial ON filial.ID_CODIGO = vendedores.ID_FILIAL
                           WHERE 1 = 1
                           $filtroBusca
                           $filtroStatus
                           $filtroVendedor) AS TOTAL_REGISTROS
                    FROM vendedores
                    LEFT JOIN usuarios ON usuarios.ID_USUARIO = vendedores.ID_USUARIO_VINCULADO
                    LEFT JOIN filial ON filial.ID_CODIGO = vendedores.ID_FILIAL
                   WHERE 1 = 1
                   $filtroBusca
                   $filtroStatus
                   $filtroVendedor
                   $filtroOrderBy
                   $filtroLimite";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Vendedores buscados com sucesso.');
    }

    public function inserirVendedor(Request $request){
        $dadosRecebidos = $request->except('_token');
        $nome = trim($dadosRecebidos['NOME_VENDEDOR'] ?? '');
        $idUsuario = (int) ($dadosRecebidos['ID_USUARIO_VINCULADO'] ?? 0);
        $idFilial = (int) ($dadosRecebidos['ID_FILIAL'] ?? 0);
        if ($nome === '') return exibirRetorno(false, false, 'Informe o nome do vendedor.');
        $nome = DB::getPdo()->quote($nome);
        $query = "INSERT INTO vendedores (NOME_VENDEDOR, ID_USUARIO_VINCULADO, ID_FILIAL, DT_HORA_CADASTRO)
                  VALUES ($nome, $idUsuario, $idFilial, NOW())";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Vendedor inserido com sucesso!');
    }

    public function alterarVendedor(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idVendedor = (int) ($dadosRecebidos['ID_VENDEDOR'] ?? 0);
        $nome = trim($dadosRecebidos['NOME_VENDEDOR'] ?? '');
        $idUsuario = (int) ($dadosRecebidos['ID_USUARIO_VINCULADO'] ?? 0);
        $idFilial = (int) ($dadosRecebidos['ID_FILIAL'] ?? 0);
        if ($idVendedor <= 0 || $nome === '') return exibirRetorno(false, false, 'Dados do vendedor inválidos.');
        $nome = DB::getPdo()->quote($nome);
        $query = "UPDATE vendedores
                     SET NOME_VENDEDOR = $nome
                       , ID_USUARIO_VINCULADO = $idUsuario
                       , ID_FILIAL = $idFilial
                   WHERE ID_VENDEDOR = $idVendedor";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Vendedor alterado com sucesso!');
    }

    public function inativarVendedor(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idVendedor = (int) ($dadosRecebidos['ID_VENDEDOR'] ?? 0);
        if ($idVendedor <= 0) return exibirRetorno(false, false, 'Vendedor inválido.');
        $query = "UPDATE vendedores SET STATUS = 'I' WHERE ID_VENDEDOR = $idVendedor";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Vendedor inativado com sucesso!');
    }

    public function buscarHorariosVendedor(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idVendedor = (int) ($dadosRecebidos['ID_VENDEDOR'] ?? 0);
        if ($idVendedor <= 0) return exibirRetorno(false, false, 'Vendedor inválido.');
        $query = "SELECT ID_CODIGO, ID_VENDEDOR, DIA_SEMANA, TIME_FORMAT(HORA_INICIO, '%H:%i') AS HORA_INICIO,
                         TIME_FORMAT(HORA_FIM, '%H:%i') AS HORA_FIM
                    FROM horario_vendedor
                   WHERE ID_VENDEDOR = $idVendedor AND STATUS = 'A'
                   ORDER BY DIA_SEMANA, HORA_INICIO";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Horários buscados com sucesso.');
    }

    public function inserirHorarioVendedor(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idVendedor = (int) ($dadosRecebidos['ID_VENDEDOR'] ?? 0);
        $diaSemana = $dadosRecebidos['DIA_SEMANA'] ?? null;
        $horaInicio = $dadosRecebidos['HORA_INICIO'] ?? '';
        $horaFim = $dadosRecebidos['HORA_FIM'] ?? '';
        if ($idVendedor <= 0 || !in_array((string) $diaSemana, ['0','1','2','3','4','5','6'], true)
            || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $horaInicio)
            || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $horaFim)
            || $horaInicio >= $horaFim) {
            return exibirRetorno(false, false, 'Informe dia e horário inicial/final válidos.');
        }
        $query = "SELECT ID_VENDEDOR FROM vendedores WHERE ID_VENDEDOR = $idVendedor AND STATUS = 'A'";
        if (empty(executarSQL($query))) return exibirRetorno(false, false, 'Vendedor não encontrado ou inativo.');
        $diaSemana = (int) $diaSemana;
        $query = "SELECT ID_CODIGO FROM horario_vendedor
                   WHERE ID_VENDEDOR = $idVendedor
                     AND DIA_SEMANA = $diaSemana
                     AND STATUS = 'A'
                     AND HORA_INICIO < '$horaFim'
                     AND HORA_FIM > '$horaInicio'";
        if (!empty(executarSQL($query))) return exibirRetorno(false, false, 'Já existe um horário sobreposto para este dia.');
        $query = "INSERT INTO horario_vendedor (ID_VENDEDOR, DIA_SEMANA, HORA_INICIO, HORA_FIM, DT_HORA_CADASTRO)
                  VALUES ($idVendedor, $diaSemana, '$horaInicio', '$horaFim', NOW())";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Horário inserido com sucesso!');
    }

    public function inativarHorarioVendedor(Request $request){
        $dadosRecebidos = $request->except('_token');
        $idVendedor = (int) ($dadosRecebidos['ID_VENDEDOR'] ?? 0);
        $idHorario = (int) ($dadosRecebidos['ID_CODIGO'] ?? 0);
        if ($idVendedor <= 0 || $idHorario <= 0) return exibirRetorno(false, false, 'Horário inválido.');
        $query = "UPDATE horario_vendedor
                     SET STATUS = 'I'
                   WHERE ID_CODIGO = $idHorario
                     AND ID_VENDEDOR = $idVendedor
                     AND STATUS = 'A'";
        $result = executarSQL($query);
        return exibirRetorno($result, true, 'Horário excluído com sucesso!');
    }
}
