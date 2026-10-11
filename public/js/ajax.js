
/**
 * Configurações globais para requisições AJAX.
 */

$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    beforeSend: function (xhr, configuracao) {
        const token = document.querySelector('meta[name="csrf-token"]');

        if (token) {
            xhr.setRequestHeader('X-CSRF-TOKEN', token.content);
        }
    }
});

/**
 * Executa uma requisição AJAX com tratamento padronizado.
 *
 * @param {Object} opcoes
 * @returns {jqXHR}
 */
window.requisicaoAjax = function (opcoes = {}) {
    const configuracao = {
        type: 'POST',
        dataType: 'json',
        ...opcoes
    };

    const sucessoOriginal = configuracao.success;
    const erroOriginal = configuracao.error;

    configuracao.success = function (resposta, status, xhr) {
        if (resposta?.mensagem) {
            if (typeof window.exibirToast === 'function') {
                window.exibirToast(
                    resposta.mensagem,
                    resposta.sucesso ? 'sucesso' : 'aviso'
                );
            } else {
                console.info(resposta.mensagem);
            }
        }

        if (typeof sucessoOriginal === 'function') {
            sucessoOriginal(resposta, status, xhr);
        }
    };

    configuracao.error = function (xhr, status, erro) {
        const mensagem =
            xhr.responseJSON?.mensagem ||
            'Não foi possível concluir a requisição.';

        if (typeof window.exibirToast === 'function') {
            window.exibirToast(mensagem, 'erro');
        } else {
            console.error(mensagem);
        }

        if (typeof erroOriginal === 'function') {
            erroOriginal(xhr, status, erro);
        }
    };

    return $.ajax(configuracao);
};

window.exibirLoading = function (status) {
    const loading = document.getElementById('loadingGlobal');

    if (!loading) {
        return;
    }

    if (status === 'S') {
        loading.classList.remove('d-none');
    } else if (status === 'N') {
        loading.classList.add('d-none');
    }
};