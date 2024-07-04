<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'driver' => [
        'mysql',
        'mariadb',
        'pgsql'
    ],
    'key' => env('APP_KEY', null)
];
