<?php
namespace App\Api;

use App\Models\User;
use Firebase\JWT\JWT;

/**
 * This endpoint is used to return Access and Refresh tokens for authenticated users
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
 * Gets new Access Token for User
 */
$app->post('/api/tokens', function ($request, $response) {

    // Get request body parameter
    $grantType = $request->getParsedBodyParam('grant_type'); // password, refresh_token, ...

    switch ($grantType) {
        case 'password':
            $email = $request->getParsedBodyParam('email');
            $password = md5($request->getParsedBodyParam('password'));
            return passwordGrantType($response, $email, $password);
        case 'refresh_token':
            $refreshToken = $request->getParsedBodyParam('refresh_token');
            return refreshTokenGrantType($response, $refreshToken);
        default:
            return code_400($response, 'unsupported_grant_type');
    }
});

/**
 * Gets new Access Token for User based on credentials
 *
 * @param response $response
 * @param string $email
 * @param string $password
 * @return response
 */
function passwordGrantType($response, $email, $password)
{
    global $config;

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

                return code_200($response, $data);
            }
        }
    } catch (\PDOException $e) {
        return code_500($response, utf8_encode($e->getMessage()));
    }

    return code_400($response, 'invalid_grant', 'Credentials are invalid.');
}

/**
 * Returns error data
 */
function errorData(string $error, string $errorDescription)
{
    $data['error'] = $error;
    if ($errorDescription != '')
        $data['error_description'] = $errorDescription;
    return $data;
}

/**
 * 200 OK
 */
function code_200($response, $data)
{
    return $response->withJson($data, 200);
}

/**
 * 400 Bad Request
 */
function code_400($response, $error, $errorDescription = '')
{
    $data = errorData($error, $errorDescription);
    return $response->withJson($data, 400);
}

/**
 * 500 Internal Server Error
 */
function code_500($response, $errorDescription = '')
{
    $data = errorData('internal_server_error', $errorDescription);
    return $response->withjson($data, 500);
}

/**
 * 501 Not Implemented
 */
function code_501($response)
{
    $data = array(
        'error' => 'not_implemented'
    );
    return $response->withJson($data, 501);
}

/**
 * Gets new Access Token for User based on refresh token
 *
 * @param response $response
 * @param string $refreshToken
 * @return response
 */
function refreshTokenGrantType($response, $refreshToken)
{
    return code_501($response);
}

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
 * Returns Token for User
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
