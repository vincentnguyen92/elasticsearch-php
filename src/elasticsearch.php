<?php

return [
    'config' => [
        'enabled' => env('ELASTICSEARCH_ENABLED', false),
        'hosts' => explode(',', env('ELASTICSEARCH_HOSTS')),
        'username' => env('ELASTICSEARCH_USERNAME', 'username'),
        'password' => env('ELASTICSEARCH_PASSWORD', 'password'),
        'client' => [
            'timeout' => 4, #Refer: https://github.com/elastic/elasticsearch-php/issues/259#issuecomment-164460926
            'connect_timeout' => 10
        ]
    ],
];
