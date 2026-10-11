<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configurações gerais do AdminLTE
    |--------------------------------------------------------------------------
    */

    'title' => env('APP_NAME', 'Sistema'),

    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */

    'sidebar' => [

        // Inicia a sidebar recolhida, exibindo os ícones.
        'collapsed' => true,

        /*
        |--------------------------------------------------------------------------
        | Menus
        |--------------------------------------------------------------------------
        |
        | label: texto exibido
        | icon: classe do Bootstrap Icons
        | route: nome da rota Laravel
        | can: Gate opcional para verificar permissão
        |
        */

        'menus' => [

            [
                'label' => 'Home',
                'icon' => 'fa-solid fa-house',
                'route' => 'home',
            ],
            [
                'label' => 'Usuários',
                'icon' => 'fa-solid fa-user',
                'route' => 'usuarios.index',
            ],

        ],

    ],

];