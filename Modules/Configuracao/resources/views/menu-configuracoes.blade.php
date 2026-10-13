
@extends('layouts.app')

@section('title', 'Brasa | Configurações')
@section('page-title', 'Configurações')

@section('content')
    <div class="menu-pagina">

        {{-- Cadastros e permissões --}}
        <section class="menu-grupo card p-2">
            <div class="menu-grupo-cabecalho">
                <div class="menu-grupo-icone">
                    <i class="fa-solid fa-sliders"></i>
                </div>

                <div>
                    <h3>Cadastros e permissões</h3>
                    <p>Gerencie os acessos e a estrutura de usuários.</p>
                </div>
            </div>

            <div class="menu-grid">

                {{-- Usuários --}}
                <button type="button" class="menu-card" onclick="abrirMenu('{{ url('/usuarios') }}')">
                    <div class="menu-card-icone menu-icone-laranja">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div class="menu-card-conteudo">
                        <h4>Usuários</h4>
                        <p>Consulte, cadastre e gerencie os usuários do sistema.</p>
                    </div>

                    <div class="menu-card-seta">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </button>

                {{-- Equipes --}}
                <button type="button" class="menu-card" onclick="abrirMenu('{{ url('/equipes') }}')">
                    <div class="menu-card-icone menu-icone-azul">
                        <i class="fa-solid fa-people-group"></i>
                    </div>

                    <div class="menu-card-conteudo">
                        <h4>Equipes</h4>
                        <p>Organize as equipes e seus integrantes.</p>
                    </div>

                    <div class="menu-card-seta">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </button>

                {{-- Perfis --}}
                <button type="button" class="menu-card" onclick="abrirMenu('{{ url('/perfis') }}')">
                    <div class="menu-card-icone menu-icone-roxo">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <div class="menu-card-conteudo">
                        <h4>Perfis de acesso</h4>
                        <p>Configure os perfis e as permissões dos usuários.</p>
                    </div>

                    <div class="menu-card-seta">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </button>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function abrirMenu(rota) {
            if (typeof rota !== 'string' || rota.trim() === '') {
                informarErro('A rota informada para abrirMenu é inválida.');
            } else {
                window.location.href = rota;
            }
        };
    </script>
@endpush
