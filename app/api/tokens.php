<?php
use App\Models\User;
use Firebase\JWT\JWT;

/**
 * These endpoints are used to generate Access and Refresh tokens
 *
 * Why to use POST method:
 * POST requests are never cached
 * POST requests do not remain in the browser history
 * POST requests cannot be bookmarked
 * POST requests have no restrictions on data length
 *
 * Why NOT to use GET method:
 * GET requests can be cached (on both sides client and server)
 * GET requests remain in the browser history
 * GET requests can be bookmarked
 * GET requests should never be used when dealing with sensitive data
 * GET requests have length restrictions
 * GET requests should be used only to retrieve data
 */

/**
 * Gets new Access Token for User based on login information
 */
$app->post('/api/tokens', function ($request, $response) {

    global $config;

    // Get request body parameters
    $email = $request->getParsedBodyParam('email');
    $password = md5($request->getParsedBodyParam('password'));

    try {

        $user = User::where('email', '=', $email)->first();

        if ($user != null) {
            if ($password == $user->token->password) {
                $token = getToken($user);
                $data = array(
                    'access_token' => $token->access,
                    'expires_in' => $token->expire,
                    'token_type' => 'Bearer',
                    'refresh_token' => $token->refresh
                );

                // Store the Refresh Token
                $user->token->refresh_token = $token->refresh;
                // Make the expiration of the Refresh Token 2 x times longer than expiration of the Access Token
                $user->token->expire = date('Y-m-d H:i:s', $token->expire + $config->jwt->expire);
                $user->token->save();

                return $response->withJson($data, 200);
            }
        }
    } catch (PDOException $e) {
        $data = array(
            'error' => 'internal_server_error',
            'error_description' => utf8_encode($e->getMessage())
        );
        return $response->withjson($data, 500);
    }

    // 400 Bad Request
    $data = array(
        'error' => 'invalid_grant',
        'error_description' => 'Credentials are invalid.'
    );
    return $response->withJson($data, 400);
});

/**
 * Gets new Access Token for User based on Refresh Token
 */
$app->post('/api/tokens/refresh', function ($request, $response) {
    $jwt = $request->getParsedBodyParam('refresh-token');

    if ($jwt != '') {
        try {
            $secretKey = 'myverysecretkey';
            $token = JWT::decode($jwt, $secretKey, array(
                'HS512'
            ));
            print_r($token);
            exit();
        } catch (Exception $e) {
            echo '401';
            exit();
        }
    }
});

/**
 * Creates Token for User
 *
 * @param User $user
 * @return object
 */
function getToken(User $user)
{
    global $config;

    // Prepare token data

    // Unique identifier for the token
    // Base64 is not encryption. It's an encoding.
    $tokenId = base64_encode(mcrypt_create_iv(32));

    // Time when the token was generated and when will expire
    $issuedAt = time();
    $notBefore = $issuedAt;
    $expire = $notBefore + $config->jwt->expire;
    $issuer = $config->jwt->serverName;

    // Create the token as an array
    $token = [
        // Reserved Claims
        'jti' => $tokenId,
        'iat' => $issuedAt,
        'nbf' => $notBefore,
        'exp' => $expire,
        'iss' => $issuer,
        // Custom Claims
        'user' => [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'permissions' => []
        ]
    ];

    return (object) array(
        // Encode the token array to a JWT string (The output string can be validated at http://jwt.io/)
        'access' => JWT::encode($token, $config->jwt->secretKey, $config->jwt->algorithm),
        'expire' => $expire,
        'refresh' => $tokenId
    );
}
