@extends('layouts.app')

@section('title', 'Brasa | Equipes')
@section('page-title', 'Equipes')

@section('content')
    <div>
        <div>
            <div class="d-flex gap-2">
                <div class="input-group flex-grow-1">
                    <input type="text" class="form-control" placeholder="Buscar equipe" id="inputFiltroEquipe">

                    <label class="input-group-text gap-2" for="checkPesquisarInativos">
                        <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                        <span class="text-nowrap">Inativas</span>
                    </label>
                </div>

                <button type="button" class="btn btn-primary text-nowrap" id="btnNovoEquipe">
                    <i class="fa-solid fa-plus me-1"></i>Novo
                </button>
            </div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelaEquipes" aria-label="Tabela de equipes">
                    <thead>
                        <tr>
                            {{-- Colunas com a classe "ordenavel" são clicáveis. data-coluna = coluna do SELECT --}}
                            <th class="ordenavel" data-coluna="ID_CODIGO" tabindex="0" aria-sort="none" style="width: 5vw;">ID</th>
                            <th class="ordenavel" data-coluna="DESCRICAO" tabindex="0" aria-sort="none" style="width: 35vw;">Descrição</th>
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

            if (!dados || dados.length === 0) {
                linhas = `
                    <tr>
                        <td colspan="6" class="text-center">
                            Nenhuma equipe encontrada.
                        </td>
                    </tr>
                `;
            } else {
                dados.forEach(function (equipe) {
                    var htmlBotoes = `
                                <div class="acoes-linha">
                                    <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar"
                                            onclick="editarCadastro(${equipe.ID_CODIGO})">
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
                                                        onclick="excluirCadastro(${equipe.ID_CODIGO})">
                                                    <i class="fa-solid fa-trash"></i>
                                                    <span>Excluir</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>`;
                    linhas += `
                        <tr>
                            <td>${equipe.ID_CODIGO}</td>
                            <td>${equipe.DESCRICAO ?? ''}</td>
                            <td>${aplicarMascaraData(equipe.DT_HORA_CADASTRO)}</td>
                            <td>
                                ${htmlBotoes}
                            </td>
                        </tr>
                    `;
                });
            }

            $('#tabelaEquipes tbody').html(linhas);
        }

        function limparCamposModal(){
            $('#inputIdEquipe').val('');
            $('#inputDescricao').val('');
        }

        function salvarCadastro(){
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

        $('#btnNovoEquipe').on('click', () => {
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

        $(document).ready(function () {
            buscarDados();
        });
    </script>
@endpush