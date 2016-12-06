<?php

/**
 * This is an example of the main configuration of the application
 * Move the content with corret values to config.php file
 *
 * (object) cast allows you to use the object syntax, use: $config->database instead of $config['database']
 */
return (object) array(
    // Database settings
    'database' => array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ),
    // JSON Web Token (Bearer)
    'jwt' => (object) array(
        'serverName' => 'example.com',
        // Key for signing the JWT; generated with base64_encode(openssl_random_pseudo_bytes(64))
        'secretKey' => '',
        // Algorithm used to sign the token
        'algorithm' => 'HS512',
        // Expiration time in seconds
        'expire' => 1800
    ),
    // Basic access authentication
    'ba' => (object) array(
        'secretKey' => ''
    ),
    'info' => array(
        'appName' => 'Example',
        'appURL' => 'http://www.example.com/'
    )
);
