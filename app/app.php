<?php

use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Factory\AppFactory;
use Slim\Views\TwigExtension;
use Slim\Psr7\Factory\UriFactory;
use Slim\Csrf\Guard;
use Twig\Extension\DebugExtension;
use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Utilities\Env;
use App\Utilities\Language;
use App\Extensions\CsrfExtension;
use App\Extensions\ClientVariablesExtension;

// Session
session_start();

// .env load
(Dotenv::createImmutable(ABSPATH))->safeLoad();


$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);


// Settings
$container->set('settings', function () use ($container) {
    return array(
        'app' => array(
            'name'         => Env::get('APP_NAME'),
            'langDefault'  => Env::get('APP_LANG_DEFAULT')
        )
    );
});


// Javascript client settings
$container->set('clientVariables', function () use ($container) {
    return array(
        'salt'      => Env::get('APP_TOKEN_CLIENT_SALT'),
        'rotations' => (int) Env::get('APP_TOKEN_CLIENT_ROTATIONS'),
        'keySize'   => (int) Env::get('APP_TOKEN_CLIENT_KEYSIZE')
    );
});


// Detect and set language automatically.
$container->set('lang', function () use ($container) {
    return Language::getLanguage($container->get('settings')['langDefault']);
});


// Twig
$container->set('view', function (ContainerInterface $container) use ($app) {
    $lang  = $container->get('lang');
    $views = sprintf('%s/app/Views/%s/', ABSPATH, $lang);

    $twig = Twig::create($views, array(
        'cache' => false
    ));

    $twig->addExtension(new DebugExtension());

    $twig->addExtension(new TwigExtension(
        $app->getRouteCollector()->getRouteParser(),
        (new UriFactory())->createFromGlobals($_SERVER),
        '/'
    ));

    $twig->addExtension(new CsrfExtension($container->get('csrf')));
    $twig->addExtension(new ClientVariablesExtension($container));
    return $twig;
});


// Anti-csrf
$container->set('csrf', function () use ($app) {
    return new Guard($app->getResponseFactory());
});
$app->add('csrf');


// Database
$capsule = new Capsule();
$capsule->addConnection(array(
    'driver'    => Env::get('APP_DATABASE_DRIVER'),
    'host'      => Env::get('APP_DATABASE_HOST'),
    'database'  => Env::get('APP_DATABASE_NAME'),
    'username'  => Env::get('APP_DATABASE_USER'),
    'password'  => Env::get('APP_DATABASE_PASS'),
    'charset'   => Env::get('APP_DATABASE_CHARSET'),
    'collation' => Env::get('APP_DATABASE_COLLATION'),
    'prefix'    => Env::get('APP_DATABASE_PREFIX'),
));

$capsule->setAsGlobal();
$capsule->bootEloquent();

require_once __DIR__ . '/routes.php';
