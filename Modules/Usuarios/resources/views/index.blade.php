@extends('layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Usuários')

@section('content')
    <div>
        <div>
            <div class="d-flex gap-2">
                <div class="input-group flex-grow-1">
                    <input type="text" class="form-control" placeholder="Buscar usuário" id="inputFiltroUsuario">

                    <label class="input-group-text gap-2" for="checkPesquisarInativos">
                        <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                        <span class="text-nowrap">Inativos</span>
                    </label>
                </div>

                <button type="button" class="btn btn-primary text-nowrap" id="btnNovoUsuario">
                    <i class="fa-solid fa-plus me-1"></i>Novo
                </button>
            </div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelaUsuarios">
                    <thead>
                        <tr>
                            {{-- Colunas com a classe "ordenavel" são clicáveis. data-coluna = coluna do SELECT --}}
                            <th class="ordenavel" data-coluna="ID_USUARIO" tabindex="0" aria-sort="none" style="width: 5vw;">ID</th>
                            <th class="ordenavel" data-coluna="NOME_USUARIO" tabindex="0" aria-sort="none" style="width: 35vw;">Nome</th>
                            <th class="ordenavel" data-coluna="DESCRICAO_PERFIL" tabindex="0" aria-sort="none" style="width: 15vw;">Perfil</th>
                            <th class="ordenavel" data-coluna="DESCRICAO_EQUIPE" tabindex="0" aria-sort="none" style="width: 15vw;">Equipe</th>
                            <th class="ordenavel" data-coluna="DT_HORA_CADASTRO" tabindex="0" aria-sort="none" style="width: 15vw;">Data Cadastro</th>
                            <th style="width: 10vw;">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
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
@endsection

@push('scripts')
    <script>
        var ordenacao = [];
        var paginaAtual = 1;
        var registrosPorPagina = 10;

        let temporizadorBusca;

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

            if (!dados || dados.length === 0) {
                linhas = `
                    <tr>
                        <td colspan="6" class="text-center">
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                `;
            } else {
                dados.forEach(function (usuario) {
                    var htmlBotoes = `
                                <div class="acoes-linha">
                                    <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar"
                                            onclick="editarCadastro(${usuario.ID_USUARIO})">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>

                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle"
                                                title="Mais ações" aria-label="Mais ações" aria-expanded="false"
                                                data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'>
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button type="button" class="dropdown-item acao-perigo"
                                                        onclick="excluirCadastro(${usuario.ID_USUARIO})">
                                                    <i class="fa-solid fa-trash"></i>
                                                    <span>Excluir</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>`;
                    linhas += `
                        <tr>
                            <td>${usuario.ID_USUARIO}</td>
                            <td>${usuario.NOME_USUARIO ?? ''}</td>
                            <td>${usuario.DESCRICAO_PERFIL}</td>
                            <td>${usuario.DESCRICAO_EQUIPE}</td>
                            <td>${aplicarMascaraData(usuario.DT_HORA_CADASTRO)}</td>
                            <td>
                                ${htmlBotoes}
                            </td>
                        </tr>
                    `;
                });
            }

            $('#tabelaUsuarios tbody').html(linhas);
        }

        function limparCamposModal(){
            $('#inputIdUsuario').val('');
            $('#inputNome').val('');
            $('#inputSenha').val('');
            $('#selectPerfis').val('0');
            $('#selectEquipes').val('0');
        }

        function salvarCadastro(){
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

        $('#btnNovoUsuario').on('click', () => {
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

        $(document).ready(function () {
            buscarDados();
        });
    </script>
@endpush