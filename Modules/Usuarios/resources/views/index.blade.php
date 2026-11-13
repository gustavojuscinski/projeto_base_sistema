@extends('layouts.app')

@section('title', 'Brasa | Usuários')
@section('page-title', 'Usuários')

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
            <button type="button" class="btn btn-outline-secondary" id="btnAlternarFiltrosUsuario"
                    data-bs-toggle="collapse" data-bs-target="#painelFiltrosUsuario"
                    aria-expanded="false" aria-controls="painelFiltrosUsuario">
                <i class="fa-solid fa-filter me-1"></i><span id="rotuloFiltrosUsuario">Mostrar filtros</span>
            </button>
            @if ($administrador)
                <button type="button" class="btn btn-primary text-nowrap" id="btnNovoUsuarioMobile">
                    <i class="fa-solid fa-plus me-1"></i>Novo
                </button>
            @endif
        </div>

        <div id="painelFiltrosUsuario" class="collapse d-lg-block">
            <div class="d-flex flex-column flex-lg-row align-items-stretch gap-2">
                <input type="text" class="form-control flex-grow-1" placeholder="Buscar usuário" id="inputFiltroUsuario">
                <label class="d-flex align-items-center gap-2 px-2 py-2 border rounded" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Pesquisar inativos</span>
                </label>
                @if ($administrador)
                    <button type="button" class="btn btn-primary text-nowrap d-none d-lg-inline-block" id="btnNovoUsuario">
                        <i class="fa-solid fa-plus me-1"></i>Novo
                    </button>
                @endif
            </div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-striped" id="tabelaUsuarios">
                    <thead>
                        <tr>
                            {{-- Colunas com a classe "ordenavel" são clicáveis. data-coluna = coluna do SELECT --}}
                            <th class="ordenavel" data-coluna="ID_USUARIO" tabindex="0" aria-sort="none" style="width: 5vw;">ID</th>
                            <th class="ordenavel" data-coluna="NOME_USUARIO" tabindex="0" aria-sort="none" style="width: 35vw;">Nome</th>
                            <th class="ordenavel" data-coluna="DESCRICAO_PERFIL" tabindex="0" aria-sort="none" style="width: 15vw;">Perfil</th>
                            <th class="ordenavel" data-coluna="DESCRICAO_EQUIPE" tabindex="0" aria-sort="none" style="width: 15vw;">Equipe</th>
                            <th class="ordenavel" data-coluna="DT_HORA_CADASTRO" tabindex="0" aria-sort="none" style="width: 15vw;">Data Cadastro</th>
                            @if ($administrador)
                            <th style="width: 10vw;">Ações</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>

            <div id="cardsUsuariosMobile" class="d-lg-none p-2" aria-live="polite"></div>

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
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuarioLabel">Novo usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="inputIdUsuario">
                    <div class="mb-3">
                        <label for="inputNome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="inputNome" name="nome" placeholder="Digite o nome do usuário" required>
                    </div>

                    <div class="mb-3">
                        <label for="inputSenha" class="form-label">Senha</label>
                        <input type="text" class="form-control" id="inputSenha" name="senha" placeholder="Digite a senha" required>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-12 col-md-6">
                            <label for="selectPerfis" class="form-label">Perfil</label>
                            <select class="form-select" id="selectPerfis">
                                <option value="0">Selecione um perfil...</option>
                            </select>
                        </div>

                        <div class="mb-3 col-12 col-md-6">
                            <label for="selectEquipes" class="form-label">Equipe</label>
                            <select class="form-select" id="selectEquipes">
                                <option value="0">Selecione uma equipe...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="button" class="btn btn-primary" onclick="salvarCadastro()">
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPermissoes" tabindex="-1" aria-labelledby="modalPermissoesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPermissoesLabel">Permissões do Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="inputIdUsuarioPermissoes">

                    <div class="mb-3">
                        <input type="text" class="form-control" id="inputFiltroPermissoes"
                               placeholder="Buscar permissão por nome ou descrição" aria-label="Buscar permissão">
                    </div>

                    <div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
                        <table class="table table-striped align-middle mb-0" id="tabelaPermissoes">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">ID</th>
                                    <th style="width: 30%;">Nome</th>
                                    <th>Descrição</th>
                                    <th class="text-center" style="width: 12%;">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr><td colspan="3" class="text-center">Carregando permissões...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
        const usuarioEhAdministrador = @json($administrador);
        const textoSeguroUsuario = valor => $('<div>').text(valor ?? '').html();

        function getFuncaoPadrao() {
            return buscarDados();
        }

        function buscarDados() {
            exibirLoading('S');
            const filtroBusca = $('#inputFiltroUsuario').val();
            const pesquisarInativos = $('#checkPesquisarInativos').is(':checked') ? 1 : 0;

            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'FILTRO_BUSCA': filtroBusca,
                    'INATIVOS': pesquisarInativos,
                    'ORDENACAO_LISTA': ordenacao.join(','),
                    'PAGINA': paginaAtual,
                    'POR_PAGINA': registrosPorPagina
                },
                url: "{{ route('usuarios.buscar') }}",
                success: function (r) {
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
                error: function (err) {
                    exibirLoading('N');
                    console.error('Erro ao buscar dados:', err);
                }
            });
        }

        // ----- Rodapé: contagem (esquerda) e paginação (centro) -----
        function renderizarPaginacao(total) {
            const totalPaginas = Math.max(1, Math.ceil(total / registrosPorPagina));
            const inicio = total === 0 ? 0 : (paginaAtual - 1) * registrosPorPagina + 1;
            const fim = Math.min(paginaAtual * registrosPorPagina, total);

            $('#infoContagem').text(
                total === 0 ? 'Nenhum registro' : `Exibindo ${inicio} a ${fim} de ${total} registros`
            );

            const botao = (rotulo, pagina, opcoes = {}) => `
                <li class="page-item ${opcoes.ativo ? 'active' : ''} ${opcoes.desabilitado ? 'disabled' : ''}">
                    <button type="button" class="page-link" data-pagina="${pagina}"
                            ${opcoes.desabilitado ? 'disabled' : ''}
                            ${opcoes.ativo ? 'aria-current="page"' : ''}
                            ${opcoes.aria ? `aria-label="${opcoes.aria}"` : ''}>${rotulo}</button>
                </li>`;

            const reticencias = `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;

            // Mostra: primeira, última e 2 páginas ao redor da atual
            const paginas = [];
            for (let p = 1; p <= totalPaginas; p++) {
                if (p === 1 || p === totalPaginas || Math.abs(p - paginaAtual) <= 2) {
                    paginas.push(p);
                }
            }

            let html = botao('<i class="fa-solid fa-angle-left"></i>', paginaAtual - 1, {
                desabilitado: paginaAtual <= 1,
                aria: 'Página anterior'
            });

            let anterior = 0;
            paginas.forEach(function (p) {
                if (p - anterior > 1) {
                    html += reticencias;
                }

                html += botao(p, p, { ativo: p === paginaAtual });
                anterior = p;
            });

            html += botao('<i class="fa-solid fa-angle-right"></i>', paginaAtual + 1, {
                desabilitado: paginaAtual >= totalPaginas,
                aria: 'Próxima página'
            });

            $('#paginacaoLista').html(html);
        }

        // ----- Ordenação: setinhas no título das colunas -----
        function atualizarIndicadoresOrdenacao() {
            const cabecalhos = $('#tabelaUsuarios th.ordenavel');

            cabecalhos.removeClass('ordenado-asc ordenado-desc').attr('aria-sort', 'none');

            ordenacao.forEach(function (item) {
                const [coluna, direcao] = item.split(' ');
                const cabecalho = cabecalhos.filter('[data-coluna="' + coluna + '"]');

                cabecalho
                    .addClass(direcao === 'ASC' ? 'ordenado-asc' : 'ordenado-desc')
                    .attr('aria-sort', direcao === 'ASC' ? 'ascending' : 'descending');
            });
        }

        function popularTabelaDados(dados) {
            let linhas = '';
            let cards = '';

            if (!dados || dados.length === 0) {
                linhas = `<tr><td colspan="${usuarioEhAdministrador ? 6 : 5}" class="text-center">Nenhum usuário encontrado.</td></tr>`;
                cards = '<div class="text-center text-secondary py-4">Nenhum usuário encontrado.</div>';
            } else {
                dados.forEach(function (usuario) {
                    const id = Number(usuario.ID_USUARIO);
                    const nome = textoSeguroUsuario(usuario.NOME_USUARIO);
                    const perfil = textoSeguroUsuario(usuario.DESCRICAO_PERFIL);
                    const equipe = textoSeguroUsuario(usuario.DESCRICAO_EQUIPE);
                    const data = textoSeguroUsuario(aplicarMascaraData(usuario.DT_HORA_CADASTRO));
                    let htmlBotoes = '';

                    if (usuarioEhAdministrador) {
                        htmlBotoes = `<div class="acoes-linha">
                            <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar"
                                    onclick="editarCadastro(${id})"><i class="fa-solid fa-edit"></i></button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle"
                                        title="Mais ações" aria-label="Mais ações" aria-expanded="false"
                                        data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'>
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button type="button" class="dropdown-item" onclick="visualizarPermissoes(${id})"><i class="fa-solid fa-key"></i><span>Permissões</span></button></li>
                                    <li><button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${id})"><i class="fa-solid fa-trash"></i><span>Excluir</span></button></li>
                                </ul>
                            </div>
                        </div>`;
                    }

                    linhas += `<tr>
                        <td>${id}</td><td>${nome}</td><td>${perfil}</td>
                        <td>${equipe}</td><td>${data}</td>
                        ${usuarioEhAdministrador ? `<td>${htmlBotoes}</td>` : ''}
                    </tr>`;

                    cards += `<div class="card mb-2 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 border-bottom pb-2 mb-2">
                                <div class="min-w-0"><div class="fw-semibold text-break">${nome}</div>
                                    <small class="text-secondary">Usuário #${id}</small></div>
                                ${htmlBotoes}
                            </div>
                            <div class="row g-2 small">
                                <div class="col-6"><div class="text-secondary">Perfil</div><div class="text-break fw-semibold">${perfil || '-'}</div></div>
                                <div class="col-6"><div class="text-secondary">Equipe</div><div class="text-break fw-semibold">${equipe || '-'}</div></div>
                                <div class="col-12"><div class="text-secondary">Data cadastro</div><div>${data || '-'}</div></div>
                            </div>
                        </div>
                    </div>`;
                });
            }
            $('#tabelaUsuarios tbody').html(linhas);
            $('#cardsUsuariosMobile').html(cards);
        }

        function limparCamposModal(){
            $('#inputIdUsuario').val('');
            $('#inputNome').val('');
            $('#inputSenha').val('');
            $('#selectPerfis').val('0');
            $('#selectEquipes').val('0');
        }

        function salvarCadastro(){
            if (!usuarioEhAdministrador) return;
            exibirLoading('S');
            var idUsuario = $('#inputIdUsuario').val();
            var nomeUsuario = $('#inputNome').val();
            var senhaUsuario = $('#inputSenha').val();
            var perfilUsuario = $('#selectPerfis').val();
            var equipeUsuario = $('#selectEquipes').val();

            if(idUsuario > 0){
                requisicaoAjax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'ID_USUARIO': idUsuario,
                        'NOME_USUARIO': nomeUsuario,
                        'SENHA_USUARIO': senhaUsuario,
                        'ID_PERFIL': perfilUsuario,
                        'ID_EQUIPE': equipeUsuario
                    },
                    url: "{{ route('usuario.alterar') }}",
                    success: function (r) {
                        limparCamposModal();
                        $('#modalUsuario').modal('hide');
                        exibirLoading('N');
                        buscarDados();
                        informarSucesso(r.mensagem);
                    },
                    error: function (err) {
                        exibirLoading('N');
                        console.error('Erro ao alterar o usuário:', err);
                    }
                });
            } else {
                requisicaoAjax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'NOME_USUARIO': nomeUsuario,
                        'SENHA_USUARIO': senhaUsuario,
                        'ID_PERFIL': perfilUsuario,
                        'ID_EQUIPE': equipeUsuario
                    },
                    url: "{{ route('usuario.inserir') }}",
                    success: function (r) {
                        limparCamposModal();
                        $('#modalUsuario').modal('hide');
                        exibirLoading('N');
                        buscarDados();
                        informarSucesso(r.mensagem);
                    },
                    error: function (err) {
                        exibirLoading('N');
                        console.error('Erro ao inserir usuário:', err);
                    }
                });
            }
        }

        function editarCadastro(idUsuario){
            if (!usuarioEhAdministrador) return;
            exibirLoading('S');

            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ID_USUARIO': idUsuario
                },
                url: "{{ route('usuarios.buscar') }}",
                success: function (r) {
                    const usuario = r.dados?.[0];

                    buscarEquipes(usuario.ID_EQUIPE);
                    buscarPerfis(usuario.ID_PERFIL);

                    $('#inputIdUsuario').val(usuario.ID_USUARIO);
                    $('#inputNome').val(usuario.NOME_USUARIO);
                    $('#inputSenha').val(usuario.SENHA);
                    $('#modalUsuario').modal('show');

                    exibirLoading('N');
                },
                error: function (err) {
                    exibirLoading('N');
                    console.error('Erro ao buscar dados:', err);
                }
            });
        }

        function excluirCadastro(idUsuario){
            if (!usuarioEhAdministrador) return;
            Swal.fire({
                title: 'Realmente deseja inativar o usuário?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, inativar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    exibirLoading('S');
                    requisicaoAjax({
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'ID_USUARIO': idUsuario,
                        },
                        url: "{{ route('usuario.inativar') }}",
                        success: function (r) {
                            exibirLoading('N');
                            buscarDados();
                            informarSucesso(r.mensagem);
                        },
                        error: function (err) {
                            exibirLoading('N');
                            console.error('Erro ao inativar usuário:', err);
                        }
                    });
                } else {
                    informarAviso('Nada foi feito!');
                }
            });
        }

        function buscarEquipes(idEquipe = null){
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                url: "{{ route('usuarios.equipes.buscar') }}",
                success: function (r) {
                    popularSelectEquipes(r.dados);

                    if (idEquipe !== null) {
                        $('#selectEquipes').val(idEquipe);
                    }
                },
                error: function (err) {
                    console.error('Erro ao buscar dados:', err);
                }
            });
        }

        function popularSelectEquipes(dados){
            var htmlDados = `<option value="0"> Selecione uma equipe... </option>`;

            for(let i = 0; i < dados.length; i++){
                htmlDados += `<option value="${dados[i]['ID_CODIGO']}"> ${dados[i]['DESCRICAO']} </option>`
            }

            $('#selectEquipes').html(htmlDados);
        }

        function buscarPerfis(idPerfil = null){
            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                },
                url: "{{ route('usuarios.perfis.buscar') }}",
                success: function (r) {
                    popularSelectPerfis(r.dados);

                    if (idPerfil !== null) {
                        $('#selectPerfis').val(idPerfil);
                    }
                },
                error: function (err) {
                    console.error('Erro ao buscar dados:', err);
                }
            });
        }

        function popularSelectPerfis(dados){
            var htmlDados = `<option value="0"> Selecione um perfil... </option>`;

            for(let i = 0; i < dados.length; i++){
                htmlDados += `<option value="${dados[i]['ID_CODIGO']}"> ${dados[i]['DESCRICAO']} </option>`
            }

            $('#selectPerfis').html(htmlDados);
        }
        
        function visualizarPermissoes(idUsuario){
            if (!usuarioEhAdministrador) return;
            $('#inputIdUsuarioPermissoes').val(idUsuario);
            $('#inputFiltroPermissoes').val('');
            $('#tabelaPermissoes tbody').html('<tr><td colspan="3" class="text-center">Carregando permissões...</td></tr>');
            $('#modalPermissoes').modal('show');

            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ID_USUARIO': idUsuario
                },
                url: "{{ route('usuarios.permissoes.buscar') }}",
                success: function (r) {
                    // Evita exibir o retorno de outro usuário se o modal foi reaberto.
                    if ($('#inputIdUsuarioPermissoes').val() != idUsuario) return;
                    popularTabelaPermissoes(r.dados || []);
                },
                error: function (err) {
                    if ($('#inputIdUsuarioPermissoes').val() != idUsuario) return;
                    $('#tabelaPermissoes tbody').html('<tr><td colspan="3" class="text-center">Erro ao carregar permissões.</td></tr>');
                    console.error('Erro ao buscar permissões:', err);
                }
            });
        }

        function popularTabelaPermissoes(dados){
            let linhas = '';

            if (dados.length === 0) {
                linhas = '<tr><td colspan="4" class="text-center">Nenhuma permissão encontrada.</td></tr>';
            } else {
                dados.forEach(function (permissao) {
                    const permitido = Number(permissao.USUARIO_POSSUI_PERMISSAO) === 1;
                    const idPermissao = Number(permissao.ID_CODIGO);
                    const nome = $('<div>').text(permissao.NOME ?? '').html();
                    const descricao = $('<div>').text(permissao.DESCRICAO ?? '').html();

                    linhas += `
                        <tr>
                            <td>${idPermissao}</td>
                            <td>${nome}</td>
                            <td>${descricao}</td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center mb-0">
                                    <input class="form-check-input switch-permissao m-0"
                                        type="checkbox"
                                        role="switch"
                                        id="permissao_${idPermissao}"
                                        data-id-permissao="${idPermissao}"
                                        aria-label="Permissão ${descricao}"
                                        ${permitido ? 'checked' : ''}>
                                </div>
                            </td>
                        </tr>`;
                });
            }

            $('#tabelaPermissoes tbody').html(linhas);
            $('#inputFiltroPermissoes').trigger('input');
        }
        
        $('#inputFiltroPermissoes').on('input', function () {
            const filtro = $(this).val().trim().toLocaleLowerCase('pt-BR');

            $('#tabelaPermissoes tbody tr').each(function () {
                const id = $(this).find('td:eq(0)').text().trim();
                const nome = $(this).find('td:eq(1)').text().toLocaleLowerCase('pt-BR');
                const descricao = $(this).find('td:eq(2)').text().toLocaleLowerCase('pt-BR');

                $(this).toggle(id.includes(filtro) || nome.includes(filtro) || descricao.includes(filtro));
            });
        });

        $('#tabelaPermissoes').on('change', '.switch-permissao', function () {
            if (!usuarioEhAdministrador) return;
            const botao = $(this);
            const idUsuario = $('#inputIdUsuarioPermissoes').val();
            const idPermissao = botao.data('id-permissao');
            const permitido = botao.is(':checked') ? 1 : 0;

            botao.prop('disabled', true);

            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ID_USUARIO': idUsuario,
                    'ID_PERMISSAO': idPermissao,
                    'PERMITIDO': permitido
                },
                url: "{{ route('usuarios.permissoes.alterar') }}",
                success: function (r) {
                    botao.prop('disabled', false);
                    informarSucesso(r.mensagem || 'Permissão atualizada com sucesso!');
                },
                error: function (err) {
                    botao.prop('checked', !permitido).prop('disabled', false);
                    console.error('Erro ao atualizar permissão:', err);
                    informarAviso('Não foi possível atualizar a permissão.');
                }
            });
        });

        // Clique: ASC -> DESC -> sem ordenação. Shift + clique soma colunas (ordenação múltipla)
        $('#tabelaUsuarios thead').on('click', 'th.ordenavel', function (evento) {
            const coluna = $(this).data('coluna');
            const itemAtual = ordenacao.find(item => item.startsWith(coluna + ' '));

            let novoItem = null;

            if (!itemAtual) {
                novoItem = coluna + ' ASC';
            } else if (itemAtual.endsWith(' ASC')) {
                novoItem = coluna + ' DESC';
            }

            if (evento.shiftKey) {
                ordenacao = ordenacao.filter(item => !item.startsWith(coluna + ' '));

                if (novoItem) {
                    ordenacao.push(novoItem);
                }
            } else {
                ordenacao = novoItem ? [novoItem] : [];
            }

            paginaAtual = 1;
            atualizarIndicadoresOrdenacao();
            getFuncaoPadrao();
        });

        // Teclado: Enter ou espaço também ordenam
        $('#tabelaUsuarios thead').on('keydown', 'th.ordenavel', function (evento) {
            if (evento.key === 'Enter' || evento.key === ' ') {
                evento.preventDefault();
                $(this).trigger('click');
            }
        });
        
        // Clique nos botões da paginação
        $('#paginacaoLista').on('click', 'button[data-pagina]', function () {
            const pagina = parseInt($(this).data('pagina'), 10);

            if (!pagina || pagina === paginaAtual) {
                return;
            }

            paginaAtual = pagina;
            getFuncaoPadrao();
        });

        // Registros por página (direita)
        $('#selectPorPagina').on('change', function () {
            registrosPorPagina = parseInt($(this).val(), 10);
            paginaAtual = 1;
            getFuncaoPadrao();
        });

        $('#btnNovoUsuario, #btnNovoUsuarioMobile').on('click', () => {
            buscarEquipes();
            buscarPerfis();
            limparCamposModal();
            $('#modalUsuario').modal('show');
        })

        $('#inputFiltroUsuario').on('keyup', () => {
            clearTimeout(temporizadorBusca);

            temporizadorBusca = setTimeout(() => {
                paginaAtual = 1;
                buscarDados();
            }, 1000);
        });

        $('#checkPesquisarInativos').on('change', () => {
            paginaAtual = 1;
            buscarDados();
        })

        $('#painelFiltrosUsuario').on('shown.bs.collapse hidden.bs.collapse', function () {
            $('#rotuloFiltrosUsuario').text($(this).hasClass('show') ? 'Ocultar filtros' : 'Mostrar filtros');
        });

        $(document).ready(function () {
            buscarDados();
        });
    </script>
@endpush