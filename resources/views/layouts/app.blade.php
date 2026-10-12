<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('img/logo-brasa-sem-fundo.png') }}">
    <title>@yield('title', config('adminlte.title', config('app.name')))</title>

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    {{-- Estilo global: sempre depois do AdminLTE. O ?v= evita cache antigo --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

    <script>
        document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('tema') || 'light');
    </script>
    @stack('styles')
</head>

{{-- Removido bg-body-tertiary: essa classe usa !important e sobrescreve o fundo do tema --}}
<body class="layout-fixed sidebar-expand-lg sidebar-mini {{ config('adminlte.sidebar.collapsed', true) ? 'sidebar-collapse' : '' }}">
    <div class="app-wrapper">

        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand m-0 p-0">
            <div class="container-fluid">

                <h3 class="navbar-titulo">
                    @yield('page-title', config('adminlte.title', config('app.name', 'Sistema')))
                </h3>

                <ul class="navbar-nav navbar-lado navbar-lado-direito">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ usuarioAutenticado()['NOME_USUARIO'] ?? 'Usuário' }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button type="button" class="dropdown-item" onclick="alternarTema()">
                                    <i class="fa-solid fa-moon icone-tema-claro"></i>
                                    <i class="fa-solid fa-sun icone-tema-escuro"></i>
                                    <span>Mudar Tema</span>
                                </button>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>Sair</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar (removido bg-body-secondary: o grafite vem do style.css) --}}
        <aside class="app-sidebar shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('home') }}" class="" title="Ir para a Home" aria-label="Brasa - Ir para a Home">
                    <img src="{{ asset('img/logo-brasa-branca.png') }}" alt="Brasa" width="30">
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        @foreach (config('adminlte.sidebar.menus', []) as $menu)
                            @php
                                $gate = $menu['can'] ?? null;

                                $autorizado = !$gate || \Illuminate\Support\Facades\Gate::has($gate) && \Illuminate\Support\Facades\Gate::allows($gate);
                            @endphp

                            @if ($autorizado)
                                <li class="nav-item">
                                    <a href="{{ route($menu['route']) }}" title="{{ $menu['label'] }}" class="nav-link {{ request()->routeIs($menu['route']) ? 'active' : '' }}">

                                        <i class="nav-icon {{ $menu['icon'] ?? 'fa-regular fa-circle' }}"></i>
                                        <p>{{ $menu['label'] }}</p>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Conteúdo --}}
        <main class="app-main">
            {{-- Faixa branca: breadcrumb à esquerda, ações da página à direita --}}
            <div class="app-content-header  m-0 p-0">
                <div class="container-fluid d-flex align-items-center justify-content-between gap-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            @yield('page-title', 'Início')
                        </li>
                    </ol>
                    <div>
                        @yield('page-actions')
                    </div>
                </div>
            </div>

            <div class="app-content m-0 p-0 pt-2">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

        </main>

        {{-- Rodapé --}}
        <footer class="app-footer d-none">
            <strong>{{ config('adminlte.title', config('app.name', 'Sistema')) }}</strong>
            <span class="float-end">
                Laravel + AdminLTE {{ config('adminlte.version', '4') }}
            </span>
        </footer>

    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" aria-live="polite" aria-atomic="true">
    </div>

    <div id="loadingGlobal" class="loading-global d-none">
        <div class="loading-conteudo">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3 mb-0">Aguarde, processando...</p>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/js/adminlte.js') }}"></script>
    <script src="{{ asset('js/funcoes.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/ajax.js') }}"></script>

     <script>
        const temaAtual = () => document.documentElement.getAttribute('data-bs-theme') || 'light';
        const swalOriginal = Swal.fire.bind(Swal);

        Swal.fire = function (...args) {
            const tema = temaAtual();

            return swalOriginal({
                background: tema === 'dark' ? '#343a40' : '#fff',
                color: tema === 'dark' ? '#f8f9fa' : '#545454',
                ...(typeof args[0] === 'object' ? args[0] : {}),
            });
        };

        const nomeUsuario = sessionStorage.getItem('bemVindo');

        if (nomeUsuario) {
            window.exibirToast(`Bem-vindo, ${nomeUsuario}!`, 'sucesso');
            sessionStorage.removeItem('bemVindo');
        }
    </script>  

    @stack('scripts')
    <script>
        function alternarTema() {
            const novo = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', novo);
            localStorage.setItem('tema', novo);
            informarSucesso('Tema alterado!')
        }
    </script>
</body>
</html>