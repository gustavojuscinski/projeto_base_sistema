@extends('layouts.app')

@section('title', 'Brasa | Rendas Uber')
@section('page-title', 'Rendas Uber')

@section('content')
    <div>
        <div class="d-flex align-items-center gap-1">
            <div class="input-group flex-grow-1" style="min-width: 0;">
                <input type="text" class="form-control" placeholder="Buscar origem ou destino" id="inputFiltroRenda">
            </div>

            <div class="btn-group flex-shrink-0" role="group" aria-label="Período de rendas">
                <button type="button" class="btn btn-outline-secondary btn-periodo" style="width: 85px;" data-periodo="diario" aria-pressed="false">
                    Diário
                </button>
                <button type="button" class="btn btn-outline-secondary btn-periodo" style="width: 85px;" data-periodo="semanal" aria-pressed="false">
                    Semanal
                </button>
                <button type="button" class="btn btn-outline-secondary btn-periodo active" style="width: 85px;" data-periodo="mensal" aria-pressed="true">
                    Mensal
                </button>
                <button type="button" class="btn btn-outline-secondary btn-periodo" style="width: 85px;" data-periodo="personalizado" aria-pressed="false">
                    Período
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 flex-shrink-0" id="filtrosDatas">
                <input type="date" class="form-control" id="inputDataInicial" aria-label="Data inicial" style="width: 145px;">
                <span>até</span>
                <input type="date" class="form-control" id="inputDataFinal" aria-label="Data final" style="width: 145px;">

                <button type="button" class="btn btn-primary" id="btnAplicarPeriodo" title="Aplicar período">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>Buscar
                </button>
            </div>

            <button type="button" class="btn btn-primary text-nowrap flex-shrink-0" id="btnNovaRenda">
                <i class="fa-solid fa-plus me-1"></i>Novo
            </button>
        </div>

        <div class="row g-1 mt-1">
            <div class="col-6 col-md-4 col-xl-2"><div class="card h-100 mb-0"><div class="card-body py-2"><small class="text-secondary">Total Bruto</small><div class="fs-5 fw-bold" id="resumoBruto">R$ 0,00</div></div></div></div>
            <div class="col-6 col-md-4 col-xl-2"><div class="card h-100 mb-0"><div class="card-body py-2"><small class="text-secondary">Descontos</small><div class="fs-5 fw-bold text-danger" id="resumoDescontos">R$ 0,00</div></div></div></div>
            <div class="col-6 col-md-4 col-xl-2"><div class="card h-100 mb-0"><div class="card-body py-2"><small class="text-secondary">Valor Líquido</small><div class="fs-5 fw-bold text-success" id="resumoLiquido">R$ 0,00</div></div></div></div>
            <div class="col-6 col-md-4 col-xl-2"><div class="card h-100 mb-0"><div class="card-body py-2"><small class="text-secondary">Corridas</small><div class="fs-5 fw-bold" id="resumoCorridas">0</div></div></div></div>
            <div class="col-6 col-md-4 col-xl-2"><div class="card h-100 mb-0"><div class="card-body py-2"><small class="text-secondary">Tempo Rodado</small><div class="fs-5 fw-bold" id="resumoDuracao">0 min</div></div></div></div>
            <div class="col-6 col-md-4 col-xl-2"><div class="card h-100 mb-0"><div class="card-body py-2"><small class="text-secondary">Distância Total</small><div class="fs-5 fw-bold" id="resumoDistancia">0 km</div></div></div></div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelaRendas" aria-label="Tabela de rendas Uber">
                    <thead>
                        <tr>
                            <th class="ordenavel" data-coluna="ID_CODIGO" tabindex="0" aria-sort="none">ID</th>
                            <th class="ordenavel" data-coluna="DT_HORA_CORRIDA" tabindex="0" aria-sort="none">Data / hora</th>
                            <th class="ordenavel" data-coluna="ENDERECO_ORIGEM" tabindex="0" aria-sort="none">Origem</th>
                            <th class="ordenavel" data-coluna="ENDERECO_DESTINO" tabindex="0" aria-sort="none">Destino</th>
                            <th class="ordenavel" data-coluna="DURACAO" tabindex="0" aria-sort="none">Duração</th>
                            <th class="ordenavel" data-coluna="DISTANCIA" tabindex="0" aria-sort="none">Distância</th>
                            <th class="ordenavel" data-coluna="VALOR_BRUTO" tabindex="0" aria-sort="none">Bruto</th>
                            <th class="ordenavel" data-coluna="TOTAL_DESCONTO" tabindex="0" aria-sort="none">Descontos</th>
                            <th class="ordenavel" data-coluna="VALOR_LIQUIDO" tabindex="0" aria-sort="none">Líquido</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer tabela-rodape">
                <div class="tabela-info" id="infoContagem" aria-live="polite"></div>
                <nav class="tabela-paginacao" aria-label="Paginação da lista"><ul class="pagination pagination-sm mb-0" id="paginacaoLista"></ul></nav>
                <div class="tabela-por-pagina">
                    <label for="selectPorPagina" class="mb-0">Por página</label>
                    <select id="selectPorPagina" class="form-select form-select-sm"><option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRenda" tabindex="-1" aria-labelledby="modalRendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="modalRendaLabel">Nova corrida</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
            <div class="modal-body">
                <input type="hidden" id="inputIdRenda">
                <div class="row g-3">
                    <div class="col-md-6"><label for="inputDataCorrida" class="form-label">Data e Hora</label><input type="datetime-local" class="form-control" id="inputDataCorrida" step="1"></div>
                    <div class="col-md-6"><label for="inputTaxa" class="form-label">Taxa Uber Efetiva (%)</label><input type="number" min="0" step="0.01" class="form-control" id="inputTaxa" value="0.00" readonly></div>
                    <div class="col-md-6"><label for="inputOrigem" class="form-label">Endereço de Origem</label><input type="text" maxlength="255" class="form-control" id="inputOrigem"></div>
                    <div class="col-md-6"><label for="inputDestino" class="form-label">Endereço de Destino</label><input type="text" maxlength="255" class="form-control" id="inputDestino"></div>
                    <div class="col-md-3"><label for="inputDuracao" class="form-label">Duração (minutos)</label><input type="number" min="0" step="0.01" class="form-control" id="inputDuracao" value="0"></div>
                    <div class="col-md-3"><label for="inputDistancia" class="form-label">Distância (km)</label><input type="number" min="0" step="0.01" class="form-control" id="inputDistancia" value="0"></div>
                    <div class="col-md-3"><label for="inputValorBruto" class="form-label">Valor Bruto (R$)</label><input type="number" min="0" step="0.01" class="form-control" id="inputValorBruto" value="0"></div>
                    <div class="col-md-3"><label for="inputTotalDesconto" class="form-label">Total de Descontos (R$)</label><input type="number" min="0" step="0.01" class="form-control" id="inputTotalDesconto" value="0"></div>
                    <div class="col-md-3">
                        <label for="selectFormaPagamento" class="form-label">Forma de Pagamento</label>
                        <select class="form-select" id="selectFormaPagamento">
                            <option value="">Selecione uma forma de pagamento</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label for="inputValorTaxaUber" class="form-label">Taxa Uber (R$)</label><input type="number" min="0" step="0.01" class="form-control" id="inputValorTaxaUber" value="0"></div>
                    <div class="col-md-3"><label for="inputIss" class="form-label">ISS (R$)</label><input type="number" min="0" step="0.01" class="form-control" id="inputIss" value="0"></div>
                    <div class="col-md-3"><label for="inputPisCofins" class="form-label">PIS e COFINS (R$)</label><input type="number" min="0" step="0.01" class="form-control" id="inputPisCofins" value="0"></div>
                    <div class="col-12"><div class="d-flex justify-content-between border-top pt-2 fw-bold"><span>Valor Líquido Calculado</span><span class="text-success" id="valorLiquidoCadastro">R$ 0,00</span></div></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" onclick="salvarCadastro()">Salvar</button></div>
        </div></div>
    </div>

    <div class="modal fade" id="modalVisualizarRenda" tabindex="-1" aria-labelledby="modalVisualizarRendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="modalVisualizarRendaLabel">Detalhes da corrida</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
            <div class="modal-body" id="detalhesRenda"></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
        </div></div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var ordenacao = [];
    var paginaAtual = 1;
    var registrosPorPagina = 10;
    var periodoSelecionado = 'mensal';
    let temporizadorBusca;
    let mapaCorrida = null;
    let corridaMapaAtual = null;
    let sequenciaMapa = 0;
    let ultimaConsultaNominatim = 0;
    const cacheEnderecosMapa = {};
    const cacheRotasMapa = {};

    const moeda = valor => Number(valor || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    const numero = (valor, casas = 2) => Number(valor || 0).toLocaleString('pt-BR', { minimumFractionDigits: casas, maximumFractionDigits: casas });
    const textoSeguro = valor => $('<div>').text(valor ?? '').html();
    const dataLocal = data => `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}-${String(data.getDate()).padStart(2, '0')}`;
    const dataExibicao = valor => { if (!valor) return ''; const partes = String(valor).replace('T', ' ').split(' '); const data = partes[0].split('-'); return data.length === 3 ? `${data[2]}/${data[1]}/${data[0]} ${partes[1] || ''}`.trim() : String(valor); };

    function getFuncaoPadrao(){ return buscarDados(); }

    function atualizarDatas(){
        if (periodoSelecionado === 'personalizado') return;
        const agora = new Date();
        const inicio = new Date(agora.getFullYear(), agora.getMonth(), agora.getDate());
        const fim = new Date(inicio);
        if (periodoSelecionado === 'semanal') {
            inicio.setDate(inicio.getDate() - ((inicio.getDay() + 6) % 7));
            fim.setDate(inicio.getDate() + 6);
        }
        if (periodoSelecionado === 'mensal') {
            inicio.setDate(1);
            fim.setMonth(fim.getMonth() + 1, 0);
        }
        $('#inputDataInicial').val(dataLocal(inicio));
        $('#inputDataFinal').val(dataLocal(fim));
    }

    function buscarDados(){
        if (!$('#inputDataInicial').val() || !$('#inputDataFinal').val() || $('#inputDataInicial').val() > $('#inputDataFinal').val()) {
            informarAviso('Informe um período de datas válido.');
            return;
        }
        exibirLoading('S');
        requisicaoAjax({
            type: 'POST', dataType: 'json',
            data: {
                '_token': '{{ csrf_token() }}',
                'FILTRO_BUSCA': $('#inputFiltroRenda').val(),
                'DATA_INICIAL': $('#inputDataInicial').val(),
                'DATA_FINAL': $('#inputDataFinal').val(),
                'ORDENACAO_LISTA': ordenacao.join(','),
                'PAGINA': paginaAtual,
                'POR_PAGINA': registrosPorPagina
            },
            url: "{{ route('rendauber.buscar') }}",
            success: function(r){
                const dados = r.dados || [];
                if (!dados.length && paginaAtual > 1) {
                    paginaAtual--;
                    buscarDados();
                    return;
                }
                const resumo = dados[0] || {};
                $('#resumoBruto').text(moeda(resumo.RESUMO_BRUTO));
                $('#resumoDescontos').text(moeda(resumo.RESUMO_DESCONTOS));
                $('#resumoLiquido').text(moeda(resumo.RESUMO_LIQUIDO));
                $('#resumoCorridas').text(Number(resumo.TOTAL_REGISTROS || 0));
                const minutos = Number(resumo.RESUMO_DURACAO || 0);
                $('#resumoDuracao').text(minutos >= 60 ? `${Math.floor(minutos / 60)}h ${numero(minutos % 60, 0)}min` : `${numero(minutos, 0)} min`);
                $('#resumoDistancia').text(`${numero(resumo.RESUMO_DISTANCIA)} km`);
                popularTabelaDados(dados);
                renderizarPaginacao(Number(resumo.TOTAL_REGISTROS || 0));
                atualizarIndicadoresOrdenacao();
                exibirLoading('N');
            },
            error: function(err){ exibirLoading('N'); console.error('Erro ao buscar rendas:', err); }
        });
    }

    function renderizarPaginacao(total){
        const totalPaginas = Math.max(1, Math.ceil(total / registrosPorPagina));
        const inicio = total ? (paginaAtual - 1) * registrosPorPagina + 1 : 0;
        const fim = Math.min(paginaAtual * registrosPorPagina, total);
        $('#infoContagem').text(total ? `Exibindo ${inicio} a ${fim} de ${total} registros` : 'Nenhum registro');
        const botao = (rotulo, pagina, opcoes = {}) => `<li class="page-item ${opcoes.ativo ? 'active' : ''} ${opcoes.desabilitado ? 'disabled' : ''}"><button type="button" class="page-link" data-pagina="${pagina}" ${opcoes.desabilitado ? 'disabled' : ''} ${opcoes.ativo ? 'aria-current="page"' : ''} ${opcoes.aria ? `aria-label="${opcoes.aria}"` : ''}>${rotulo}</button></li>`;
        const paginas = [];
        for (let p = 1; p <= totalPaginas; p++) if (p === 1 || p === totalPaginas || Math.abs(p - paginaAtual) <= 2) paginas.push(p);
        let html = botao('<i class="fa-solid fa-angle-left"></i>', paginaAtual - 1, { desabilitado: paginaAtual <= 1, aria: 'Página anterior' });
        let anterior = 0;
        paginas.forEach(function(p){
            if (p - anterior > 1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
            html += botao(p, p, { ativo: p === paginaAtual });
            anterior = p;
        });
        html += botao('<i class="fa-solid fa-angle-right"></i>', paginaAtual + 1, { desabilitado: paginaAtual >= totalPaginas, aria: 'Próxima página' });
        $('#paginacaoLista').html(html);
    }

    function atualizarIndicadoresOrdenacao(){
        const cabecalhos = $('#tabelaRendas th.ordenavel');
        cabecalhos.removeClass('ordenado-asc ordenado-desc').attr('aria-sort', 'none');
        ordenacao.forEach(function(item){
            const [coluna, direcao] = item.split(' ');
            cabecalhos.filter('[data-coluna="' + coluna + '"]').addClass(direcao === 'ASC' ? 'ordenado-asc' : 'ordenado-desc').attr('aria-sort', direcao === 'ASC' ? 'ascending' : 'descending');
        });
    }

    function popularTabelaDados(dados){
        let linhas = '';
        if (!dados.length) {
            linhas = '<tr><td colspan="10" class="text-center">Nenhuma corrida encontrada.</td></tr>';
        } else {
            dados.forEach(function(renda){
                const id = Number(renda.ID_CODIGO);
                const botoes = `<div class="acoes-linha">
                    <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar" onclick="editarCadastro(${id})"><i class="fa-solid fa-edit"></i></button>
                    <div class="dropdown"><button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle" title="Mais ações" aria-label="Mais ações" aria-expanded="false" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button type="button" class="dropdown-item" onclick="visualizarCadastro(${id})"><i class="fa-solid fa-eye"></i><span>Visualizar</span></button></li>
                        <li><button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${id})"><i class="fa-solid fa-trash"></i><span>Excluir</span></button></li>
                    </ul></div></div>`;
                linhas += `<tr>
                    <td>${id}</td><td>${textoSeguro(dataExibicao(renda.DT_HORA_CORRIDA))}</td>
                    <td>${textoSeguro(renda.ENDERECO_ORIGEM)}</td><td>${textoSeguro(renda.ENDERECO_DESTINO)}</td>
                    <td>${numero(renda.DURACAO)} min</td><td>${numero(renda.DISTANCIA)} km</td>
                    <td>${moeda(renda.VALOR_BRUTO)}</td><td>${moeda(renda.TOTAL_DESCONTO)}</td>
                    <td class="fw-semibold text-success">${moeda(renda.VALOR_LIQUIDO)}</td><td>${botoes}</td>
                </tr>`;
            });
        }
        $('#tabelaRendas tbody').html(linhas);
    }

    function limparCamposModal(){
        $('#inputIdRenda, #inputOrigem, #inputDestino').val('');
        $('#inputDuracao, #inputDistancia, #inputValorBruto, #inputValorTaxaUber, #inputTotalDesconto, #inputIss, #inputPisCofins').val('0');
        $('#inputTaxa').val('0.00');
        $('#selectFormaPagamento').val('');
        const hoje = new Date();
        $('#inputDataCorrida').val(`${dataLocal(hoje)}T${String(hoje.getHours()).padStart(2, '0')}:${String(hoje.getMinutes()).padStart(2, '0')}:00`);
        calcularValorLiquido();
    }

    function calcularDescontosAutomaticos(){
        const valorBruto = Math.max(0, Number($('#inputValorBruto').val()) || 0);
        const valorISS = Math.round((valorBruto * 0.0025 + Number.EPSILON) * 100) / 100;
        const valorPisCofins = Math.round((valorBruto * 0.011 + Number.EPSILON) * 100) / 100;

        // Nos exemplos, R$ 15,00 tem taxa de R$ 1,66 (1,665 arredondado para o par).
        // Emprega "meio para o par" SOMENTE na taxa Uber para reproduzir os quatro exemplos.
        const centavosTaxa = valorBruto * 0.111 * 100;
        const inteiro = Math.floor(centavosTaxa);
        const fracao = centavosTaxa - inteiro;
        const centavosArredondados = Math.abs(fracao - 0.5) < 0.00000001
            ? (inteiro % 2 === 0 ? inteiro : inteiro + 1)
            : Math.round(centavosTaxa);
        const valorTaxa = centavosArredondados / 100;

        $('#inputIss').val(valorISS.toFixed(2));
        $('#inputPisCofins').val(valorPisCofins.toFixed(2));
        $('#inputValorTaxaUber').val(valorTaxa.toFixed(2));
        calcularTotalDescontos();
    }

    function calcularTotalDescontos(){
        const totalCentavos = Math.round((Number($('#inputIss').val()) || 0) * 100)
            + Math.round((Number($('#inputPisCofins').val()) || 0) * 100)
            + Math.round((Number($('#inputValorTaxaUber').val()) || 0) * 100);
        $('#inputTotalDesconto').val((totalCentavos / 100).toFixed(2));
        const valorBruto = Number($('#inputValorBruto').val()) || 0;
        const valorTaxa = Number($('#inputValorTaxaUber').val()) || 0;
        $('#inputTaxa').val(valorBruto > 0 ? (valorTaxa / valorBruto * 100).toFixed(2) : '0.00');
        calcularValorLiquido();
    }

    function calcularValorLiquido(){
        const brutoCentavos = Math.round((Number($('#inputValorBruto').val()) || 0) * 100);
        const descontoCentavos = Math.round((Number($('#inputTotalDesconto').val()) || 0) * 100);
        $('#valorLiquidoCadastro').text(moeda((brutoCentavos - descontoCentavos) / 100));
    }

    function dadosCadastro(){
        return {
            '_token': '{{ csrf_token() }}',
            'ID_CODIGO': $('#inputIdRenda').val(),
            'DT_HORA_CORRIDA': ($('#inputDataCorrida').val() || '').replace('T', ' ').padEnd(19, ':00'),
            'ENDERECO_ORIGEM': $('#inputOrigem').val(),
            'ENDERECO_DESTINO': $('#inputDestino').val(),
            'DURACAO': $('#inputDuracao').val(),
            'DISTANCIA': $('#inputDistancia').val(),
            'VALOR_BRUTO': $('#inputValorBruto').val(),
            'VALOR_ISS': $('#inputIss').val(),
            'VALOR_PIS_COFINS': $('#inputPisCofins').val(),
            'TOTAL_DESCONTO': $('#inputTotalDesconto').val(),
            'TAXA': $('#inputTaxa').val(),
            'ID_FORMA_PAGAMENTO': $('#selectFormaPagamento').val(),
            'FORMA_PAGAMENTO': $('#selectFormaPagamento option:selected').val() ? $('#selectFormaPagamento option:selected').text() : ''
        };
    }

    function salvarCadastro(){
        const dados = dadosCadastro();
        if (!dados.DT_HORA_CORRIDA || dados.DT_HORA_CORRIDA.startsWith(' :') || !$('#inputDataCorrida').val()) { informarAviso('Informe a data da corrida.'); return; }
        if (Number(dados.TOTAL_DESCONTO) > Number(dados.VALOR_BRUTO)) { informarAviso('O desconto não pode superar o valor bruto.'); return; }
        exibirLoading('S');
        requisicaoAjax({
            type: 'POST', dataType: 'json', data: dados,
            url: dados.ID_CODIGO ? "{{ route('rendauber.alterar') }}" : "{{ route('rendauber.inserir') }}",
            success: function(r){
                limparCamposModal();
                $('#modalRenda').modal('hide');
                exibirLoading('N');
                paginaAtual = 1;
                buscarDados();
                informarSucesso(r.mensagem);
            },
            error: function(err){ exibirLoading('N'); console.error('Erro ao salvar renda:', err); }
        });
    }

    function buscarCorrida(idRenda, callback){
        exibirLoading('S');
        requisicaoAjax({
            type: 'POST', dataType: 'json', data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idRenda },
            url: "{{ route('rendauber.buscar') }}",
            success: function(r){
                exibirLoading('N');
                const renda = r.dados?.[0];
                if (!renda) { informarAviso('Corrida não encontrada.'); return; }
                callback(renda);
            },
            error: function(err){ exibirLoading('N'); console.error('Erro ao consultar corrida:', err); }
        });
    }

    function editarCadastro(idRenda){
        buscarCorrida(idRenda, function(renda){
            $('#inputIdRenda').val(renda.ID_CODIGO);
            $('#inputDataCorrida').val(String(renda.DT_HORA_CORRIDA).replace(' ', 'T').slice(0, 19));
            $('#inputOrigem').val(renda.ENDERECO_ORIGEM);
            $('#inputDestino').val(renda.ENDERECO_DESTINO);
            $('#inputDuracao').val(renda.DURACAO);
            $('#inputDistancia').val(renda.DISTANCIA);
            $('#inputValorBruto').val(renda.VALOR_BRUTO);
            $('#inputIss').val(renda.VALOR_ISS);
            $('#inputPisCofins').val(renda.VALOR_PIS_COFINS);
            $('#inputTotalDesconto').val(renda.TOTAL_DESCONTO);
            $('#inputTaxa').val(Number(renda.TAXA ?? 0).toFixed(2));
            $('#inputValorTaxaUber').val((Math.max(0, Number(renda.TOTAL_DESCONTO || 0) - Number(renda.VALOR_ISS || 0) - Number(renda.VALOR_PIS_COFINS || 0))).toFixed(2));
            $('#selectFormaPagamento').val(renda.ID_FORMA_PAGAMENTO || '');
            calcularValorLiquido();
            $('#modalRendaLabel').text('Editar corrida');
            $('#modalRenda').modal('show');
        });
    }

    function visualizarCadastro(idRenda){
        buscarCorrida(idRenda, function(renda){
            const descontoServico = Math.round((Number(renda.TOTAL_DESCONTO || 0) - Number(renda.VALOR_ISS || 0) - Number(renda.VALOR_PIS_COFINS || 0)) * 100) / 100;
            corridaMapaAtual = renda;
            sequenciaMapa++;
            if (mapaCorrida) {
                mapaCorrida.remove();
                mapaCorrida = null;
            }
            $('#detalhesRenda').html(`<div class="row g-3">
                <div class="col-12">
                    <div id="mapaRendaUber" style="height: 320px; width: 100%; border-radius: 6px; z-index: 0;"></div>
                    <div id="mensagemMapaRenda" class="small text-secondary mt-1" role="status">Preparando mapa da rota estimada...</div>
                </div>
                <div class="col-md-6"><div class="text-secondary small">Data / hora</div><div class="fw-semibold">${textoSeguro(dataExibicao(renda.DT_HORA_CORRIDA))}</div></div>
                <div class="col-md-3"><div class="text-secondary small">Duração</div><div class="fw-semibold">${numero(renda.DURACAO)} min</div></div>
                <div class="col-md-3"><div class="text-secondary small">Distância</div><div class="fw-semibold">${numero(renda.DISTANCIA)} km</div></div>
                <div class="col-md-6"><div class="text-secondary small">Origem</div><div>${textoSeguro(renda.ENDERECO_ORIGEM)}</div></div>
                <div class="col-md-6"><div class="text-secondary small">Destino</div><div>${textoSeguro(renda.ENDERECO_DESTINO)}</div></div>
                <div class="col-12"><hr class="my-1"><h6 class="mb-2">Cálculo da corrida</h6>
                    <div class="d-flex justify-content-between mb-2"><span>Valor bruto</span><strong>${moeda(renda.VALOR_BRUTO)}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>ISS</span><span>${moeda(renda.VALOR_ISS)}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>PIS e COFINS</span><span>${moeda(renda.VALOR_PIS_COFINS)}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Taxa Uber (${numero(renda.TAXA)}%)</span><span>${moeda(descontoServico)}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Total de descontos</span><strong class="text-danger">${moeda(renda.TOTAL_DESCONTO)}</strong></div>
                    <div class="d-flex justify-content-between border-top pt-2"><strong>Valor líquido</strong><strong class="text-success fs-5">${moeda(renda.VALOR_LIQUIDO)}</strong></div>
                </div></div>`);
            $('#modalVisualizarRenda').modal('show');
        });
    }

    async function buscarCoordenadasMapa(endereco){
        const busca = endereco.includes('Ponta Grossa') ? endereco : endereco + ', Ponta Grossa, Paraná, Brasil';

        if (cacheEnderecosMapa[busca]) {
            return cacheEnderecosMapa[busca];
        }

        const intervalo = Math.max(0, 1100 - (Date.now() - ultimaConsultaNominatim));
        if (intervalo > 0) {
            await new Promise(resolve => setTimeout(resolve, intervalo));
        }
        ultimaConsultaNominatim = Date.now();

        const resposta = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=br&q=' + encodeURIComponent(busca), {
            headers: { 'Accept': 'application/json' }
        });

        if (!resposta.ok) {
            throw new Error('Falha ao localizar endereço (' + resposta.status + ').');
        }

        const dados = await resposta.json();
        if (!dados.length) {
            throw new Error('Endereço não localizado: ' + endereco);
        }

        cacheEnderecosMapa[busca] = [Number(dados[0].lat), Number(dados[0].lon)];
        return cacheEnderecosMapa[busca];
    }

    async function carregarMapaCorrida(){
        const corrida = corridaMapaAtual;
        const sequencia = sequenciaMapa;

        if (!corrida || !$('#mapaRendaUber').length) return;

        if (typeof L === 'undefined') {
            $('#mensagemMapaRenda').text('Não foi possível carregar a biblioteca de mapas.');
            return;
        }

        mapaCorrida = L.map('mapaRendaUber');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a> contributors'
        }).addTo(mapaCorrida);
        mapaCorrida.setView([-25.095, -50.161], 12);
        mapaCorrida.invalidateSize();

        if (!corrida.ENDERECO_ORIGEM || !corrida.ENDERECO_DESTINO) {
            $('#mensagemMapaRenda').text('É necessário informar origem e destino para visualizar a rota.');
            return;
        }

        try {
            $('#mensagemMapaRenda').text('Localizando origem e destino...');
            const origem = await buscarCoordenadasMapa(corrida.ENDERECO_ORIGEM);
            if (sequencia !== sequenciaMapa) return;
            const destino = await buscarCoordenadasMapa(corrida.ENDERECO_DESTINO);
            if (sequencia !== sequenciaMapa) return;

            L.marker(origem).addTo(mapaCorrida).bindPopup('Origem: ' + textoSeguro(corrida.ENDERECO_ORIGEM));
            L.marker(destino).addTo(mapaCorrida).bindPopup('Destino: ' + textoSeguro(corrida.ENDERECO_DESTINO));
            mapaCorrida.fitBounds([origem, destino], { padding: [35, 35] });

            $('#mensagemMapaRenda').text('Calculando rota estimada...');
            const chaveRota = origem.join(',') + '|' + destino.join(',');
            let rota = cacheRotasMapa[chaveRota];

            if (!rota) {
                const coordenadas = origem[1] + ',' + origem[0] + ';' + destino[1] + ',' + destino[0];
                const resposta = await fetch('https://router.project-osrm.org/route/v1/driving/' + coordenadas + '?overview=full&geometries=geojson');
                if (!resposta.ok) throw new Error('Serviço de rotas indisponível.');
                const dados = await resposta.json();
                if (dados.code !== 'Ok' || !dados.routes || !dados.routes.length) throw new Error('Não foi encontrada uma rota entre os endereços.');
                rota = dados.routes[0];
                cacheRotasMapa[chaveRota] = rota;
            }

            if (sequencia !== sequenciaMapa) return;
            const desenho = L.geoJSON(rota.geometry, { style: { color: '#0d6efd', weight: 5, opacity: 0.85 } }).addTo(mapaCorrida);
            mapaCorrida.fitBounds(desenho.getBounds(), { padding: [30, 30] });
            $('#mensagemMapaRenda').text('Rota estimada: ' + numero(rota.distance / 1000) + ' km.');
        } catch (erro) {
            if (sequencia === sequenciaMapa) {
                $('#mensagemMapaRenda').text('Não foi possível traçar a rota: ' + erro.message);
                console.error('Erro ao carregar mapa da corrida:', erro);
            }
        }
    }

    $('#modalVisualizarRenda').on('shown.bs.modal', function(){
        carregarMapaCorrida();
    });

    $('#modalVisualizarRenda').on('hidden.bs.modal', function(){
        sequenciaMapa++;
        corridaMapaAtual = null;
        if (mapaCorrida) {
            mapaCorrida.remove();
            mapaCorrida = null;
        }
    });

    function excluirCadastro(idRenda){
        Swal.fire({ title: 'Realmente deseja inativar a corrida?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sim, inativar', cancelButtonText: 'Cancelar', confirmButtonColor: '#d33', cancelButtonColor: '#6c757d' }).then(resultado => {
            if (resultado.isConfirmed) {
                exibirLoading('S');
                requisicaoAjax({
                    type: 'POST', dataType: 'json', data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idRenda },
                    url: "{{ route('rendauber.inativar') }}",
                    success: function(r){ exibirLoading('N'); buscarDados(); informarSucesso(r.mensagem); },
                    error: function(err){ exibirLoading('N'); console.error('Erro ao inativar renda:', err); }
                });
            } else informarAviso('Nada foi feito!');
        });
    }

    function buscarFormaPagamento(){
        requisicaoAjax({
            type: 'POST',
            dataType: 'json',
            data: {
                '_token': '{{ csrf_token() }}'
            },
            url: "{{ route('rendauber.buscar.formapagamento') }}",
            success: function (r) {
                popularSelectFormaPagamento(r.dados || []);
            },
            error: function (err) {
                console.error('Erro ao buscar formas de pagamento:', err);
            }
        });
    }

    function popularSelectFormaPagamento(dados){
        const selecionado = $('#selectFormaPagamento').val();
        $('#selectFormaPagamento').empty();
        $('#selectFormaPagamento').append('<option value="">Selecione uma forma de pagamento</option>');

        dados.forEach(function (formaPagamento) {
            $('#selectFormaPagamento').append(
                $('<option>', {
                    value: formaPagamento.ID_CODIGO,
                    text: formaPagamento.DESCRICAO
                })
            );
        });
        $('#selectFormaPagamento').val(selecionado);
    }

    $('#tabelaRendas thead').on('click', 'th.ordenavel', function(evento){
        const coluna = $(this).data('coluna');
        const itemAtual = ordenacao.find(item => item.startsWith(coluna + ' '));
        let novoItem = null;
        if (!itemAtual) novoItem = coluna + ' ASC';
        else if (itemAtual.endsWith(' ASC')) novoItem = coluna + ' DESC';
        if (evento.shiftKey) {
            ordenacao = ordenacao.filter(item => !item.startsWith(coluna + ' '));
            if (novoItem) ordenacao.push(novoItem);
        } else ordenacao = novoItem ? [novoItem] : [];
        paginaAtual = 1;
        atualizarIndicadoresOrdenacao();
        getFuncaoPadrao();
    });

    $('#tabelaRendas thead').on('keydown', 'th.ordenavel', function(evento){
        if (evento.key === 'Enter' || evento.key === ' ') { evento.preventDefault(); $(this).trigger('click'); }
    });

    $('#paginacaoLista').on('click', 'button[data-pagina]', function(){
        const pagina = parseInt($(this).data('pagina'), 10);
        if (!pagina || pagina === paginaAtual) return;
        paginaAtual = pagina;
        getFuncaoPadrao();
    });

    $('#selectPorPagina').on('change', function(){ registrosPorPagina = parseInt($(this).val(), 10); paginaAtual = 1; getFuncaoPadrao(); });

    $('#btnNovaRenda').on('click', function(){ limparCamposModal(); $('#modalRendaLabel').text('Nova corrida'); $('#modalRenda').modal('show'); });

    $('#inputValorBruto').on('input', calcularDescontosAutomaticos);
    $('#inputIss, #inputPisCofins, #inputValorTaxaUber').on('input', calcularTotalDescontos);
    $('#inputTotalDesconto').on('input', calcularValorLiquido);

    $('#inputFiltroRenda').on('keyup', function(){
        clearTimeout(temporizadorBusca);
        temporizadorBusca = setTimeout(function(){ paginaAtual = 1; buscarDados(); }, 1000);
    });


    $('.btn-periodo').on('click', function(){
        periodoSelecionado = $(this).data('periodo');
        $('.btn-periodo').removeClass('btn-secondary active').addClass('btn-outline-secondary').attr('aria-pressed', 'false');
        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary active').attr('aria-pressed', 'true');
        if (periodoSelecionado !== 'personalizado') {
            atualizarDatas();
            paginaAtual = 1;
            buscarDados();
        } else {
            $('#inputDataInicial').trigger('focus');
        }
    });

    $('#btnAplicarPeriodo').on('click', function(){
        periodoSelecionado = 'personalizado';
        $('.btn-periodo').removeClass('btn-secondary active').addClass('btn-outline-secondary').attr('aria-pressed', 'false');
        $('.btn-periodo[data-periodo="personalizado"]').removeClass('btn-outline-secondary').addClass('btn-secondary active').attr('aria-pressed', 'true');
        paginaAtual = 1;
        buscarDados();
    });
    
    $(document).ready(function(){ 
        atualizarDatas(); 
        buscarDados();
        buscarFormaPagamento();
    });
</script>
@endpush
