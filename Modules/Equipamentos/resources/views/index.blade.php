@extends('layouts.app')

@section('title', 'Brasa | Equipamentos')
@section('page-title', 'Equipamentos')

@section('content')
    <div>
        <div class="d-flex gap-2">
            <div class="input-group flex-grow-1">
                <input type="text" class="form-control" placeholder="Buscar equipamento por marca, modelo, tipo ou série" id="inputFiltroEquipamento">
                <label class="input-group-text gap-2" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Inativos</span>
                </label>
            </div>
            <button type="button" class="btn btn-primary text-nowrap" id="btnNovoEquipamento">
                <i class="fa-solid fa-plus me-1"></i>Novo
            </button>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelaEquipamentos">
                    <thead>
                        <tr>
                            <th class="ordenavel" data-coluna="ID_CODIGO" tabindex="0" aria-sort="none">ID</th>
                            <th class="ordenavel" data-coluna="MARCA" tabindex="0" aria-sort="none">Marca</th>
                            <th class="ordenavel" data-coluna="MODELO" tabindex="0" aria-sort="none">Modelo</th>
                            <th class="ordenavel" data-coluna="TIPO" tabindex="0" aria-sort="none">Tipo</th>
                            <th class="ordenavel" data-coluna="DT_HORA_CADASTRO" tabindex="0" aria-sort="none">Cadastro</th>
                            <th>Ações</th>
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

    <div class="modal fade" id="modalEquipamento" tabindex="-1" aria-labelledby="modalEquipamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEquipamentoLabel">Novo equipamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="inputIdEquipamento">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label for="inputMarca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="inputMarca" maxlength="150" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="inputModelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="inputModelo" maxlength="150" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="inputTipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="inputTipo" maxlength="150" placeholder="Ex.: Impressora, notebook, monitor" required>
                        </div>
                        <div class="col-12">
                            <label for="inputObservacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="inputObservacoes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvarEquipamento" onclick="salvarCadastro()">Salvar</button>
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

        function getFuncaoPadrao(){
            return buscarDados();
        }

        function escaparHtml(valor){
            return $('<div>').text(valor ?? '').html();
        }

        function buscarDados(){
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'FILTRO_BUSCA': $('#inputFiltroEquipamento').val(),
                    'INATIVOS': $('#checkPesquisarInativos').is(':checked') ? 1 : 0,
                    'ORDENACAO_LISTA': ordenacao.join(','),
                    'PAGINA': paginaAtual,
                    'POR_PAGINA': registrosPorPagina
                },
                url: "{{ route('equipamentos.buscar') }}",
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
                    console.error('Erro ao buscar equipamentos:', err);
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
                    <button type="button" class="page-link" data-pagina="${pagina}"
                            ${opcoes.desabilitado ? 'disabled' : ''}
                            ${opcoes.ativo ? 'aria-current="page"' : ''}
                            ${opcoes.aria ? `aria-label="${opcoes.aria}"` : ''}>${rotulo}</button>
                </li>`;
            const paginas = [];
            for(let p = 1; p <= totalPaginas; p++) {
                if(p === 1 || p === totalPaginas || Math.abs(p - paginaAtual) <= 2) paginas.push(p);
            }
            let html = botao('<i class="fa-solid fa-angle-left"></i>', paginaAtual - 1, { desabilitado: paginaAtual <= 1, aria: 'Página anterior' });
            let anterior = 0;
            paginas.forEach(function(p){
                if(p - anterior > 1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                html += botao(p, p, { ativo: p === paginaAtual });
                anterior = p;
            });
            html += botao('<i class="fa-solid fa-angle-right"></i>', paginaAtual + 1, { desabilitado: paginaAtual >= totalPaginas, aria: 'Próxima página' });
            $('#paginacaoLista').html(html);
        }

        function atualizarIndicadoresOrdenacao(){
            const cabecalhos = $('#tabelaEquipamentos th.ordenavel');
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
            if (!dados || dados.length === 0) {
                linhas = '<tr><td colspan="7" class="text-center">Nenhum equipamento encontrado.</td></tr>';
            } else {
                dados.forEach(function(equipamento){
                    const id = Number(equipamento.ID_CODIGO);
                    linhas += `<tr>
                        <td>${id}</td>
                        <td>${escaparHtml(equipamento.MARCA)}</td>
                        <td>${escaparHtml(equipamento.MODELO)}</td>
                        <td>${escaparHtml(equipamento.TIPO)}</td>
                        <td>${equipamento.DT_HORA_CADASTRO ? aplicarMascaraData(equipamento.DT_HORA_CADASTRO) : ''}</td>
                        <td><div class="acoes-linha">
                            <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar" onclick="editarCadastro(${id})">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle"
                                        title="Mais ações" aria-label="Mais ações" aria-expanded="false"
                                        data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'>
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${id})">
                                        <i class="fa-solid fa-trash"></i><span>Excluir</span>
                                    </button></li>
                                </ul>
                            </div>
                        </div></td>
                    </tr>`;
                });
            }
            $('#tabelaEquipamentos tbody').html(linhas);
        }

        function limparCamposModal(){
            $('#modalEquipamento input, #modalEquipamento textarea').val('');
        }

        function salvarCadastro(){
            const marca = $('#inputMarca').val().trim();
            const modelo = $('#inputModelo').val().trim();
            const tipo = $('#inputTipo').val().trim();
            if (!marca || !modelo || !tipo) {
                informarAviso('Informe a marca, o modelo e o tipo do equipamento.');
                (!marca ? $('#inputMarca') : (!modelo ? $('#inputModelo') : $('#inputTipo'))).trigger('focus');
                return;
            }
            const idEquipamento = Number($('#inputIdEquipamento').val() || 0);
            const dados = {
                '_token': '{{ csrf_token() }}',
                'MARCA': marca,
                'MODELO': modelo,
                'TIPO': tipo,
                'OBSERVACOES': $('#inputObservacoes').val()
            };
            if (idEquipamento > 0) dados.ID_CODIGO = idEquipamento;
            $('#btnSalvarEquipamento').prop('disabled', true);
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: dados,
                url: idEquipamento > 0 ? "{{ route('equipamento.alterar') }}" : "{{ route('equipamento.inserir') }}",
                success: function(r){
                    exibirLoading('N');
                    $('#btnSalvarEquipamento').prop('disabled', false);
                    if (r.sucesso === false || r.success === false) {
                        informarAviso(r.mensagem || 'Não foi possível salvar o equipamento.');
                        return;
                    }
                    $('#modalEquipamento').modal('hide');
                    buscarDados();
                    informarSucesso(r.mensagem || 'Equipamento salvo com sucesso!');
                },
                error: function(err){
                    exibirLoading('N');
                    $('#btnSalvarEquipamento').prop('disabled', false);
                    informarAviso('Não foi possível salvar o equipamento. Confira os campos.');
                    console.error('Erro ao salvar equipamento:', err);
                }
            });
        }

        function editarCadastro(idEquipamento){
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idEquipamento },
                url: "{{ route('equipamentos.buscar') }}",
                success: function(r){
                    exibirLoading('N');
                    const equipamento = (r.dados || [])[0];
                    if (!equipamento) {
                        informarAviso('Equipamento não encontrado.');
                        return;
                    }
                    limparCamposModal();
                    $('#inputIdEquipamento').val(equipamento.ID_CODIGO);
                    $('#inputMarca').val(equipamento.MARCA ?? '');
                    $('#inputModelo').val(equipamento.MODELO ?? '');
                    $('#inputTipo').val(equipamento.TIPO ?? '');
                    $('#inputObservacoes').val(equipamento.OBSERVACOES ?? '');
                    $('#modalEquipamentoLabel').text('Editar equipamento');
                    $('#modalEquipamento').modal('show');
                },
                error: function(err){
                    exibirLoading('N');
                    console.error('Erro ao buscar equipamento:', err);
                }
            });
        }

        function excluirCadastro(idEquipamento){
            Swal.fire({
                title: 'Realmente deseja inativar o equipamento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, inativar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then(function(resultado){
                if (!resultado.isConfirmed) return;
                exibirLoading('S');
                requisicaoAjax({
                    type: 'POST',
                    dataType: 'json',
                    data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idEquipamento },
                    url: "{{ route('equipamento.inativar') }}",
                    success: function(r){
                        exibirLoading('N');
                        if (r.sucesso === false || r.success === false) {
                            informarAviso(r.mensagem || 'Não foi possível inativar o equipamento.');
                            return;
                        }
                        buscarDados();
                        informarSucesso(r.mensagem || 'Equipamento inativado!');
                    },
                    error: function(err){
                        exibirLoading('N');
                        console.error('Erro ao inativar equipamento:', err);
                    }
                });
            });
        }

        $('#tabelaEquipamentos thead').on('click', 'th.ordenavel', function(evento){
            const coluna = $(this).data('coluna');
            const atual = ordenacao.find(item => item.startsWith(coluna + ' '));
            const novo = !atual ? coluna + ' ASC' : (atual.endsWith(' ASC') ? coluna + ' DESC' : null);
            if(evento.shiftKey) {
                ordenacao = ordenacao.filter(item => !item.startsWith(coluna + ' '));
                if(novo) ordenacao.push(novo);
            } else {
                ordenacao = novo ? [novo] : [];
            }
            paginaAtual = 1;
            buscarDados();
        });
        $('#tabelaEquipamentos thead').on('keydown', 'th.ordenavel', function(evento){
            if (evento.key === 'Enter' || evento.key === ' ') {
                evento.preventDefault();
                $(this).trigger('click');
            }
        });
        $('#paginacaoLista').on('click', 'button[data-pagina]', function(){
            const pagina = Number($(this).data('pagina'));
            if (pagina > 0 && pagina !== paginaAtual) {
                paginaAtual = pagina;
                buscarDados();
            }
        });
        $('#selectPorPagina').on('change', function(){
            registrosPorPagina = Number($(this).val());
            paginaAtual = 1;
            buscarDados();
        });
        $('#btnNovoEquipamento').on('click', function(){
            limparCamposModal();
            $('#modalEquipamentoLabel').text('Novo equipamento');
            $('#modalEquipamento').modal('show');
        });
        $('#modalEquipamento').on('shown.bs.modal', function(){
            $('#inputMarca').trigger('focus');
        });
        $('#inputFiltroEquipamento').on('input', function(){
            clearTimeout(temporizadorBusca);
            temporizadorBusca = setTimeout(function(){
                paginaAtual = 1;
                buscarDados();
            }, 500);
        });
        $('#checkPesquisarInativos').on('change', function(){
            paginaAtual = 1;
            buscarDados();
        });
        $(document).ready(function(){ buscarDados(); });
    </script>
@endpush
