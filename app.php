<?php
require_once __DIR__ . '/config/.env.php';
require_once __DIR__ . '/vendor/autoload.php';

use Core\Router;

new Router($_SERVER['REQUEST_URI']);

$helper = new \Core\Helper();

require __DIR__ . '/routes/web.php';

Router::run();
