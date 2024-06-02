<?php
session_start();
define('ABSPATH', realpath(__DIR__ . '/../'));

require_once ABSPATH . '/vendor/autoload.php';
require_once ABSPATH . '/app/App.php';

(new App())->run();
