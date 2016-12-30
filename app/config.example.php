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
    // JSON Web Token Authentication
    'jwt' => (object) array(
        'serverName' => 'example.com',
        // Key for signing the JWT; generated with base64_encode(openssl_random_pseudo_bytes(64))
        'secretKey' => '',
        // Algorithm used to sign the token
        'algorithm' => 'HS512',
        // Expiration time in seconds (30 min.)
        'accessExpire' => 1800,
        // Expiration time in seconds (1 week)
        'refreshExpire' => 604800
    ),
    // Basic Authentication
    'ba' => (object) array(
        'user' => '',
        // password_hash('password', PASSWORD_BCRYPT)
        'password' => ''
    ),
    'info' => array(
        'name' => 'Example',
        'url' => 'www.example.com',
        'version' => '1.0'
    )
);
