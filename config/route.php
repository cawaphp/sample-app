<?php
/* @var $this \Cawa\Router\Router */

use \Cawa\Core\App;
use \Cawa\Router\Route;

return [
    Route::create()->setName("directResponse")->setMatch("/fr/direct")->setController(function(array $args = array())
    {
        App::response()->setStatus(422);
        App::response()->addHeader("maman-sd78fsd5f4", "sdfsd");
        return "Super";
    }),
    Route::create()->setName("nolanguage")->setMatch("/")->setController("SampleApp\\Controller\\Index::redirect"),

    Route::create()->setName("index")->setMatch("/{{L}}")->setController("SampleApp\\Controller\\Index::method"),
    Route::create()->setName("indexTwig")->setMatch("/{{L}}/twig")->setController("SampleApp\\Controller\\Index::twig"),

    Route::create()->setResponseCode(404)->setController("SampleApp\\Controller\\Index::notFound")->setOption(Route::OPTIONS_CACHE, 60),
];
