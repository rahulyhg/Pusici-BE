<?php

/**
 * This playground section is used for testing
 */
$app->get('/playground', function ($request, $response, $args) {

    $hash = password_hash('password', PASSWORD_BCRYPT);
    var_dump($hash);

    if ($this->has('jwt')) {
        var_dump($this->jwt);
    }
});
