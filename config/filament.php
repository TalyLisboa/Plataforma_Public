<?php

return [

    'broadcasting' => [
        // Configurações do Laravel Echo (caso você queira usar notificação em tempo real com websockets)
        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'assets_path' => null,

    'cache_path' => base_path('bootstrap/cache/filament'),

    'livewire_loading_delay' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Configurações de autenticação do Filament. Aqui, definimos o guard e o 
    | usuário padrão para o painel. Ajuste conforme o guard configurado no 
    | seu projeto Laravel para autenticação.
    |
    */
    'auth' => [
        'guard' => 'web', // Defina o guard padrão para autenticação do Filament
        'user' => App\Models\User::class, // Defina o modelo de usuário padrão
    ],

    /*
    |--------------------------------------------------------------------------
    | Panel Guard (Middlewares de Autenticação)
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir o middleware que será usado para proteger as rotas 
    | do painel do Filament. A opção 'auth:sanctum' é recomendada se você estiver 
    | usando autenticação baseada em API (JWT, por exemplo). 
    | Use 'auth' ou 'auth:web' para sessões normais.
    |
    */
    'panel_guard' => [
        'middleware' => ['web', 'auth'], // Autenticação padrão para rotas do Filament
    ],

    /*
    |--------------------------------------------------------------------------
    | User Avatar
    |--------------------------------------------------------------------------
    |
    | Define as configurações para o avatar do usuário no painel Filament. 
    | Pode ser uma URL estática, gravatar, ou o valor de um campo do modelo.
    |
    */
    'user_avatar' => [
        'field' => 'avatar_url', // Nome do campo no modelo de usuário (caso exista)
        'default' => '/path/to/default/avatar.png', // URL de um avatar padrão caso o usuário não tenha um
    ],

];
