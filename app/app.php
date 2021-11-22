<?php

use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Factory\AppFactory;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;
use Slim\Psr7\Factory\UriFactory;
use App\Utilities\Language;
use Slim\Csrf\Guard;
use App\Extensions\CsrfExtension;
use DI\Container;

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
            'name'         => $_ENV['APP_NAME'],
            'langDefault'  => $_ENV['APP_LANG_DEFAULT']
        ]
    ];
});


// Auto language setup
$container->set('language', function () use ($container) {
    return Language::getLanguage($container->get('settings')['langDefault']);
});


// Twig
$container->set('view', function (ContainerInterface $container) use ($app) {
    $lang  = $container->get('language');
    $views = sprintf('%s/app/Views/%s/', ABSPATH, $lang);

    $twig = Twig::create($views, [
        'cache' => false
    ]);

    $twig->addExtension(new DebugExtension());

    $twig->addExtension(new TwigExtension(
        $app->getRouteCollector()->getRouteParser(),
        (new UriFactory())->createFromGlobals($_SERVER),
        '/'
    ));

     $twig->addExtension(new CsrfExtension($container->get('csrf')));

    return $twig;
});


// Anti-csrf
$container->set('csrf', function () use ($app) {
    return new Guard($app->getResponseFactory());
});
$app->add('csrf');

require_once __DIR__ . '/routes.php';
