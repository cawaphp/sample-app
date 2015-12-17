<?php
declare (strict_types=1);

// ini set configuration
ini_set('display_errors', '1');
ini_set('default_socket_timeout', '30');
ini_set('report_memleaks', '1');
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);

// date
date_default_timezone_set('UTC');

// autoload
require '../vendor/autoload.php';

use Cawa\Core\App;

putenv('APP_ENV=' . App::DEV);

$app = App::create(realpath("../"));
$app->init();
$app->registerModule(new Cawa\Clockwork\Module());
$app->registerModule(new Cawa\SwaggerServer\Module([
        new \Cawa\SwaggerServer\ServiceNamespace('Example', 'Cawa\\SwaggerServer\\ExamplesService', [2, 3]),
    ],
    [
        'web' => [
            'password' => md5(strtolower('web') . 'pass'),
            'ip' => [
                '127.0.0.1/16',
            ],
            'services' => [
                'Example.*' => '.*'
            ]
        ]
    ]
));

$app->handle();
$app->end();
