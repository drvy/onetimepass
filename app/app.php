<?php

namespace App;

use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Factory\AppFactory;
use App\Classes\AppContainer;
use App\Classes\Log;
use App\Classes\Server;
use App\Utilities\Settings;

// App creation
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
AppContainer::getInstance()->set($app);

// App settings
$container->set('settings', (new Settings())); // loads settings and env
$app->setBasePath($container->get('settings')->get('basepath', null));
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Extensions
$container->set('server', (new Server($container)));
$container->set('log', (new Log($container)));

// Database
$container->set('db', (new Capsule()));
$container->get('db')->addConnection($container->get('settings')->get('database'));
$container->get('db')->setAsGlobal();
$container->get('db')->bootEloquent();

require_once __DIR__ . '/routes.php';
