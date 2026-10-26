<?php

return [

    'title' => env('APP_NAME', 'Sistema'),

    'version' => '1.0.0',

    'sidebar' => [

        'collapsed' => true,

        'menus' => [
            [
                'label' => 'Home',
                'icon' => 'fa-solid fa-house',
                'route' => 'home',
            ],
            [
                'label' => 'Rendas Uber',
                'icon' => 'fa-solid fa-car',
                'route' => 'rendauber.index',
                'can' => 'WEB_MENU_RENDA_UBER',
            ],
            [
                'label' => 'Configurações',
                'icon' => 'fa-solid fa-gear',
                'can' => 'WEB_MENU_CADASTROS',
                'submenu' => [
                    [
                        'label' => 'Usuários',
                        'icon' => 'fa-solid fa-user',
                        'route' => 'usuarios.index',
                        'can' => 'WEB_MENU_CADASTRO_USUARIO',
                    ],
                    [
                        'label' => 'Perfis',
                        'icon' => 'fa-solid fa-user-shield',
                        'route' => 'perfis.index',
                        'can' => 'WEB_MENU_CADASTRO_PERFIL',
                    ],
                    [
                        'label' => 'Equipes',
                        'icon' => 'fa-solid fa-user-group',
                        'route' => 'equipes.index',
                        'can' => 'WEB_MENU_CADASTRO_EQUIPE',
                    ],
                    [
                        'label' => 'Vendedores',
                        'icon' => 'fa-solid fa-user-group',
                        'route' => 'vendedores.index',
                        'can' => 'WEB_MENU_CADASTRO_VENDEDOR',
                    ],
                ],
            ],
            [
                'label' => 'Financeiro',
                'icon' => 'fa-solid fa-money-bill',
                'can' => 'WEB_MENU_FINANCEIRO',
                'submenu' => [
                    [
                        'label' => 'Forma de Pagamento',
                        'icon' => 'fa-solid fa-credit-card',
                        'route' => 'forma-pagamentos.index',
                        'can' => 'WEB_MENU_FORMA_PAGAMENTO',
                    ],
                ],
            ],
        ],
    ],
];
