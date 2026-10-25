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
            ],
            [
                'label' => 'Configurações',
                'icon' => 'fa-solid fa-gear',
                'submenu' => [
                    [
                        'label' => 'Usuários',
                        'icon' => 'fa-solid fa-user',
                        'route' => 'usuarios.index',
                    ],  
                    [
                        'label' => 'Perfis',
                        'icon' => 'fa-solid fa-user-shield',
                        'route' => 'perfis.index',
                    ],
                    [
                        'label' => 'Equipes',
                        'icon' => 'fa-solid fa-user-group',
                        'route' => 'equipes.index',
                    ],
                ],
            ],
            [
                'label' => 'Financeiro',
                'icon' => 'fa-solid fa-money-bill',
                'submenu' => [
                    [
                        'label' => 'Forma de Pagamento',
                        'icon' => 'fa-solid fa-credit-card',
                        'route' => 'forma-pagamentos.index',
                    ],
                ],
            ],

        ],

    ],

];