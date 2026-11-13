@extends('layouts.app')

@section('title', 'Brasa | Vendedores')
@section('page-title', 'Vendedores')

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
            <button type="button" class="btn btn-outline-secondary" id="btnAlternarFiltrosVendedor"
                    data-bs-toggle="collapse" data-bs-target="#painelFiltrosVendedor"
                    aria-expanded="false" aria-controls="painelFiltrosVendedor">
                <i class="fa-solid fa-filter me-1"></i><span id="rotuloFiltrosVendedor">Mostrar filtros</span>
            </button>
            @if ($administrador)
                <button type="button" class="btn btn-primary text-nowrap" id="btnNovoVendedorMobile">
                    <i class="fa-solid fa-plus me-1"></i>Novo
                </button>
            @endif
        </div>

        <div id="painelFiltrosVendedor" class="collapse d-lg-block">
            <div class="d-flex flex-column flex-lg-row align-items-stretch gap-2">
                <input type="text" class="form-control flex-grow-1" placeholder="Buscar vendedor, usuário ou filial" id="inputFiltroVendedor">
                <label class="d-flex align-items-center gap-2 px-2 py-2 border rounded" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Pesquisar inativos</span>
                </label>
                @if ($administrador)
                    <button type="button" class="btn btn-primary text-nowrap d-none d-lg-inline-block" id="btnNovoVendedor">
                        <i class="fa-solid fa-plus me-1"></i>Novo
                    </button>
                @endif
            </div>
        </div>

        <div class="card mt-2">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-striped" id="tabelaVendedores" aria-label="Tabela de vendedores">
                    <thead>
                        <tr>
                            <th class="ordenavel" data-coluna="ID_VENDEDOR" tabindex="0" aria-sort="none">ID</th>
                            <th class="ordenavel" data-coluna="NOME_VENDEDOR" tabindex="0" aria-sort="none">Nome</th>
                            <th class="ordenavel" data-coluna="NOME_USUARIO" tabindex="0" aria-sort="none">Usuário</th>
                            <th class="ordenavel" data-coluna="DESCRICAO_FILIAL" tabindex="0" aria-sort="none">Filial</th>
                            <th class="ordenavel" data-coluna="QTDE_HORARIOS" tabindex="0" aria-sort="none">Horários</th>
                            @if ($administrador)
                                <th>Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="cardsVendedoresMobile" class="d-lg-none p-2" aria-live="polite"></div>
            <div class="card-footer tabela-rodape">
                <div class="tabela-info" id="infoContagem" aria-live="polite"></div>
                <nav class="tabela-paginacao" aria-label="Paginação da lista">
                    <ul class="pagination pagination-sm mb-0" id="paginacaoLista"></ul>
                </nav>
                <div class="tabela-por-pagina">
                    <label for="selectPorPagina" class="mb-0">Por página</label>
                    <select id="selectPorPagina" class="form-select form-select-sm">
                        <option value="10" selected>10</option><option value="25">25</option>
                        <option value="50">50</option><option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($administrador)
    <div class="modal fade" id="modalVendedor" tabindex="-1" aria-labelledby="modalVendedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVendedorLabel">Novo vendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="inputIdVendedor">
                    <div class="mb-3">
                        <label class="form-label" for="inputNomeVendedor">Nome</label>
                        <input class="form-control" type="text" id="inputNomeVendedor" placeholder="Nome do vendedor">
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label" for="selectUsuarioVinculado">Usuário vinculado</label>
                            <select class="form-select" id="selectUsuarioVinculado"><option value="0">Sem usuário vinculado</option></select>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label" for="selectFilial">Filial</label>
                            <select class="form-select" id="selectFilial"><option value="0">Sem filial</option></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarCadastro()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHorariosVendedor" tabindex="-1" aria-labelledby="modalHorariosVendedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHorariosVendedorLabel">Horários do vendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="inputIdVendedorHorarios">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="selectDiaSemana">Dia da semana</label>
                            <select class="form-select" id="selectDiaSemana">
                                <option value="">Selecione...</option>
                                <option value="0">Domingo</option><option value="1">Segunda-feira</option>
                                <option value="2">Terça-feira</option><option value="3">Quarta-feira</option>
                                <option value="4">Quinta-feira</option><option value="5">Sexta-feira</option>
                                <option value="6">Sábado</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="inputHoraInicio">Início</label>
                            <input class="form-control" type="time" id="inputHoraInicio">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="inputHoraFim">Fim</label>
                            <input class="form-control" type="time" id="inputHoraFim">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="button" class="btn btn-primary w-100" id="btnInserirHorario"><i class="fa-solid fa-plus me-1"></i>Inserir</button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                        <table class="table table-striped align-middle" id="tabelaHorariosVendedor">
                            <thead><tr><th>ID</th><th>Dia</th><th>Início</th><th>Fim</th><th>Ações</th></tr></thead>
                            <tbody><tr><td colspan="5" class="text-center">Nenhum horário encontrado.</td></tr></tbody>
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
        const usuarioEhAdministradorVendedor = @json($administrador);
        const diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];

        function textoSeguro(valor) {
            return $('<div>').text(valor ?? '').html();
        }

        function getFuncaoPadrao(){ 
            return buscarDados(); 
        }

        function buscarDados(){
            exibirLoading('S');
            requisicaoAjax({
                type: 'POST', dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'FILTRO_BUSCA': $('#inputFiltroVendedor').val(),
                    'INATIVOS': $('#checkPesquisarInativos').is(':checked') ? 1 : 0,
                    'ORDENACAO_LISTA': ordenacao.join(','),
                    'PAGINA': paginaAtual,
                    'POR_PAGINA': registrosPorPagina
                },
                url: "{{ route('vendedores.buscar') }}",
                success: function(r){
                    const dados = r.dados || [];
                    if (dados.length === 0 && paginaAtual > 1){
                        paginaAtual--; buscarDados(); return;
                    }
                    const total = dados.length ? (parseInt(dados[0].TOTAL_REGISTROS, 10) || dados.length) : 0;
                    exibirLoading('N');
                    popularTabelaDados(dados);
                    renderizarPaginacao(total);
                    atualizarIndicadoresOrdenacao();
                },
                error: function(err){ exibirLoading('N'); console.error('Erro ao buscar vendedores:', err); }
            });
        }

        function renderizarPaginacao(total){
            const totalPaginas = Math.max(1, Math.ceil(total / registrosPorPagina));
            const inicio = total ? (paginaAtual - 1) * registrosPorPagina + 1 : 0;
            const fim = Math.min(paginaAtual * registrosPorPagina, total);
            $('#infoContagem').text(total ? `Exibindo ${inicio} a ${fim} de ${total} registros` : 'Nenhum registro');
            const botao = (rotulo, pagina, opcoes = {}) => `
                <li class="page-item ${opcoes.ativo ? 'active' : ''} ${opcoes.desabilitado ? 'disabled' : ''}">
                    <button type="button" class="page-link" data-pagina="${pagina}" ${opcoes.desabilitado ? 'disabled' : ''}
                        ${opcoes.ativo ? 'aria-current="page"' : ''}>${rotulo}</button></li>`;
            const paginas = [];
            for (let p=1; p<=totalPaginas; p++){
                if(p===1 || p===totalPaginas || Math.abs(p-paginaAtual)<=2) paginas.push(p);
            }
            let html = botao('<i class="fa-solid fa-angle-left"></i>', paginaAtual - 1, {desabilitado: paginaAtual<=1});
            let anterior=0;
            paginas.forEach(function(p){
                if(p-anterior>1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                html += botao(p,p,{ativo:p===paginaAtual}); anterior=p;
            });
            html += botao('<i class="fa-solid fa-angle-right"></i>', paginaAtual+1, {desabilitado:paginaAtual>=totalPaginas});
            $('#paginacaoLista').html(html);
        }

        function atualizarIndicadoresOrdenacao(){
            const cabecalhos=$('#tabelaVendedores th.ordenavel');
            cabecalhos.removeClass('ordenado-asc ordenado-desc').attr('aria-sort','none');
            ordenacao.forEach(function(item){
                const [coluna,direcao]=item.split(' ');
                cabecalhos.filter('[data-coluna="'+coluna+'"]')
                    .addClass(direcao==='ASC'?'ordenado-asc':'ordenado-desc')
                    .attr('aria-sort',direcao==='ASC'?'ascending':'descending');
            });
        }

        function popularTabelaDados(dados){
            let linhas = '';
            let cards = '';
            if (!dados.length) {
                linhas = `<tr><td colspan="${usuarioEhAdministradorVendedor ? 6 : 5}" class="text-center">Nenhum vendedor encontrado.</td></tr>`;
                cards = '<div class="text-center text-secondary py-4">Nenhum vendedor encontrado.</div>';
            } else {
                dados.forEach(function(vendedor){
                    const id = Number(vendedor.ID_VENDEDOR);
                    const nome = textoSeguro(vendedor.NOME_VENDEDOR);
                    const usuario = textoSeguro(vendedor.NOME_USUARIO) || '-';
                    const filial = textoSeguro(vendedor.DESCRICAO_FILIAL) || '-';
                    const horarios = Number(vendedor.QTDE_HORARIOS) || 0;
                    let htmlBotoes = '';
                    if (usuarioEhAdministradorVendedor) {
                        htmlBotoes = `<div class="acoes-linha">
                            <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar" onclick="editarCadastro(${id})"><i class="fa-solid fa-edit"></i></button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle" title="Mais ações" aria-label="Mais ações" aria-expanded="false" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button type="button" class="dropdown-item" onclick="visualizarHorarios(${id})"><i class="fa-solid fa-clock me-1"></i>Horários</button></li>
                                    <li><button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${id})"><i class="fa-solid fa-trash me-1"></i>Excluir</button></li>
                                </ul>
                            </div>
                        </div>`;
                    }
                    linhas += `<tr>
                        <td>${id}</td><td>${nome}</td><td>${usuario}</td>
                        <td>${filial}</td><td>${horarios}</td>
                        ${usuarioEhAdministradorVendedor ? `<td>${htmlBotoes}</td>` : ''}
                    </tr>`;
                    cards += `<div class="card mb-2 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 border-bottom pb-2 mb-2">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-break">${nome}</div>
                                    <small class="text-secondary">Vendedor #${id}</small>
                                </div>
                                ${htmlBotoes}
                            </div>
                            <div class="row g-2 small">
                                <div class="col-12"><div class="text-secondary">Usuário vinculado</div><div class="text-break">${usuario}</div></div>
                                <div class="col-8"><div class="text-secondary">Filial</div><div class="text-break">${filial}</div></div>
                                <div class="col-4"><div class="text-secondary">Horários</div><div>${horarios}</div></div>
                            </div>
                        </div>
                    </div>`;
                });
            }
            $('#tabelaVendedores tbody').html(linhas);
            $('#cardsVendedoresMobile').html(cards);
        }

        function limparCamposModal(){
            $('#inputIdVendedor').val('');
            $('#inputNomeVendedor').val('');
            $('#selectUsuarioVinculado').val('0');
            $('#selectFilial').val('0');
        }

        function buscarUsuarios(idUsuario=0){
            requisicaoAjax({type:'POST',dataType:'json',data:{'_token':'{{ csrf_token() }}'},
                url:"{{ route('usuarios.buscar') }}",
                success:function(r){
                    let html='<option value="0">Sem usuário vinculado</option>';
                    (r.dados||[]).forEach(function(usuario){html+=`<option value="${usuario.ID_USUARIO}">${textoSeguro(usuario.NOME_USUARIO)}</option>`;});
                    $('#selectUsuarioVinculado').html(html).val(String(idUsuario||0));
                },error:function(err){console.error('Erro ao buscar usuários:',err);}
            });
        }

        function buscarFiliais(idFilial=0){
            requisicaoAjax({type:'POST',dataType:'json',data:{'_token':'{{ csrf_token() }}'},
                url:"{{ route('filiais.buscar') }}",
                success:function(r){
                    let html='<option value="0">Sem filial</option>';
                    (r.dados||[]).forEach(function(filial){html+=`<option value="${filial.ID_CODIGO}">${textoSeguro(filial.DESCRICAO)}</option>`;});
                    $('#selectFilial').html(html).val(String(idFilial||0));
                },error:function(err){console.error('Erro ao buscar filiais:',err);}
            });
        }

        function salvarCadastro(){
            if (!usuarioEhAdministradorVendedor) return;
            const idVendedor=Number($('#inputIdVendedor').val())||0;
            const nome=$('#inputNomeVendedor').val().trim();
            if(!nome){ informarAviso('Informe o nome do vendedor.'); return; }
            exibirLoading('S');
            requisicaoAjax({
                type:'POST', dataType:'json',
                data:{'_token':'{{ csrf_token() }}','ID_VENDEDOR':idVendedor,
                    'NOME_VENDEDOR':nome,
                    'ID_USUARIO_VINCULADO':$('#selectUsuarioVinculado').val(),
                    'ID_FILIAL':$('#selectFilial').val()},
                url:idVendedor>0?"{{ route('vendedores.alterar') }}":"{{ route('vendedores.inserir') }}",
                success:function(r){
                    exibirLoading('N');
                    if(r.sucesso===false){informarAviso(r.mensagem);return;}
                    $('#modalVendedor').modal('hide'); limparCamposModal(); buscarDados(); informarSucesso(r.mensagem);
                },error:function(err){exibirLoading('N');console.error('Erro ao salvar vendedor:',err);}
            });
        }

        function editarCadastro(idVendedor){
            if (!usuarioEhAdministradorVendedor) return;
            exibirLoading('S');
            requisicaoAjax({type:'POST',dataType:'json',
                data:{'_token':'{{ csrf_token() }}','ID_VENDEDOR':idVendedor},
                url:"{{ route('vendedores.buscar') }}",
                success:function(r){
                    exibirLoading('N');
                    const vendedor=(r.dados||[])[0];
                    if(!vendedor){informarAviso('Vendedor não encontrado.');return;}
                    $('#modalVendedorLabel').text('Editar vendedor');
                    $('#inputIdVendedor').val(vendedor.ID_VENDEDOR);
                    $('#inputNomeVendedor').val(vendedor.NOME_VENDEDOR);
                    buscarUsuarios(vendedor.ID_USUARIO_VINCULADO);
                    buscarFiliais(vendedor.ID_FILIAL);
                    $('#modalVendedor').modal('show');
                },error:function(err){exibirLoading('N');console.error('Erro ao editar vendedor:',err);}
            });
        }

        function excluirCadastro(idVendedor){
            if (!usuarioEhAdministradorVendedor) return;
            Swal.fire({title:'Realmente deseja inativar o vendedor?',icon:'warning',showCancelButton:true,
                confirmButtonText:'Sim, inativar',cancelButtonText:'Cancelar',confirmButtonColor:'#d33',cancelButtonColor:'#6c757d'
            }).then(function(resultado){
                if(!resultado.isConfirmed)return;
                exibirLoading('S');
                requisicaoAjax({type:'POST',dataType:'json',
                    data:{'_token':'{{ csrf_token() }}','ID_VENDEDOR':idVendedor},
                    url:"{{ route('vendedores.inativar') }}",
                    success:function(r){exibirLoading('N');if(r.sucesso===false){informarAviso(r.mensagem);return;}buscarDados();informarSucesso(r.mensagem);},
                    error:function(err){exibirLoading('N');console.error('Erro ao inativar vendedor:',err);}
                });
            });
        }

        function visualizarHorarios(idVendedor){
            if (!usuarioEhAdministradorVendedor) return;
            const nomeVendedor = $('#tabelaVendedores tbody tr').filter(function(){
                return Number($(this).find('td:first').text()) === Number(idVendedor);
            }).find('td:eq(1)').text();
            $('#inputIdVendedorHorarios').val(idVendedor);
            $('#modalHorariosVendedorLabel').text('Horários - '+nomeVendedor);
            $('#selectDiaSemana').val('');$('#inputHoraInicio').val('');$('#inputHoraFim').val('');
            $('#modalHorariosVendedor').modal('show');
            buscarHorariosVendedor();
        }

        function buscarHorariosVendedor(){
            const idVendedor=$('#inputIdVendedorHorarios').val();
            $('#tabelaHorariosVendedor tbody').html('<tr><td colspan="5" class="text-center">Carregando horários...</td></tr>');
            requisicaoAjax({type:'POST',dataType:'json',
                data:{'_token':'{{ csrf_token() }}','ID_VENDEDOR':idVendedor},
                url:"{{ route('vendedores.horarios.buscar') }}",
                success:function(r){
                    if($('#inputIdVendedorHorarios').val()!=idVendedor)return;
                    let linhas='';
                    (r.dados||[]).forEach(function(horario){
                        const id=Number(horario.ID_CODIGO);
                        linhas+=`<tr><td>${id}</td><td>${diasSemana[Number(horario.DIA_SEMANA)]||''}</td>
                            <td>${textoSeguro(horario.HORA_INICIO)}</td><td>${textoSeguro(horario.HORA_FIM)}</td>
                            <td><button type="button" class="btn btn-outline-danger btn-sm" title="Excluir horário" onclick="excluirHorario(${id})"><i class="fa-solid fa-trash"></i></button></td></tr>`;
                    });
                    $('#tabelaHorariosVendedor tbody').html(linhas||'<tr><td colspan="5" class="text-center">Nenhum horário encontrado.</td></tr>');
                },error:function(err){console.error('Erro ao buscar horários:',err);$('#tabelaHorariosVendedor tbody').html('<tr><td colspan="5" class="text-center">Erro ao carregar horários.</td></tr>');}
            });
        }

        function inserirHorario(){
            if (!usuarioEhAdministradorVendedor) return;
            const idVendedor=$('#inputIdVendedorHorarios').val();
            const diaSemana=$('#selectDiaSemana').val();
            const horaInicio=$('#inputHoraInicio').val();
            const horaFim=$('#inputHoraFim').val();
            if(diaSemana===''||!horaInicio||!horaFim||horaInicio>=horaFim){informarAviso('Informe dia e horários válidos (início anterior ao fim).');return;}
            $('#btnInserirHorario').prop('disabled',true);
            requisicaoAjax({type:'POST',dataType:'json',
                data:{'_token':'{{ csrf_token() }}','ID_VENDEDOR':idVendedor,'DIA_SEMANA':diaSemana,'HORA_INICIO':horaInicio,'HORA_FIM':horaFim},
                url:"{{ route('vendedores.horarios.inserir') }}",
                success:function(r){
                    $('#btnInserirHorario').prop('disabled',false);
                    if(r.sucesso===false){informarAviso(r.mensagem);return;}
                    $('#selectDiaSemana').val('');$('#inputHoraInicio').val('');$('#inputHoraFim').val('');
                    buscarHorariosVendedor();buscarDados();informarSucesso(r.mensagem);
                },error:function(err){$('#btnInserirHorario').prop('disabled',false);console.error('Erro ao inserir horário:',err);}
            });
        }

        function excluirHorario(idHorario){
            if (!usuarioEhAdministradorVendedor) return;
            Swal.fire({title:'Deseja excluir este horário?',icon:'warning',showCancelButton:true,
                confirmButtonText:'Sim, excluir',cancelButtonText:'Cancelar'
            }).then(function(resultado){
                if(!resultado.isConfirmed)return;
                requisicaoAjax({type:'POST',dataType:'json',
                    data:{'_token':'{{ csrf_token() }}','ID_CODIGO':idHorario,'ID_VENDEDOR':$('#inputIdVendedorHorarios').val()},
                    url:"{{ route('vendedores.horarios.inativar') }}",
                    success:function(r){if(r.sucesso===false){informarAviso(r.mensagem);return;}buscarHorariosVendedor();buscarDados();informarSucesso(r.mensagem);},
                    error:function(err){console.error('Erro ao excluir horário:',err);}
                });
            });
        }

        $('#btnInserirHorario').on('click',inserirHorario);

        $('#btnNovoVendedor, #btnNovoVendedorMobile').on('click',function(){
            limparCamposModal();$('#modalVendedorLabel').text('Novo vendedor');
            buscarUsuarios();buscarFiliais();$('#modalVendedor').modal('show');
        });

        $('#tabelaVendedores thead').on('click','th.ordenavel',function(evento){
            const coluna=$(this).data('coluna');
            const itemAtual=ordenacao.find(item=>item.startsWith(coluna+' '));
            let novoItem=null;
            if(!itemAtual)novoItem=coluna+' ASC';else if(itemAtual.endsWith(' ASC'))novoItem=coluna+' DESC';
            if(evento.shiftKey){ordenacao=ordenacao.filter(item=>!item.startsWith(coluna+' '));if(novoItem)ordenacao.push(novoItem);}
            else ordenacao=novoItem?[novoItem]:[];
            paginaAtual=1;atualizarIndicadoresOrdenacao();buscarDados();
        });

        $('#tabelaVendedores thead').on('keydown','th.ordenavel',function(evento){
            if(evento.key==='Enter'||evento.key===' '){evento.preventDefault();$(this).trigger('click');}
        });

        $('#paginacaoLista').on('click','button[data-pagina]',function(){
            const pagina=Number($(this).data('pagina'));
            if(!pagina||pagina===paginaAtual)return;
            paginaAtual=pagina;buscarDados();
        });

        $('#selectPorPagina').on('change',function(){
            registrosPorPagina=Number($(this).val());
            paginaAtual=1;
            buscarDados();
        });

        $('#painelFiltrosVendedor').on('shown.bs.collapse hidden.bs.collapse', function () {
            $('#rotuloFiltrosVendedor').text($(this).hasClass('show') ? 'Ocultar filtros' : 'Mostrar filtros');
        });

        $('#inputFiltroVendedor').on('input',function(){
            clearTimeout(temporizadorBusca);
            temporizadorBusca=setTimeout(function(){paginaAtual=1;buscarDados();},600);
        });

        $('#checkPesquisarInativos').on('change',function(){
            paginaAtual=1;
            buscarDados();
        });

        $(document).ready(function(){
            buscarDados();
        });
    </script>
@endpush
