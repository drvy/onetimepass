<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\HomeController;
use App\Controllers\MessageController;
use App\Controllers\CsrfController;
use App\Controllers\ViewController;

// Home
$app->get('/', HomeController::class . ':index');
