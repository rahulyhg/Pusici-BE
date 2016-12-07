<?php

/**
 * This playground section is used for testing
 */
$app->get('/playground', function ($request, $response) {

    var_dump(base64_encode(mcrypt_create_iv(32)));

    var_dump(base64_encode(openssl_random_pseudo_bytes(32)));
});

/*
    // get request headers
    // Basic access authentication
    $authorization = $request->getHeaderLine('authorization');

    if ($authorization == '' || $authorization != 'Basic ' . $config->ba->secretKey) {
        // 401 Unauthorized
        $data = array(
            'authentication' => 'Basic access authentication failed.'
        );
        return $response->withJson($data, 401);
    }
*/