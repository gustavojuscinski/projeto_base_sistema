@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>Laravel</h3>
                    <p>Framework da aplicação</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-code-slash"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>AdminLTE 4</h3>
                    <p>Interface administrativa</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-window"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h3>jQuery AJAX</h3>
                    <p>Requisições assíncronas</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Teste de AJAX</h3>
        </div>

        <div class="card-body">

            <p>
                Clique no botão para enviar uma requisição ao Laravel
                sem atualizar a página.
            </p>

            <button type="button" id="btnTestarAjax" class="btn btn-primary">
                <i class="bi bi-send"></i>
                Testar requisição
            </button>

            <div id="resultadoAjax" class="mt-3" aria-live="polite"></div>

        </div>
    </div>

@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        $('#btnTestarAjax').off('click').on('click', function () {
            const botao = $(this);

            botao.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...'
            );

            window.requisicaoAjax({
                url: '{{ route('ajax.testar') }}',

                success: function (resposta) {
                    if (!resposta.sucesso) {
                        return;
                    }

                    const dados = resposta.dados?.[0];

                    if (!dados) {
                        $('#resultadoAjax').text(
                            'A consulta não retornou dados.'
                        );
                        return;
                    }

                    const resultado = $('<div>').addClass(
                        'alert alert-success mt-3'
                    );

                    resultado.append(
                        $('<strong>').text('Conexão realizada com sucesso!')
                    );

                    resultado.append($('<br>'));
                    resultado.append(
                        document.createTextNode('Banco: ' + dados.banco)
                    );

                    resultado.append($('<br>'));
                    resultado.append(
                        document.createTextNode('Data e hora: ' + dados.data_hora)
                    );

                    $('#resultadoAjax').empty().append(resultado);
                },

                error: function (xhr) {
                    $('#resultadoAjax').text(
                        'Falha na requisição. Código HTTP: ' + xhr.status
                    );
                },

                complete: function () {
                    botao.prop('disabled', false).html(
                        '<i class="bi bi-send"></i> Testar requisição'
                    );
                }
            });
        });
    });
</script>
@endpush