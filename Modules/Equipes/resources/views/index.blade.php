@extends('layouts.app')

@section('title', 'Brasa | Equipes')
@section('page-title', 'Equipes')

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
            <button type="button" class="btn btn-outline-secondary" id="btnAlternarFiltrosEquipe"
                    data-bs-toggle="collapse" data-bs-target="#painelFiltrosEquipe"
                    aria-expanded="false" aria-controls="painelFiltrosEquipe">
                <i class="fa-solid fa-filter me-1"></i><span id="rotuloFiltrosEquipe">Mostrar filtros</span>
            </button>
            @if ($administrador)
                <button type="button" class="btn btn-primary text-nowrap" id="btnNovoEquipeMobile">
                    <i class="fa-solid fa-plus me-1"></i>Novo
                </button>
            @endif
        </div>

        <div id="painelFiltrosEquipe" class="collapse d-lg-block">
            <div class="d-flex flex-column flex-lg-row align-items-stretch gap-2">
                <input type="text" class="form-control flex-grow-1" placeholder="Buscar equipe" id="inputFiltroEquipe">
                <label class="d-flex align-items-center gap-2 px-2 py-2 border rounded" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Pesquisar inativas</span>
                </label>
                @if ($administrador)
                    <button type="button" class="btn btn-primary text-nowrap d-none d-lg-inline-block" id="btnNovoEquipe">
                        <i class="fa-solid fa-plus me-1"></i>Novo
                    </button>
                @endif
            </div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-striped" id="tabelaEquipes" aria-label="Tabela de equipes">
                    <thead>
                        <tr>
                            {{-- Colunas com a classe "ordenavel" são clicáveis. data-coluna = coluna do SELECT --}}
                            <th class="ordenavel" data-coluna="ID_CODIGO" tabindex="0" aria-sort="none" style="width: 5vw;">ID</th>
                            <th class="ordenavel" data-coluna="DESCRICAO" tabindex="0" aria-sort="none" style="width: 35vw;">Descrição</th>
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

            <div id="cardsEquipesMobile" class="d-lg-none p-2" aria-live="polite"></div>

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
    <div class="modal fade" id="modalEquipe" tabindex="-1" aria-labelledby="modalEquipeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEquipeLabel">Nova equipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="inputIdEquipe">
                    <div class="mb-3">
                        <label for="inputDescricao" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="inputDescricao" name="descricao" placeholder="Digite a descrição da equipe" required>
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
    @endif
@endsection

