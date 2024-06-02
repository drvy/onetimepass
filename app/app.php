<?php

use Dotenv\Dotenv;
use Slim\Views\Twig;
use Slim\Factory\AppFactory;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;
use Slim\Psr7\Factory\UriFactory;

// Session
session_start();

// .env load
(Dotenv::createImmutable(ABSPATH))->safeLoad();


$container = new DI\Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);


// Settings
$container->set('settings', function () use ($container) {
    return [
        'app' => [
            'name' => $_ENV['APP_NAME']
        ]
    ];
});


// Twig
$container->set('view', function ($container) use ($app) {
    $twig = Twig::create(ABSPATH . '/app/Views', [
        'cache' => false
    ]);

    $twig->addExtension(new DebugExtension());

    $twig->addExtension(new TwigExtension(
        $app->getRouteCollector()->getRouteParser(),
        (new UriFactory())->createFromGlobals($_SERVER),
        '/'
    ));

    return $twig;
});


require_once __DIR__ . '/routes.php';
