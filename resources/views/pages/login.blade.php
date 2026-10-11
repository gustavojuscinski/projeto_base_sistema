<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('adminlte.title', config('app.name')) }} | Login</title>

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <script>
        document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('tema') || 'light');
    </script>
</head>

<body class="tela-login">
    <div class="login-box">
        <div class="card login-card">
            <div class="login-logo">
                <img class="logo-tema-claro" src="{{ asset('img/logo-brasa-sem-fundo.png') }}" alt="Brasa">
                <img class="logo-tema-escuro" src="{{ asset('img/logo-brasa-branca.png') }}" alt="" aria-hidden="true">
            </div>

            <h1 class="login-titulo">LOGIN</h1>
            <p class="login-subtitulo">Acesse sua conta</p>

            <form id="formLogin" novalidate>
                @csrf
                <div class="campo">
                    <input type="text" name="inputUsuario" id="inputUsuario" class="form-control" placeholder="Usuário" autocomplete="username" required autofocus>
                    <i class="bi bi-person icone"></i>
                </div>
                <div class="campo">
                    <input type="password" name="senhaUsuario" id="senhaUsuario" class="form-control" placeholder="Senha" autocomplete="current-password" required>
                    <i class="bi bi-lock icone"></i>
                </div>
                <button type="submit" id="btnEntrar" class="btn-entrar">
                    <i class="bi bi-box-arrow-in-right me-1"></i>
                    ENTRAR
                </button>
            </form>

            <div class="tema-seletor">
                <span>Mudar Tema</span>
                <button type="button" class="btn-tema" onclick="alternarTema()" title="Alternar tema" aria-label="Alternar tema">
                    <i class="fa-solid fa-moon icone-tema-claro"></i>
                    <i class="fa-solid fa-sun icone-tema-escuro"></i>
                </button>
            </div>

        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" aria-live="polite" aria-atomic="true">
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/js/adminlte.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}"></script>

    <script>
        const htmlBotao = '<i class="bi bi-box-arrow-in-right me-1"></i> ENTRAR';

        function limparErro(campo) {
            $(campo).removeClass('is-invalid');
        }

        $('#inputUsuario, #senhaUsuario').on('input', function () {
            limparErro(this);
        });

        $('#formLogin').on('submit', function (event) {
            event.preventDefault();

            const usuario = $('#inputUsuario');
            const senha = $('#senhaUsuario');
            const botao = $('#btnEntrar');

            usuario.removeClass('is-invalid');
            senha.removeClass('is-invalid');

            if (!usuario.val().trim() || !senha.val()) {
                if (!usuario.val().trim()) {
                    usuario.addClass('is-invalid');
                }

                if (!senha.val()) {
                    senha.addClass('is-invalid');
                }

                window.exibirToast(
                    'Informe o usuário e a senha.',
                    'erro'
                );

                return;
            }

            botao.prop('disabled', true).html('ENTRANDO...');

            $.ajax({
                url: '{{ route('login.autenticar') }}',
                method: 'POST',
                data: $(this).serialize(),                
                success: function (resposta) {
                    if (resposta.sucesso) {
                        const dados = resposta.dados;

                        if (dados?.redirect) {
                            if (dados.bemVindo) {
                                sessionStorage.setItem('bemVindo', dados.bemVindo);
                            }

                            window.location.href = dados.redirect;
                            return;
                        }
                    }

                    window.exibirToast(resposta.mensagem || 'Não foi possível realizar o login.', 'erro');
                    botao.prop('disabled', false).html(htmlBotao);
                },
                error: function (xhr) {
                    const mensagem = xhr.responseJSON?.mensagem || 'Não foi possível realizar o login. Tente novamente.';

                    if (xhr.status === 401) {
                        usuario.addClass('is-invalid');
                        senha.addClass('is-invalid');
                    }

                    window.exibirToast(mensagem, 'erro');
                    botao.prop('disabled', false).html(htmlBotao);
                }
            });
        });

        function alternarTema() {
            const novo = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', novo);
            localStorage.setItem('tema', novo);
        }
    </script>

</body>
</html>