<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\HomeController;
use App\Controllers\MessageController;
use App\Controllers\CsrfController;
use App\Controllers\ViewController;

$app->get('/', HomeController::class . ':index');

$app->redirect('/p/', '/', 301);
$app->get('/p/{uuid}[/{pwd}]', ViewController::class . ':index');

/**
 * REST API routes
 */
$app->group('/rest', function (RouteCollectorProxy $group) {
    $group->get('/message', MessageController::class . ':getMessage');
    $group->post('/message', MessageController::class . ':createMessage');
    $group->delete('/message', MessageController::class . ':deleteMessage');

    $group->get('/csrf', CsrfController::class . ':getTokens');
});
