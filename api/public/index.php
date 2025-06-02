<?php

define('ABSPATH', realpath(__DIR__ . '/../'));

require_once ABSPATH . '/vendor/autoload.php';
require_once ABSPATH . '/app/app.php';

if (php_sapi_name() !== 'cli') {
    $app->run();
}
