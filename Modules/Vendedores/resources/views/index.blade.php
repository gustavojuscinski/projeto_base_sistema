@extends('layouts.app')

@section('title', 'Brasa | Vendedores')
@section('page-title', 'Vendedores')

@section('content')
    <div>
        <div class="d-flex gap-2">
            <div class="input-group flex-grow-1">
                <input type="text" class="form-control" placeholder="Buscar vendedor, usuário ou filial" id="inputFiltroVendedor">
                <label class="input-group-text gap-2" for="checkPesquisarInativos">
                    <input type="checkbox" class="form-check-input m-0" id="checkPesquisarInativos">
                    <span class="text-nowrap">Inativos</span>
                </label>
            </div>
            <button type="button" class="btn btn-primary text-nowrap" id="btnNovoVendedor">
                <i class="fa-solid fa-plus me-1"></i>Novo
            </button>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelaVendedores" aria-label="Tabela de vendedores">
                    <thead>
                        <tr>
                            <th class="ordenavel" data-coluna="ID_VENDEDOR" tabindex="0" aria-sort="none">ID</th>
                            <th class="ordenavel" data-coluna="NOME_VENDEDOR" tabindex="0" aria-sort="none">Nome</th>
                            <th class="ordenavel" data-coluna="NOME_USUARIO" tabindex="0" aria-sort="none">Usuário</th>
                            <th class="ordenavel" data-coluna="DESCRICAO_FILIAL" tabindex="0" aria-sort="none">Filial</th>
                            <th class="ordenavel" data-coluna="QTDE_HORARIOS" tabindex="0" aria-sort="none">Horários</th>
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
                        <option value="10" selected>10</option><option value="25">25</option>
                        <option value="50">50</option><option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

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
@endsection

@push('scripts')
    <script>
        var ordenacao = [];
        var paginaAtual = 1;
        var registrosPorPagina = 10;
        let temporizadorBusca;
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
            let linhas='';
            if (!dados.length) {
                linhas='<tr><td colspan="6" class="text-center">Nenhum vendedor encontrado.</td></tr>';
            } else {
                dados.forEach(function(vendedor){
                    const id=Number(vendedor.ID_VENDEDOR);
                    linhas += `<tr>
                        <td>${id}</td>
                        <td>${textoSeguro(vendedor.NOME_VENDEDOR)}</td>
                        <td>${textoSeguro(vendedor.NOME_USUARIO)}</td>
                        <td>${textoSeguro(vendedor.DESCRICAO_FILIAL)}</td>
                        <td>${Number(vendedor.QTDE_HORARIOS)||0}</td>
                        <td><div class="acoes-linha">
                            <button type="button" class="btn btn-primary m-0 py-0 px-1" title="Editar" onclick="editarCadastro(${id})"><i class="fa-solid fa-edit"></i></button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-secondary m-0 py-0 px-1 dropdown-toggle" title="Mais ações" aria-label="Mais ações" aria-expanded="false" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}'><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><button type="button" class="dropdown-item" onclick="visualizarHorarios(${id})"><i class="fa-solid fa-clock me-1"></i>Horários</button></li>
                                    <li><button type="button" class="dropdown-item acao-perigo" onclick="excluirCadastro(${id})"><i class="fa-solid fa-trash me-1"></i>Excluir</button></li>
                                </ul>
                            </div>
                        </div></td>
                    </tr>`;
                });
            }
            $('#tabelaVendedores tbody').html(linhas);
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

        $('#btnNovoVendedor').on('click',function(){
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
