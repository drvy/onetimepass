<?php

define('ABSPATH', realpath(__DIR__ . '/../'));

require_once ABSPATH . '/vendor/autoload.php';
require_once ABSPATH . '/app/app.php';

$app->run();
