<?php
return [
    'database' => [
        'name' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
        'connection' => $_ENV['DB_CONNECTION'].':host='.$_ENV['DB_HOST'],
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ]
    ];