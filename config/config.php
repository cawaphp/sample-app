<?php
/* @var $this \Cawa\Core\Config */

use Cawa\Core\App;
use Cawa\Http\Client;
use Cawa\Log\Output\SyslogUdp;
use Psr\Log\LogLevel;

$udp = new SyslogUdp(SyslogUdp::FACILITY_LOG_LOCAL0, "Сáша");
$udp->setMinimumLevel(LogLevel::EMERGENCY);

$config = [
    "ip" => [
        "admin" => [
            "127.0.0.1",
        ],
        "remoteAddressHeaders" => ["REMOTE_ADDR"],
    ],
    "email" => [
        "default" => "smtp://localhost:25"
    ],
    "logger" => [
        $udp,
        // new \Cawa\Log\Output\Dump(),
    ],
    "locale" => [
        "available" => [
            "fr" => "fr_FR.utf8",
            "en" => "en_US.utf8",
            "de" => "de_DE.utf8",
            "es" => "es_ES.utf8"
        ],
        "default" => "fr",
    ],
    "assets" => "//www.cawa.dev/static/",
    "cache" => [
        "OUTPUT" => ["type" => "Apc", "prefix"=> "output"],
        "CLOCKWORK" => ["type" => "Apc", "prefix"=> "clockwork"],
        "REDIS" => ["type" => "Redis", "prefix"=> "redis", "config" => "redis://localhost:6379"]
    ],
    "clockwork" => ["type" => "Cache", "config" => ["CLOCKWORK"]]
];


if (App::env() == App::PROD) {
    /*
    |--------------------------------------------------------------------------
    | Production Configuration
    |--------------------------------------------------------------------------
    */
    $config = array_merge($config, [
        "db" => [
            "MAIN" => "mysql://root:pass@prod:3306/database",
            "SLAVE" => "mysql://root:pass@prod:3307/database",
        ],
        "httpclient" => [
            "GOOGLE" => "http://www.google.com",
            "GOOGLE_FN" => function()
            {
                $client = new Client();
                $client->setBaseUri("http://www.google.com");
                $client->getClient()
                    ->setOption(Client\AbstractClient::OPTIONS_DEBUG, true)
                    ->setOption(Client\AbstractClient::OPTIONS_SSL_VERIFY, false);

                return $client;
            },
        ]
    ]);
} else {
    /*
    |--------------------------------------------------------------------------
    | Development
    |--------------------------------------------------------------------------
    */
    $config = array_merge($config, [
        "db" => [
            "MAIN" => "mysql://root:@127.0.0.1:3306/test",
            "SLAVE" => "mysql://root:@127.0.0.1:3307/test",
        ],
        "httpclient" => [
            "GOOGLE" => "http://www.google.fr",
            "GOOGLE_FN" => function()
            {
                $client = new Client();
                $client->setBaseUri("http://www.google.fr");
                $client->getClient()
                    ->setOption(Client\AbstractClient::OPTIONS_DEBUG, true)
                    ->setOption(Client\AbstractClient::OPTIONS_SSL_VERIFY, false);

                return $client;
            },

        ]
    ]);
}

return $config;
