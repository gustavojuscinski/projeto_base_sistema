@extends('layouts.app')

@section('title', 'Brasa | Ordem de Serviço')
@section('page-title', 'Ordem de Serviço')

@section('content')
    <div>
        <div class="d-flex gap-2">
            <div class="input-group flex-grow-1">
                <input type="text" class="form-control" placeholder="Buscar OS, cliente, telefone ou equipamento" id="inputFiltroOrdemServico">
                <label class="input-group-text gap-2" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Inativos</span>
                </label>
            </div>
            <button type="button" class="btn btn-primary text-nowrap" id="btnNovaOrdemServico">
                <i class="fa-solid fa-plus me-1"></i>Novo
            </button>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelaOrdensServico">
                    <thead>
                        <tr>
                            <th class="ordenavel" data-coluna="ID_CODIGO" tabindex="0" aria-sort="none">OS</th>
                            <th class="ordenavel" data-coluna="DT_HORA_CADASTRO" tabindex="0" aria-sort="none">Data</th>
                            <th class="ordenavel" data-coluna="NOME_CLIENTE" tabindex="0" aria-sort="none">Cliente</th>
                            <th class="ordenavel" data-coluna="MARCA_EQUIPAMENTO" tabindex="0" aria-sort="none">Equipamento</th>
                            <th class="ordenavel" data-coluna="VALOR_TOTAL" tabindex="0" aria-sort="none">Total</th>
                            <th style="width: 110px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer tabela-rodape">
                <div class="tabela-info" id="infoContagem" aria-live="polite"></div>
                <nav class="tabela-paginacao" aria-label="Paginação da lista">
                    <ul class="pagination pagination-sm mb-0" id="paginacaoLista"></ul>
                </nav>
                <div class="tabela-por-pagina">
                    <label for="selectPorPagina" class="mb-0">Por página</label>
                    <select id="selectPorPagina" class="form-select form-select-sm">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalOrdemServico" tabindex="-1" aria-labelledby="modalOrdemServicoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="modalOrdemServicoLabel">Nova ordem de serviço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body py-2" style="max-height: calc(100dvh - 170px); overflow-y: auto;">
                    <input type="hidden" id="inputIdOrdemServico">
                    <ul class="nav nav-tabs mb-1" id="abasOrdemServico" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="abaDadosTab" data-bs-toggle="tab" data-bs-target="#abaDados" type="button" role="tab" aria-controls="abaDados" aria-selected="true">Dados da OS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="abaValoresTab" data-bs-toggle="tab" data-bs-target="#abaValores" type="button" role="tab" aria-controls="abaValores" aria-selected="false">Valores</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="conteudoAbasOrdemServico">
                        <div class="tab-pane fade show active" id="abaDados" role="tabpanel" aria-labelledby="abaDadosTab">
                            <div class="row g-2 align-items-end mb-1">
                                <div class="col-12 col-md-8"><h6 class="mb-1">Cliente</h6></div>
                                <div class="col-12 col-md-4">
                                    <label for="inputDataOrdemServico" class="form-label mb-1">Data e hora da OS <span class="text-danger">*</span></label>
                                    <input type="datetime-local" step="60" class="form-control form-control-sm" id="inputDataOrdemServico" required>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-12 col-md-3 mb-1">
                                    <label for="inputCepCliente" class="form-label mb-1">CEP</label>
                                    <input type="text" class="form-control form-control-sm" id="inputCepCliente" maxlength="10" inputmode="numeric" placeholder="00000-000">
                                </div>
                                <div class="col-12 col-md-6 mb-1 position-relative">
                                    <label for="inputNomeCliente" class="form-label mb-1">Nome <span class="text-danger">*</span></label>
                                    <input type="hidden" id="inputIdCliente">
                                    <input type="text" class="form-control form-control-sm" id="inputNomeCliente" autocomplete="off" maxlength="255" placeholder="Busque um cliente ou digite o nome" required>
                                    <div class="dropdown-menu w-100" id="listaClientes" style="max-height: 220px; overflow-y: auto;"></div>
                                </div>
                                <div class="col-12 col-md-3 mb-1">
                                    <label for="inputTelefoneCliente" class="form-label mb-1">Telefone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="inputTelefoneCliente" maxlength="30" required>
                                </div>
                                <div class="col-12 col-md-8 mb-1">
                                    <label for="inputRuaCliente" class="form-label mb-1">Rua</label>
                                    <input type="text" class="form-control form-control-sm" id="inputRuaCliente" maxlength="255">
                                </div>
                                <div class="col-12 col-md-4 mb-1">
                                    <label for="inputNumeroCliente" class="form-label mb-1">Número</label>
                                    <input type="text" class="form-control form-control-sm" id="inputNumeroCliente" maxlength="30">
                                </div>
                                <div class="col-12 col-md-4 mb-1">
                                    <label for="inputBairroCliente" class="form-label mb-1">Bairro</label>
                                    <input type="text" class="form-control form-control-sm" id="inputBairroCliente" maxlength="150">
                                </div>
                                <div class="col-12 col-md-4 mb-1">
                                    <label for="inputCidadeCliente" class="form-label mb-1">Cidade</label>
                                    <input type="text" class="form-control form-control-sm" id="inputCidadeCliente" maxlength="150">
                                </div>
                                <div class="col-6 col-md-2 mb-1">
                                    <label for="inputEstadoCliente" class="form-label mb-1">Estado</label>
                                    <input type="text" class="form-control form-control-sm" id="inputEstadoCliente" maxlength="2" placeholder="UF">
                                </div>
                            </div>

                            <hr class="my-2">
                            <h6 class="mb-1">Equipamento</h6>
                            <div class="row g-2">
                                <div class="col-12 col-md-4 mb-1 position-relative">
                                    <label for="inputMarcaEquipamento" class="form-label mb-1">Marca (buscar equipamento)</label>
                                    <input type="hidden" id="inputIdEquipamento">
                                    <input type="text" class="form-control form-control-sm" id="inputMarcaEquipamento" autocomplete="off" maxlength="150" placeholder="Busque ou informe a marca">
                                    <div class="dropdown-menu w-100" id="listaEquipamentos" style="max-height: 220px; overflow-y: auto;"></div>
                                </div>
                                <div class="col-12 col-md-4 mb-1">
                                    <label for="inputModeloEquipamento" class="form-label mb-1">Modelo</label>
                                    <input type="text" class="form-control form-control-sm" id="inputModeloEquipamento" maxlength="150">
                                </div>
                                <div class="col-12 col-md-4 mb-1">
                                    <label for="inputTipoEquipamento" class="form-label mb-1">Tipo</label>
                                    <input type="text" class="form-control form-control-sm" id="inputTipoEquipamento" maxlength="150">
                                </div>
                                <div class="col-12 col-md-4 mb-1">
                                    <label for="inputNumeroSerie" class="form-label mb-1">Número de série</label>
                                    <input type="text" class="form-control form-control-sm" id="inputNumeroSerie" maxlength="150">
                                </div>
                                <div class="col-12 col-md-8 mb-1">
                                    <label for="inputDefeito" class="form-label mb-1">Defeito informado</label>
                                    <textarea class="form-control form-control-sm" id="inputDefeito" rows="3"></textarea>
                                </div>
                                <div class="col-12 mb-1">
                                    <label for="inputLaudoTecnico" class="form-label mb-1">Laudo técnico</label>
                                    <textarea class="form-control form-control-sm" id="inputLaudoTecnico" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="abaValores" role="tabpanel" aria-labelledby="abaValoresTab">
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header fw-semibold py-1 px-2">Peças</div>
                                        <div class="card-body p-2">
                                            <div class="mb-1">
                                                <label for="inputDescricaoPecas" class="form-label mb-1">Peças utilizadas</label>
                                                <textarea class="form-control form-control-sm" id="inputDescricaoPecas" rows="3" placeholder="Descreva as peças utilizadas"></textarea>
                                            </div>
                                            <label for="inputValorPecas" class="form-label mb-1">Valor das peças (R$)</label>
                                            <input type="text" class="form-control form-control-sm campo-valor" id="inputValorPecas" inputmode="numeric" value="0,00" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header fw-semibold py-1 px-2">Serviços</div>
                                        <div class="card-body p-2">
                                            <div class="mb-1">
                                                <label for="inputDescricaoServicos" class="form-label mb-1">Serviços realizados</label>
                                                <textarea class="form-control form-control-sm" id="inputDescricaoServicos" rows="3" placeholder="Descreva os serviços realizados"></textarea>
                                            </div>
                                            <label for="inputValorServicos" class="form-label mb-1">Valor dos serviços (R$)</label>
                                            <input type="text" class="form-control form-control-sm campo-valor" id="inputValorServicos" inputmode="numeric" value="0,00" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" id="checkHouveDeslocamento">
                                        <label class="form-check-label" for="checkHouveDeslocamento">Houve deslocamento?</label>
                                    </div>
                                    <div class="col-12 col-md-4 d-none" id="areaValorDeslocamento">
                                        <label for="inputValorDeslocamento" class="form-label mb-1">Valor do deslocamento (R$)</label>
                                        <input type="text" class="form-control form-control-sm campo-valor" id="inputValorDeslocamento" inputmode="numeric" value="0,00" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
                    <div class="d-flex align-items-center gap-2 me-auto">
                        <span class="fw-semibold">Total da OS:</span>
                        <strong class="fs-4" id="textoTotalOrdemServico">R$ 0,00</strong>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnSalvarOrdemServico" onclick="salvarCadastro()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var ordenacao = [];
        var paginaAtual = 1;
        var registrosPorPagina = 10;
        let temporizadorBusca;
        let temporizadorCliente;
        let temporizadorEquipamento;
        let sequenciaCliente = 0;
        let sequenciaEquipamento = 0;

        function getFuncaoPadrao(){
            return buscarDados();
        }

        function buscarDados(){
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'FILTRO_BUSCA': $('#inputFiltroOrdemServico').val(),
                    'INATIVOS': $('#checkPesquisarInativos').is(':checked') ? 1 : 0,
                    'ORDENACAO_LISTA': ordenacao.join(','),
                    'PAGINA': paginaAtual,
                    'POR_PAGINA': registrosPorPagina
                },
                url: "{{ route('ordem-servico.buscar') }}",
                success: function(r){
                    const dados = r.dados || [];
                    if (dados.length === 0 && paginaAtual > 1) {
                        paginaAtual--;
                        buscarDados();
                        return;
                    }
                    const total = dados.length ? (parseInt(dados[0].TOTAL_REGISTROS, 10) || dados.length) : 0;
                    exibirLoading('N');
                    popularTabelaDados(dados);
                    renderizarPaginacao(total);
                    atualizarIndicadoresOrdenacao();
                },
                error: function(err){
                    exibirLoading('N');
                    console.error('Erro ao buscar ordens de serviço:', err);
                }
            });
        }

        function renderizarPaginacao(total){
            const totalPaginas = Math.max(1, Math.ceil(total / registrosPorPagina));
            const inicio = total === 0 ? 0 : (paginaAtual - 1) * registrosPorPagina + 1;
            const fim = Math.min(paginaAtual * registrosPorPagina, total);
            $('#infoContagem').text(total === 0 ? 'Nenhum registro' : `Exibindo ${inicio} a ${fim} de ${total} registros`);
            const botao = (rotulo, pagina, opcoes = {}) => `
                <li class="page-item ${opcoes.ativo ? 'active' : ''} ${opcoes.desabilitado ? 'disabled' : ''}">
                    <button type="button" class="page-link" data-pagina="${pagina}" ${opcoes.desabilitado ? 'disabled' : ''} ${opcoes.ativo ? 'aria-current="page"' : ''}>
                        ${rotulo}
                    </button>
                </li>`;
            const paginas = [];
            for (let p = 1; p <= totalPaginas; p++) {
                if (p === 1 || p === totalPaginas || Math.abs(p - paginaAtual) <= 2) paginas.push(p);
            }
            let html = botao('<i class="fa-solid fa-angle-left"></i>', paginaAtual - 1, { desabilitado: paginaAtual <= 1 });
            let anterior = 0;
            paginas.forEach(function(p){
                if (p - anterior > 1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                html += botao(p, p, { ativo: p === paginaAtual });
                anterior = p;
            });
            html += botao('<i class="fa-solid fa-angle-right"></i>', paginaAtual + 1, { desabilitado: paginaAtual >= totalPaginas });
            $('#paginacaoLista').html(html);
        }

        function atualizarIndicadoresOrdenacao(){
            const cabecalhos = $('#tabelaOrdensServico th.ordenavel');
            cabecalhos.removeClass('ordenado-asc ordenado-desc').attr('aria-sort', 'none');
            ordenacao.forEach(function(item){
                const [coluna, direcao] = item.split(' ');
                cabecalhos.filter('[data-coluna="' + coluna + '"]')
                    .addClass(direcao === 'ASC' ? 'ordenado-asc' : 'ordenado-desc')
                    .attr('aria-sort', direcao === 'ASC' ? 'ascending' : 'descending');
            });
        }

        function popularTabelaDados(dados){
            let linhas = '';
            if (!dados.length) {
                linhas = '<tr><td colspan="6" class="text-center">Nenhuma ordem de serviço encontrada.</td></tr>';
            } else {
                dados.forEach(function(ordem){
                    const nome = $('<div>').text(ordem.NOME_CLIENTE ?? '').html();
                    const equipamento = $('<div>').text([ordem.MARCA_EQUIPAMENTO, ordem.MODELO_EQUIPAMENTO].filter(Boolean).join(' - ')).html();
                    const total = Number(ordem.VALOR_TOTAL || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    linhas += `
                        <tr>
                            <td>${Number(ordem.ID_CODIGO)}</td>
                            <td>${aplicarMascaraData(ordem.DT_HORA_CADASTRO)}</td>
                            <td>${nome}</td>
                            <td>${equipamento}</td>
                            <td>${total}</td>
                            <td>
                                <div class="acoes-linha">
                                    <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar" onclick="editarCadastro(${Number(ordem.ID_CODIGO)})">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle" title="Mais ações" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'>
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button type="button" class="dropdown-item" onclick="imprimirOrdemServico(${Number(ordem.ID_CODIGO)})">
                                                    <i class="fa-solid fa-print"></i><span> Impresso</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${Number(ordem.ID_CODIGO)})">
                                                    <i class="fa-solid fa-trash"></i><span> Excluir</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                });
            }
            $('#tabelaOrdensServico tbody').html(linhas);
        }

        function imprimirOrdemServico(idCodigo){
            const url = @json(route('ordem-servico.imprimir', ['idCodigo' => '__ID__'])).replace('__ID__', encodeURIComponent(idCodigo));
            window.open(url, '_blank', 'noopener');
        }

        function limparCamposModal(){
            $('#modalOrdemServico input[type="text"], #modalOrdemServico input[type="hidden"], #modalOrdemServico textarea').val('');
            $('#modalOrdemServico .campo-valor').val('0,00');
            const agora = new Date();
            const dataLocal = new Date(agora.getTime() - agora.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            $('#inputDataOrdemServico').val(dataLocal);
            $('#checkHouveDeslocamento').prop('checked', false);
            $('#areaValorDeslocamento').addClass('d-none');
            $('#listaClientes, #listaEquipamentos').removeClass('show').empty();
            sequenciaCliente++;
            sequenciaEquipamento++;
            $('#modalOrdemServicoLabel').text('Nova ordem de serviço');
            bootstrap.Tab.getOrCreateInstance(document.getElementById('abaDadosTab')).show();
            atualizarTotalizador();
        }

        function valorNumerico(id){
            const texto = String($('#' + id).val() || '0').replace(/\./g, '').replace(',', '.');
            return Number(texto) || 0;
        }

        function formatarValor(valor){
            const numero = Math.max(0, Number(valor) || 0);
            return numero.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function aplicarMascaraTelefone(valor){
            const digitos = String(valor || '').replace(/\D/g, '').slice(0, 11);
            if (digitos.length <= 2) return digitos ? '(' + digitos : '';
            if (digitos.length <= 6) return '(' + digitos.slice(0, 2) + ') ' + digitos.slice(2);
            const tamanhoPrefixo = digitos.length > 10 ? 5 : 4;
            return '(' + digitos.slice(0, 2) + ') ' + digitos.slice(2, 2 + tamanhoPrefixo) + '-' + digitos.slice(2 + tamanhoPrefixo);
        }

        function atualizarTotalizador(){
            const pecas = valorNumerico('inputValorPecas');
            const servicos = valorNumerico('inputValorServicos');
            const deslocamento = $('#checkHouveDeslocamento').is(':checked') ? valorNumerico('inputValorDeslocamento') : 0;
            const total = Math.round((pecas + servicos + deslocamento) * 100) / 100;
            $('#textoTotalOrdemServico').text(total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        }

        function salvarCadastro(){
            if (!$('#inputDataOrdemServico').val()) {
                informarAviso('Informe a data da ordem de serviço.');
                bootstrap.Tab.getOrCreateInstance(document.getElementById('abaDadosTab')).show();
                document.getElementById('inputDataOrdemServico').focus();
                return;
            }
            if (!$('#inputNomeCliente').val().trim() || $('#inputTelefoneCliente').val().replace(/\D/g, '').length < 10) {
                informarAviso('Informe o nome e o telefone do cliente.');
                bootstrap.Tab.getOrCreateInstance(document.getElementById('abaDadosTab')).show();
                return;
            }
            const camposValores = ['inputValorPecas', 'inputValorServicos', 'inputValorDeslocamento'];
            if (camposValores.some(id => !/^\d{1,3}(\.\d{3})*,\d{2}$|^\d+,\d{2}$/.test($('#' + id).val()))) {
                informarAviso('Informe valores válidos, maiores ou iguais a zero.');
                return;
            }
            const idCodigo = Number($('#inputIdOrdemServico').val()) || 0;
            const dados = {
                '_token': '{{ csrf_token() }}',
                'ID_CODIGO': idCodigo,
                'DATA_ORDEM_SERVICO': $('#inputDataOrdemServico').val(),
                'ID_CLIENTE': Number($('#inputIdCliente').val()) || 0,
                'NOME_CLIENTE': $('#inputNomeCliente').val().trim(),
                'TELEFONE_CLIENTE': $('#inputTelefoneCliente').val().trim(),
                'RUA_CLIENTE': $('#inputRuaCliente').val(),
                'NUMERO_CLIENTE': $('#inputNumeroCliente').val(),
                'BAIRRO_CLIENTE': $('#inputBairroCliente').val(),
                'CIDADE_CLIENTE': $('#inputCidadeCliente').val(),
                'ESTADO_CLIENTE': $('#inputEstadoCliente').val(),
                'CEP_CLIENTE': $('#inputCepCliente').val(),
                'ID_EQUIPAMENTO': Number($('#inputIdEquipamento').val()) || 0,
                'MARCA_EQUIPAMENTO': $('#inputMarcaEquipamento').val(),
                'MODELO_EQUIPAMENTO': $('#inputModeloEquipamento').val(),
                'TIPO_EQUIPAMENTO': $('#inputTipoEquipamento').val(),
                'NUMERO_SERIE': $('#inputNumeroSerie').val(),
                'DEFEITO': $('#inputDefeito').val(),
                'LAUDO_TECNICO': $('#inputLaudoTecnico').val(),
                'DESCRICAO_PECAS': $('#inputDescricaoPecas').val(),
                'VALOR_PECAS': valorNumerico('inputValorPecas'),
                'DESCRICAO_SERVICOS': $('#inputDescricaoServicos').val(),
                'VALOR_SERVICOS': valorNumerico('inputValorServicos'),
                'HOUVE_DESLOCAMENTO': $('#checkHouveDeslocamento').is(':checked') ? 'S' : 'N',
                'VALOR_DESLOCAMENTO': $('#checkHouveDeslocamento').is(':checked') ? valorNumerico('inputValorDeslocamento') : 0
            };
            exibirLoading('S');
            $('#btnSalvarOrdemServico').prop('disabled', true);
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: dados,
                url: idCodigo > 0 ? "{{ route('ordem-servico.alterar') }}" : "{{ route('ordem-servico.inserir') }}",
                success: function(r){
                    exibirLoading('N');
                    $('#btnSalvarOrdemServico').prop('disabled', false);
                    $('#modalOrdemServico').modal('hide');
                    limparCamposModal();
                    buscarDados();
                    informarSucesso(r.mensagem || 'Ordem de serviço salva com sucesso!');
                },
                error: function(err){
                    exibirLoading('N');
                    $('#btnSalvarOrdemServico').prop('disabled', false);
                    console.error('Erro ao salvar ordem de serviço:', err);
                    informarAviso(err.responseJSON?.message || 'Não foi possível salvar a ordem de serviço.');
                }
            });
        }

        function editarCadastro(idCodigo){
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idCodigo },
                url: "{{ route('ordem-servico.buscar') }}",
                success: function(r){
                    const ordem = (r.dados || [])[0];
                    exibirLoading('N');
                    if (!ordem) {
                        informarAviso('Ordem de serviço não encontrada.');
                        return;
                    }
                    limparCamposModal();
                    $('#inputDataOrdemServico').val(String(ordem.DT_HORA_CADASTRO || '').replace(' ', 'T').slice(0, 16));
                    const campos = {
                        inputIdOrdemServico: 'ID_CODIGO', inputIdCliente: 'ID_CLIENTE', inputNomeCliente: 'NOME_CLIENTE',
                        inputRuaCliente: 'RUA_CLIENTE', inputNumeroCliente: 'NUMERO_CLIENTE', inputBairroCliente: 'BAIRRO_CLIENTE',
                        inputCidadeCliente: 'CIDADE_CLIENTE', inputEstadoCliente: 'ESTADO_CLIENTE', inputCepCliente: 'CEP_CLIENTE',
                        inputTelefoneCliente: 'TELEFONE_CLIENTE', inputIdEquipamento: 'ID_EQUIPAMENTO',
                        inputMarcaEquipamento: 'MARCA_EQUIPAMENTO', inputModeloEquipamento: 'MODELO_EQUIPAMENTO',
                        inputTipoEquipamento: 'TIPO_EQUIPAMENTO', inputNumeroSerie: 'NUMERO_SERIE', inputDefeito: 'DEFEITO',
                        inputLaudoTecnico: 'LAUDO_TECNICO', inputDescricaoPecas: 'DESCRICAO_PECAS', inputValorPecas: 'VALOR_PECAS',
                        inputDescricaoServicos: 'DESCRICAO_SERVICOS', inputValorServicos: 'VALOR_SERVICOS', inputValorDeslocamento: 'VALOR_DESLOCAMENTO'
                    };
                    Object.keys(campos).forEach(function(id){
                        $('#' + id).val(ordem[campos[id]] ?? '');
                    });
                    ['inputValorPecas', 'inputValorServicos', 'inputValorDeslocamento'].forEach(function(id){
                        $('#' + id).val(formatarValor($('#' + id).val()));
                    });
                    $('#inputTelefoneCliente').val(aplicarMascaraTelefone($('#inputTelefoneCliente').val()));
                    $('#checkHouveDeslocamento').prop('checked', ordem.HOUVE_DESLOCAMENTO === 'S');
                    $('#areaValorDeslocamento').toggleClass('d-none', ordem.HOUVE_DESLOCAMENTO !== 'S');
                    $('#modalOrdemServicoLabel').text('Editar ordem de serviço #' + idCodigo);
                    atualizarTotalizador();
                    $('#modalOrdemServico').modal('show');
                },
                error: function(err){
                    exibirLoading('N');
                    console.error('Erro ao carregar ordem de serviço:', err);
                }
            });
        }

        function excluirCadastro(idCodigo){
            Swal.fire({
                title: 'Realmente deseja excluir a ordem de serviço?',
                text: 'A ordem será inativada e poderá ser consultada pelo filtro Inativos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then(function(resultado){
                if (!resultado.isConfirmed) return;
                exibirLoading('S');
                requisicaoAjax({
                    type: 'POST', dataType: 'json',
                    data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idCodigo },
                    url: "{{ route('ordem-servico.inativar') }}",
                    success: function(r){
                        exibirLoading('N');
                        buscarDados();
                        informarSucesso(r.mensagem);
                    },
                    error: function(err){
                        exibirLoading('N');
                        console.error('Erro ao excluir ordem de serviço:', err);
                    }
                });
            });
        }

        // Callback executado quando um cliente é escolhido no autocomplete.
        function selecionarCliente(cliente){
            $('#inputIdCliente').val(cliente.ID_CODIGO);
            $('#inputNomeCliente').val(cliente.NOME || '');
            $('#inputRuaCliente').val(cliente.RUA || '');
            $('#inputNumeroCliente').val(cliente.NUMERO || '');
            $('#inputBairroCliente').val(cliente.BAIRRO || '');
            $('#inputCidadeCliente').val(cliente.CIDADE || '');
            $('#inputEstadoCliente').val(cliente.ESTADO || '');
            $('#inputCepCliente').val(cliente.CEP || '');
            $('#inputTelefoneCliente').val(aplicarMascaraTelefone(cliente.TELEFONE || ''));
            $('#listaClientes').removeClass('show').empty();
            sequenciaCliente++;
        }

        // Callback do equipamento: somente marca, modelo e tipo são preenchidos.
        function selecionarEquipamento(equipamento){
            $('#inputIdEquipamento').val(equipamento.ID_CODIGO);
            $('#inputMarcaEquipamento').val(equipamento.MARCA || '');
            $('#inputModeloEquipamento').val(equipamento.MODELO || '');
            $('#inputTipoEquipamento').val(equipamento.TIPO || '');
            $('#listaEquipamentos').removeClass('show').empty();
            sequenciaEquipamento++;
        }

        $('#inputNomeCliente').on('input', function(){
            $('#inputIdCliente').val('');
            const termo = $(this).val().trim();
            const sequencia = ++sequenciaCliente;
            clearTimeout(temporizadorCliente);
            $('#listaClientes').removeClass('show').empty();
            if (termo.length < 2) return;
            temporizadorCliente = setTimeout(function(){
                requisicaoAjax({
                    type: 'POST', dataType: 'json',
                    data: { '_token': '{{ csrf_token() }}', 'FILTRO_BUSCA': termo },
                    url: "{{ route('ordem-servico.clientes.buscar') }}",
                    success: function(r){
                        if (sequencia !== sequenciaCliente) return;
                        const lista = $('#listaClientes').empty();
                        (r.dados || []).forEach(function(cliente){
                            $('<button type="button" class="dropdown-item"></button>')
                                .text((cliente.NOME || '') + (cliente.TELEFONE ? ' - ' + cliente.TELEFONE : ''))
                                .on('click', function(){ selecionarCliente(cliente); })
                                .appendTo(lista);
                        });
                        lista.toggleClass('show', lista.children().length > 0);
                    },
                    error: function(err){ console.error('Erro ao buscar clientes:', err); }
                });
            }, 350);
        });

        $('#inputMarcaEquipamento').on('input', function(){
            $('#inputIdEquipamento').val('');
            const termo = $(this).val().trim();
            const sequencia = ++sequenciaEquipamento;
            clearTimeout(temporizadorEquipamento);
            $('#listaEquipamentos').removeClass('show').empty();
            if (termo.length < 2) return;
            temporizadorEquipamento = setTimeout(function(){
                requisicaoAjax({
                    type: 'POST', dataType: 'json',
                    data: { '_token': '{{ csrf_token() }}', 'FILTRO_BUSCA': termo },
                    url: "{{ route('ordem-servico.equipamentos.buscar') }}",
                    success: function(r){
                        if (sequencia !== sequenciaEquipamento) return;
                        const lista = $('#listaEquipamentos').empty();
                        (r.dados || []).forEach(function(equipamento){
                            $('<button type="button" class="dropdown-item"></button>')
                                .text([equipamento.MARCA, equipamento.MODELO, equipamento.TIPO].filter(Boolean).join(' - '))
                                .on('click', function(){ selecionarEquipamento(equipamento); })
                                .appendTo(lista);
                        });
                        lista.toggleClass('show', lista.children().length > 0);
                    },
                    error: function(err){ console.error('Erro ao buscar equipamentos:', err); }
                });
            }, 350);
        });

        $('#checkHouveDeslocamento').on('change', function(){
            const marcado = $(this).is(':checked');
            $('#areaValorDeslocamento').toggleClass('d-none', !marcado);
            if (!marcado) $('#inputValorDeslocamento').val('0,00');
            if (marcado) $('#inputValorDeslocamento').trigger('focus').trigger('select');
            atualizarTotalizador();
        });
        $('#inputTelefoneCliente').on('input', function(){
            $(this).val(aplicarMascaraTelefone($(this).val()));
        });
        $('#inputCepCliente').on('input', function(){
            const cep = $(this).val().replace(/\D/g, '').slice(0, 8);
            $(this).val(cep.length > 5 ? cep.slice(0, 5) + '-' + cep.slice(5) : cep);
        });
        $('.campo-valor').on('input', function(){
            const digitos = $(this).val().replace(/\D/g, '').slice(0, 15);
            $(this).val(formatarValor(Number(digitos || 0) / 100));
            atualizarTotalizador();
        });
        $('#modalOrdemServico').on('shown.bs.modal', function(){
            bootstrap.Tab.getOrCreateInstance(document.getElementById('abaDadosTab')).show();
            // Aguarda a ativação da aba e o ciclo de foco do Bootstrap.
            setTimeout(function(){
                const campoData = document.getElementById('inputDataOrdemServico');
                campoData.focus({ preventScroll: true });
            }, 100);
        });
        $('#abaValoresTab').on('shown.bs.tab', function(){
            $('#inputDescricaoPecas').trigger('focus');
        });
        $('#btnNovaOrdemServico').on('click', function(){
            limparCamposModal();
            $('#modalOrdemServico').modal('show');
        });
        $('#inputFiltroOrdemServico').on('keyup', function(){
            clearTimeout(temporizadorBusca);
            temporizadorBusca = setTimeout(function(){ paginaAtual = 1; buscarDados(); }, 500);
        });
        $('#checkPesquisarInativos').on('change', function(){ paginaAtual = 1; buscarDados(); });
        $('#selectPorPagina').on('change', function(){
            registrosPorPagina = parseInt($(this).val(), 10);
            paginaAtual = 1;
            buscarDados();
        });
        $('#paginacaoLista').on('click', 'button[data-pagina]', function(){
            const pagina = parseInt($(this).data('pagina'), 10);
            if (!pagina || pagina === paginaAtual) return;
            paginaAtual = pagina;
            buscarDados();
        });
        $('#tabelaOrdensServico thead').on('click', 'th.ordenavel', function(evento){
            const coluna = $(this).data('coluna');
            const itemAtual = ordenacao.find(item => item.startsWith(coluna + ' '));
            let novoItem = null;
            if (!itemAtual) novoItem = coluna + ' ASC';
            else if (itemAtual.endsWith(' ASC')) novoItem = coluna + ' DESC';
            if (evento.shiftKey) {
                ordenacao = ordenacao.filter(item => !item.startsWith(coluna + ' '));
                if (novoItem) ordenacao.push(novoItem);
            } else {
                ordenacao = novoItem ? [novoItem] : [];
            }
            paginaAtual = 1;
            buscarDados();
        });
        $('#tabelaOrdensServico thead').on('keydown', 'th.ordenavel', function(evento){
            if (evento.key === 'Enter' || evento.key === ' ') {
                evento.preventDefault();
                $(this).trigger('click');
            }
        });
        $(document).on('click', function(evento){
            if (!$(evento.target).closest('#inputNomeCliente, #listaClientes').length) $('#listaClientes').removeClass('show');
            if (!$(evento.target).closest('#inputMarcaEquipamento, #listaEquipamentos').length) $('#listaEquipamentos').removeClass('show');
        });
        $(document).ready(function(){ buscarDados(); });
    </script>
@endpush
