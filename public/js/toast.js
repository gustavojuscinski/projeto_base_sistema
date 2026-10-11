window.exibirToast = function (mensagem, tipo = 'sucesso', titulo = null) {
    const configuracoes = {
        sucesso: {
            titulo: titulo || 'Sucesso',
            icone: 'fa-circle-check',
            classe: 'toast-sucesso'
        },
        erro: {
            titulo: titulo || 'Erro',
            icone: 'fa-circle-xmark',
            classe: 'toast-erro'
        },
        aviso: {
            titulo: titulo || 'Atenção',
            icone: 'fa-triangle-exclamation',
            classe: 'toast-aviso'
        }
    };

    const config = configuracoes[tipo] || configuracoes.aviso;
    const container = document.getElementById('toastContainer');

    if (!container) {
        console.error('Container de Toast não encontrado.');
        return;
    }

    const toast = document.createElement('div');
    toast.className = `toast ${config.classe}`;
    toast.setAttribute('role', tipo === 'erro' ? 'alert' : 'status');

    const cabecalho = document.createElement('div');
    cabecalho.className = 'toast-header';

    const icone = document.createElement('i');
    icone.className = `fa-solid ${config.icone} me-2`;

    const tituloElemento = document.createElement('strong');
    tituloElemento.className = 'me-auto';
    tituloElemento.textContent = config.titulo;

    const fechar = document.createElement('button');
    fechar.type = 'button';
    fechar.className = 'btn-close';
    fechar.setAttribute('data-bs-dismiss', 'toast');
    fechar.setAttribute('aria-label', 'Fechar');

    cabecalho.append(icone, tituloElemento, fechar);

    const corpo = document.createElement('div');
    corpo.className = 'toast-body';
    corpo.textContent = mensagem;

    toast.append(cabecalho, corpo);
    container.appendChild(toast);

        const instancia = new bootstrap.Toast(toast, {
        delay: 1000,
        autohide: true
    });

    const remover = function () {
        if (!toast.isConnected) return;
        instancia.dispose();
        toast.remove();
    };

    toast.addEventListener('hidden.bs.toast', remover);

    instancia.show();

    setTimeout(function () {
        if (!toast.isConnected) return;
        instancia.hide();
        setTimeout(remover, 600);
    }, 5000);
};

window.informarSucesso = function (mensagem) {
    window.exibirToast(mensagem, 'sucesso');
};

window.informarErro = function (mensagem) {
    window.exibirToast(mensagem, 'erro');
};

window.informarAviso = function (mensagem) {
    window.exibirToast(mensagem, 'aviso');
};
