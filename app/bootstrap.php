<?php
require '../vendor/autoload.php';
require 'Api/status_codes.php';

// Load configuration
$config = require('config.php');

// Configure the Slim application
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => $config->database
    ]
]);

/*
 * Expose the Slim application container
 * - store additional data for later use
 * - store decoded JWT
 * - store Eloquent database information
 */
$container = $app->getContainer();

/*
 * Add middleware layer (the first, inner layer)
 * Protect API with JWT Authentication
 * Request header should contain Authorization key with 'Bearer <token>' value
 */
$app->add(new \Slim\Middleware\JwtAuthentication([
    'path' => [
        '/api'
    ],
    'passthrough' => [
        '/api/tokens'
    ],
    'secret' => base64_decode($config->jwt->secretKey),
    'callback' => function ($request, $response, $arguments) use ($container) {
        $container['jwt'] = $arguments['decoded'];
    },
    'error' => function ($request, $response, $arguments) {
        return \App\Api\error($response, 'authentication_failed', $arguments['message']);
    }
]));

/*
 * Add middleware layer (the second, outer layer)
 * Token can be retrieved via HTTP Basic Authentication
 * Request header should contain Authorization key with 'Basic <base64_encode('user:password')>' value
 */
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    'path' => '/api/tokens',
    'users' => [
        $config->ba->user => $config->ba->password
    ],
    'error' => function ($request, $response, $arguments) {
        return \App\Api\error($response, 'authentication_failed', $arguments['message']);
    }
]));

// Initialize database
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Set the database information
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// Register endpoints
require 'Api/users.php';
require 'Api/tokens.php';
require 'Api/playground.php';
