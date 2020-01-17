<?php

return new \Phalcon\Config([
    'dev' => [
        'database' => [
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => 'root',
            'dbname'      => 'dbdev',
            'charset'     => 'utf8',
        ],
        'social' => [
            'googlePlus' => [
                'clientId' => '',
                'clientSecret' => '',
                'devKey' => ''
            ]
        ],
        'mep' => [
            'ssh' => 'login@IP:path',
            'key' => '', // path to the key or leave empty
            'excludes' => null // array or set null
        ]
    ],
    'preprod' => [
        'database' => [
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => 'root',
            'dbname'      => 'dbpreprod',
            'charset'     => 'utf8',
        ],
        'mep' => [
            'ssh' => 'login@IP:path',
            'key' => '', // path to the key or leave empty
            'excludes' => null // array or set null
        ]
    ],
    'prod' => [
        'database' => [
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => 'root',
            'dbname'      => 'dbprod',
            'charset'     => 'utf8',
        ],
        'mep' => [
            'ssh' => 'login@IP:path',
            'key' => '', // path to the key or leave empty
            'excludes' => null // array or set null
        ]
    ], 
    'version'=>1,
    'session_lifetime'=>172800,
    'libraries' => ['api', 'scrud'],   
    'application' => [
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir'      => __DIR__ . '/../models/',
        'dumpDir'        => __DIR__ . '/../dump/',
        'viewsDir'       => __DIR__ . '/../views/',
        'classesDir'     => __DIR__ . '/../classes/',
        'pluginsDir'     => __DIR__ . '/../plugins/',
        'messagesDir'    => __DIR__ . '/../messages/',
        'migrationsDir'  => __DIR__ . '/../migrations/',
        'tasksDir'       => __DIR__ . '/../tasks/',
        'publicDir'      => __DIR__ . '/../../../public/[app]/',
        'rootDir'        => __DIR__ . '/../../../',
        'libDir'         => [__DIR__ . '/../libraries/'],
        'baseUri'        => '/',
    ]
]);
