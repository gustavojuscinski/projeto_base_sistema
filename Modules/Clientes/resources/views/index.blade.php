@extends('layouts.app')

@section('title', 'Brasa | Clientes')
@section('page-title', 'Clientes')

@section('content')
    @php
        $usuarioAtual = usuarioAutenticado();
        $idUsuarioAtual = (int) ($usuarioAtual['ID_USUARIO'] ?? 0);
        $idPerfilAtual = (int) (\Illuminate\Support\Facades\DB::table('usuarios')
            ->where('ID_USUARIO', $idUsuarioAtual)->value('ID_PERFIL') ?? 0);
        $descricaoPerfil = (string) (\Illuminate\Support\Facades\DB::table('perfis')
            ->where('ID_CODIGO', $idPerfilAtual)->value('DESCRICAO') ?? '');
        $administrador = mb_strtolower(trim($descricaoPerfil), 'UTF-8') === 'administrador';
    @endphp
    <div>
        <div class="d-flex justify-content-between align-items-center gap-2 d-lg-none mb-2">
            <button type="button" class="btn btn-outline-secondary" id="btnAlternarFiltrosCliente"
                    data-bs-toggle="collapse" data-bs-target="#painelFiltrosCliente"
                    aria-expanded="false" aria-controls="painelFiltrosCliente">
                <i class="fa-solid fa-filter me-1"></i><span id="rotuloFiltrosCliente">Mostrar filtros</span>
            </button>
            @if ($administrador)
                <button type="button" class="btn btn-primary text-nowrap" id="btnNovoClienteMobile">
                    <i class="fa-solid fa-plus me-1"></i>Novo
                </button>
            @endif
        </div>

        <div id="painelFiltrosCliente" class="collapse d-lg-block">
            <div class="d-flex flex-column flex-lg-row align-items-stretch gap-2">
                <input type="text" class="form-control flex-grow-1" placeholder="Buscar cliente, telefone, CPF/CNPJ, e-mail ou cidade" id="inputFiltroCliente">
                <label class="d-flex align-items-center gap-2 px-2 py-2 border rounded" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Pesquisar inativos</span>
                </label>
                @if ($administrador)
                    <button type="button" class="btn btn-primary text-nowrap d-none d-lg-inline-block" id="btnNovoCliente">
                        <i class="fa-solid fa-plus me-1"></i>Novo
                    </button>
                @endif
            </div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-striped" id="tabelaClientes" aria-label="Tabela de clientes">
                    <thead>
                        <tr>
                            <th class="ordenavel" data-coluna="ID_CODIGO" tabindex="0" aria-sort="none">ID</th>
                            <th class="ordenavel" data-coluna="NOME" tabindex="0" aria-sort="none">Nome</th>
                            <th class="ordenavel" data-coluna="TELEFONE" tabindex="0" aria-sort="none">Telefone</th>
                            <th>E-mail</th>
                            <th class="ordenavel" data-coluna="CIDADE" tabindex="0" aria-sort="none">Cidade/UF</th>
                            <th class="ordenavel" data-coluna="DT_HORA_CADASTRO" tabindex="0" aria-sort="none">Cadastro</th>
                            @if ($administrador)
                                <th>Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="cardsClientesMobile" class="d-lg-none p-2" aria-live="polite"></div>
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

    @if ($administrador)
    <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteLabel">Novo cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="inputIdCliente">
                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <label for="inputCep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="inputCep" maxlength="9" inputmode="numeric" placeholder="00000-000">
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="inputNome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="inputNome" maxlength="255" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="inputTelefone" class="form-label">Telefone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="inputTelefone" maxlength="15" inputmode="tel" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="inputCpfCnpj" class="form-label">CPF/CNPJ</label>
                            <input type="text" class="form-control" id="inputCpfCnpj" maxlength="18" inputmode="numeric">
                        </div>
                        <div class="col-12">
                            <label for="inputEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="inputEmail" maxlength="255">
                        </div>
                        <div class="col-12 col-md-9">
                            <label for="inputRua" class="form-label">Rua</label>
                            <input type="text" class="form-control" id="inputRua" maxlength="255">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="inputNumero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="inputNumero" maxlength="30">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="inputComplemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="inputComplemento" maxlength="150">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="inputBairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="inputBairro" maxlength="150">
                        </div>
                        <div class="col-12 col-md-9">
                            <label for="inputCidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="inputCidade" maxlength="150">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="inputEstado" class="form-label">UF</label>
                            <input type="text" class="form-control text-uppercase" id="inputEstado" maxlength="2" placeholder="PR">
                        </div>
                        <div class="col-12">
                            <label for="inputObservacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="inputObservacoes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvarCliente" onclick="salvarCadastro()">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
    <script>
        var ordenacao = [];
        var paginaAtual = 1;
        var registrosPorPagina = 10;
        let temporizadorBusca;
        const usuarioEhAdministradorCliente = @json($administrador);

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
                    'FILTRO_BUSCA': $('#inputFiltroCliente').val(),
                    'INATIVOS': $('#checkPesquisarInativos').is(':checked') ? 1 : 0,
                    'ORDENACAO_LISTA': ordenacao.join(','),
                    'PAGINA': paginaAtual,
                    'POR_PAGINA': registrosPorPagina
                },
                url: "{{ route('clientes.buscar') }}",
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
                    console.error('Erro ao buscar clientes:', err);
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
            const cabecalhos = $('#tabelaClientes th.ordenavel');
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
            let cards = '';
            if (!dados || dados.length === 0) {
                linhas = `<tr><td colspan="${usuarioEhAdministradorCliente ? 7 : 6}" class="text-center">Nenhum cliente encontrado.</td></tr>`;
                cards = '<div class="text-center text-secondary py-4">Nenhum cliente encontrado.</div>';
            } else {
                dados.forEach(function(cliente){
                    const id = Number(cliente.ID_CODIGO);
                    const nome = escaparHtml(cliente.NOME);
                    const telefone = escaparHtml(cliente.TELEFONE) || '-';
                    const email = escaparHtml(cliente.EMAIL) || '-';
                    const cidadeUf = escaparHtml([cliente.CIDADE, cliente.ESTADO].filter(Boolean).join(' / ')) || '-';
                    const dataCadastro = cliente.DT_HORA_CADASTRO ? escaparHtml(aplicarMascaraData(cliente.DT_HORA_CADASTRO)) : '-';
                    let htmlBotoes = '';

                    if (usuarioEhAdministradorCliente) {
                        htmlBotoes = `<div class="acoes-linha">
                            <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar" aria-label="Editar cliente" onclick="editarCadastro(${id})">
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
                        </div>`;
                    }

                    linhas += `<tr>
                        <td>${id}</td><td>${nome}</td><td>${telefone}</td><td>${email}</td>
                        <td>${cidadeUf}</td><td>${dataCadastro}</td>
                        ${usuarioEhAdministradorCliente ? `<td>${htmlBotoes}</td>` : ''}
                    </tr>`;
                    cards += `<div class="card mb-2 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 border-bottom pb-2 mb-2">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-break">${nome}</div>
                                    <small class="text-secondary">Cliente #${id}</small>
                                </div>
                                ${htmlBotoes}
                            </div>
                            <div class="row g-2 small">
                                <div class="col-12"><div class="text-secondary">Telefone</div><div class="text-break">${telefone}</div></div>
                                <div class="col-12"><div class="text-secondary">E-mail</div><div class="text-break">${email}</div></div>
                                <div class="col-12"><div class="text-secondary">Cidade/UF</div><div class="text-break">${cidadeUf}</div></div>
                                <div class="col-12"><div class="text-secondary">Data cadastro</div><div>${dataCadastro}</div></div>
                            </div>
                        </div>
                    </div>`;
                });
            }
            $('#tabelaClientes tbody').html(linhas);
            $('#cardsClientesMobile').html(cards);
        }

        function limparCamposModal(){
            $('#modalCliente input:not([type=checkbox]), #modalCliente textarea').val('');
        }

        function salvarCadastro(){
            if (!usuarioEhAdministradorCliente) return;
            const nome = $('#inputNome').val().trim();
            const telefone = $('#inputTelefone').val().trim();
            if (!nome || !telefone) {
                informarAviso('Informe o nome e o telefone do cliente.');
                (!nome ? $('#inputNome') : $('#inputTelefone')).trigger('focus');
                return;
            }
            const idCliente = Number($('#inputIdCliente').val() || 0);
            const dados = {
                '_token': '{{ csrf_token() }}',
                'NOME': nome,
                'TELEFONE': telefone,
                'EMAIL': $('#inputEmail').val().trim(),
                'CPF_CNPJ': $('#inputCpfCnpj').val().trim(),
                'CEP': $('#inputCep').val().trim(),
                'RUA': $('#inputRua').val().trim(),
                'NUMERO': $('#inputNumero').val().trim(),
                'COMPLEMENTO': $('#inputComplemento').val().trim(),
                'BAIRRO': $('#inputBairro').val().trim(),
                'CIDADE': $('#inputCidade').val().trim(),
                'ESTADO': $('#inputEstado').val().trim().toUpperCase(),
                'OBSERVACOES': $('#inputObservacoes').val()
            };
            if (idCliente > 0) dados.ID_CODIGO = idCliente;
            $('#btnSalvarCliente').prop('disabled', true);
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: dados,
                url: idCliente > 0 ? "{{ route('cliente.alterar') }}" : "{{ route('cliente.inserir') }}",
                success: function(r){
                    exibirLoading('N');
                    $('#btnSalvarCliente').prop('disabled', false);
                    if (r.sucesso === false || r.success === false) {
                        informarAviso(r.mensagem || 'Não foi possível salvar o cliente.');
                        return;
                    }
                    $('#modalCliente').modal('hide');
                    buscarDados();
                    informarSucesso(r.mensagem || 'Cliente salvo com sucesso!');
                },
                error: function(err){
                    exibirLoading('N');
                    $('#btnSalvarCliente').prop('disabled', false);
                    informarAviso('Não foi possível salvar o cliente. Confira os campos.');
                    console.error('Erro ao salvar cliente:', err);
                }
            });
        }

        function editarCadastro(idCliente){
            if (!usuarioEhAdministradorCliente) return;
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idCliente },
                url: "{{ route('clientes.buscar') }}",
                success: function(r){
                    exibirLoading('N');
                    const cliente = (r.dados || [])[0];
                    if (!cliente) {
                        informarAviso('Cliente não encontrado.');
                        return;
                    }
                    limparCamposModal();
                    $('#inputIdCliente').val(cliente.ID_CODIGO);
                    const campos = { NOME: 'Nome', TELEFONE: 'Telefone', EMAIL: 'Email', CPF_CNPJ: 'CpfCnpj',
                        CEP: 'Cep', RUA: 'Rua', NUMERO: 'Numero', COMPLEMENTO: 'Complemento',
                        BAIRRO: 'Bairro', CIDADE: 'Cidade', ESTADO: 'Estado', OBSERVACOES: 'Observacoes' };
                    Object.keys(campos).forEach(function(campo){
                        $('#input' + campos[campo]).val(cliente[campo] ?? '');
                    });
                    $('#modalClienteLabel').text('Editar cliente');
                    $('#modalCliente').modal('show');
                },
                error: function(err){
                    exibirLoading('N');
                    console.error('Erro ao buscar cliente:', err);
                }
            });
        }

        function excluirCadastro(idCliente){
            if (!usuarioEhAdministradorCliente) return;
            Swal.fire({
                title: 'Realmente deseja inativar o cliente?',
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
                    data: { '_token': '{{ csrf_token() }}', 'ID_CODIGO': idCliente },
                    url: "{{ route('cliente.inativar') }}",
                    success: function(r){
                        exibirLoading('N');
                        if (r.sucesso === false || r.success === false) {
                            informarAviso(r.mensagem || 'Não foi possível inativar o cliente.');
                            return;
                        }
                        buscarDados();
                        informarSucesso(r.mensagem || 'Cliente inativado!');
                    },
                    error: function(err){
                        exibirLoading('N');
                        console.error('Erro ao inativar cliente:', err);
                    }
                });
            });
        }

        function mascaraTelefone(valor){
            const digitos = valor.replace(/\D/g, '').slice(0, 11);
            if (!digitos) return '';
            if (digitos.length <= 2) return '(' + digitos;
            if (digitos.length <= 6) return '(' + digitos.slice(0, 2) + ') ' + digitos.slice(2);
            const tamanhoPrefixo = digitos.length > 10 ? 5 : 4;
            return '(' + digitos.slice(0, 2) + ') ' + digitos.slice(2, 2 + tamanhoPrefixo) + '-' + digitos.slice(2 + tamanhoPrefixo);
        }

        function mascaraCpfCnpj(valor){
            const digitos = valor.replace(/\D/g, '').slice(0, 14);
            if (digitos.length <= 11) {
                return digitos.replace(/^(\d{3})(\d)/, '$1.$2').replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
                    .replace(/\.(\d{3})(\d)/, '.$1-$2');
            }
            return digitos.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                .replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
        }

        $('#inputTelefone').on('input', function(){ $(this).val(mascaraTelefone($(this).val())); });
        $('#inputCep').on('input', function(){
            const digitos = $(this).val().replace(/\D/g, '').slice(0, 8);
            $(this).val(digitos.length > 5 ? digitos.slice(0, 5) + '-' + digitos.slice(5) : digitos);
        });
        $('#inputCpfCnpj').on('input', function(){ $(this).val(mascaraCpfCnpj($(this).val())); });
        $('#inputEstado').on('input', function(){ $(this).val($(this).val().replace(/[^a-zA-Z]/g, '').slice(0, 2).toUpperCase()); });

        $('#tabelaClientes thead').on('click', 'th.ordenavel', function(evento){
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
        $('#tabelaClientes thead').on('keydown', 'th.ordenavel', function(evento){
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
        $('#btnNovoCliente, #btnNovoClienteMobile').on('click', function(){
            limparCamposModal();
            $('#modalClienteLabel').text('Novo cliente');
            $('#modalCliente').modal('show');
        });
        $('#modalCliente').on('shown.bs.modal', function(){
            $('#inputCep').trigger('focus');
        });
        $('#painelFiltrosCliente').on('shown.bs.collapse hidden.bs.collapse', function(){
            $('#rotuloFiltrosCliente').text($(this).hasClass('show') ? 'Ocultar filtros' : 'Mostrar filtros');
        });

        $('#inputFiltroCliente').on('input', function(){
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
