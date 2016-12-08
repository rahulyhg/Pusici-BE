<?php
require '../vendor/autoload.php';

$config = require ('config.php');

// Configuration of Slim application
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => $config->database
    ]
]);

$container = $app->getContainer();

// initialize database
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// require endpoints
require_once 'Api/status_codes.php';
require_once 'Api/users.php';
require_once 'Api/tokens.php';
require_once 'Api/playground.php';
