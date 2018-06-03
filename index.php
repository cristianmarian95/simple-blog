<?php
/**
 * Fainisi Marian Cristian
 * Email: marian.fainisi@gmail.com
 * Site: https://www.cristian-apps.xyz
 * Proiect baze de date 2
 * An 2 Grupa 2 SubGrupa 3
 * Platforma de blog
 * @Version 1.0 beta
 */


if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

session_start();

require_once __DIR__ . '/src/vendor/autoload.php';

$settings = require __DIR__ . '/src/configs/default.php';
$app = new \Slim\App($settings);

require_once __DIR__ . '/src/core/bootstrap.php';
require_once __DIR__ . '/src/core/controllers.php';
require_once __DIR__ . '/src/core/middlewares.php';
require_once __DIR__ . '/src/core/modules.php';
require_once __DIR__ . '/src/core/routes.php';

$app->run();