@push('scripts')
    <script>
        var ordenacao = [];
        var paginaAtual = 1;
        var registrosPorPagina = 10;

        let temporizadorBusca;
        const usuarioEhAdministrador = @json($administrador);
        const textoSeguroEquipe = valor => $('<div>').text(valor ?? '').html();

        function getFuncaoPadrao() {
            return buscarDados();
        }

        function buscarDados() {
            exibirLoading('S');
            const filtroBusca = $('#inputFiltroEquipe').val();
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
                url: "{{ route('equipes.buscar') }}",
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
            const cabecalhos = $('#tabelaEquipes th.ordenavel');

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
                linhas = `<tr><td colspan="${usuarioEhAdministrador ? 4 : 3}" class="text-center">Nenhuma equipe encontrada.</td></tr>`;
                cards = '<div class="text-center text-secondary py-4">Nenhuma equipe encontrada.</div>';
            } else {
                dados.forEach(function (equipe) {
                    const id = Number(equipe.ID_CODIGO);
                    const descricao = textoSeguroEquipe(equipe.DESCRICAO);
                    const dataCadastro = textoSeguroEquipe(aplicarMascaraData(equipe.DT_HORA_CADASTRO));
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
                                    <li><button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${id})"><i class="fa-solid fa-trash"></i><span>Excluir</span></button></li>
                                </ul>
                            </div>
                        </div>`;
                    }

                    linhas += `<tr>
                        <td>${id}</td><td>${descricao}</td><td>${dataCadastro}</td>
                        ${usuarioEhAdministrador ? `<td>${htmlBotoes}</td>` : ''}
                    </tr>`;

                    cards += `<div class="card mb-2 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 border-bottom pb-2 mb-2">
                                <div class="min-w-0"><div class="fw-semibold text-break">${descricao}</div>
                                    <small class="text-secondary">Equipe #${id}</small></div>
                                ${htmlBotoes}
                            </div>
                            <div class="small">
                                <div class="text-secondary">Data cadastro</div>
                                <div>${dataCadastro || '-'}</div>
                            </div>
                        </div>
                    </div>`;
                });
            }

            $('#tabelaEquipes tbody').html(linhas);
            $('#cardsEquipesMobile').html(cards);
        }

        function limparCamposModal(){
            $('#inputIdEquipe').val('');
            $('#inputDescricao').val('');
        }

        function salvarCadastro(){
            if (!usuarioEhAdministrador) return;
            exibirLoading('S');
            var idEquipe = $('#inputIdEquipe').val();
            var descricaoEquipe = $('#inputDescricao').val();


            if(idEquipe > 0){
                requisicaoAjax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'ID_CODIGO': idEquipe,
                        'DESCRICAO': descricaoEquipe
                    },
                    url: "{{ route('equipe.alterar') }}",
                    success: function (r) {
                        limparCamposModal();
                        $('#modalEquipe').modal('hide');
                        exibirLoading('N');
                        buscarDados();
                        informarSucesso(r.mensagem);
                    },
                    error: function (err) {
                        exibirLoading('N');
                        console.error('Erro ao alterar a equipe:', err);
                    }
                });
            } else {
                requisicaoAjax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'ID_CODIGO': idEquipe,
                        'DESCRICAO': descricaoEquipe
                    },
                    url: "{{ route('equipe.inserir') }}",
                    success: function (r) {
                        limparCamposModal();
                        $('#modalEquipe').modal('hide');
                        exibirLoading('N');
                        buscarDados();
                        informarSucesso(r.mensagem);
                    },
                    error: function (err) {
                        exibirLoading('N');
                        console.error('Erro ao inserir equipe:', err);
                    }
                });
            }
        }

        function editarCadastro(idEquipe){
            if (!usuarioEhAdministrador) return;
            exibirLoading('S');

            requisicaoAjax({
                type: 'POST',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ID_CODIGO': idEquipe
                },
                url: "{{ route('equipes.buscar') }}",
                success: function (r) {
                    const equipe = r.dados?.[0];

                    $('#inputIdEquipe').val(equipe.ID_CODIGO);
                    $('#inputDescricao').val(equipe.DESCRICAO);
                    $('#modalEquipe').modal('show');

                    exibirLoading('N');
                },
                error: function (err) {
                    exibirLoading('N');
                    console.error('Erro ao buscar dados:', err);
                }
            });
        }

        function excluirCadastro(idEquipe){
            if (!usuarioEhAdministrador) return;
            Swal.fire({
                title: 'Realmente deseja inativar a equipe?',
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
                            'ID_CODIGO': idEquipe,
                        },
                        url: "{{ route('equipe.inativar') }}",
                        success: function (r) {
                            exibirLoading('N');
                            buscarDados();
                            informarSucesso(r.mensagem);
                        },
                        error: function (err) {
                            exibirLoading('N');
                            console.error('Erro ao inativar equipe:', err);
                        }
                    });
                } else {
                    informarAviso('Nada foi feito!');
                }
            });
        }

        // Clique: ASC -> DESC -> sem ordenação. Shift + clique soma colunas (ordenação múltipla)
        $('#tabelaEquipes thead').on('click', 'th.ordenavel', function (evento) {
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
        $('#tabelaEquipes thead').on('keydown', 'th.ordenavel', function (evento) {
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

        $('#btnNovoEquipe, #btnNovoEquipeMobile').on('click', () => {
            limparCamposModal();
            $('#modalEquipe').modal('show');
        })

        $('#inputFiltroEquipe').on('keyup', () => {
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

        $('#painelFiltrosEquipe').on('shown.bs.collapse hidden.bs.collapse', function () {
            $('#rotuloFiltrosEquipe').text($(this).hasClass('show') ? 'Ocultar filtros' : 'Mostrar filtros');
        });

        $(document).ready(function () {
            buscarDados();
        });
    </script>
@endpush