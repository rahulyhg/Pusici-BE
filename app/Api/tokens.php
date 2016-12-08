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
class Tokens
{

    private static $config;

    /**
     * Register routes
     *
     * @param \Slim\App $app
     */
    static function register($app, $config)
    {
        self::$config = $config;

        /**
         * Get new Access Token for User
         *
         * 200 - Return Access and Refresh Tokens
         * 400 - unsupported_grant_type, invalid_grant
         * 500 - Internal Server Error
         */
        $app->post('/api/tokens', function ($request, $response) {
            return self::getAccessToken($request, $response);
        });
    }

    private static function getAccessToken($request, $response)
    {
        // Get request body parameter
        $grantType = $request->getParsedBodyParam('grant_type'); // password, refresh_token, ...

        switch ($grantType) {
            case 'password':
                $email = $request->getParsedBodyParam('email');
                $password = md5($request->getParsedBodyParam('password'));
                return self::passwordGrantType($response, $email, $password);
            case 'refresh_token':
                $refreshToken = $request->getParsedBodyParam('refresh_token');
                return self::refreshTokenGrantType($response, $refreshToken);
            default:
                return code_400($response, 'unsupported_grant_type');
        }
    }

    private static function passwordGrantType($response, $email, $password)
    {
        try {

            $user = User::where('email', '=', $email)->first();

            if ($user != null) {
                if ($password == $user->token->password) {
                    $token = self::prepareToken($user);
                    $data = array(
                        'access_token' => $token->access,
                        'expires_in' => $token->accessExpire,
                        'token_type' => 'Bearer',
                        'refresh_token' => $token->refresh
                    );

                    // Store the Refresh Token
                    $user->token->refresh_token = $token->refresh;
                    $user->token->expire = date('Y-m-d H:i:s', $token->refreshExpire);
                    $user->token->save();

                    return code_200($response, $data);
                }
            }
        } catch (\PDOException $e) {
            return code_500($response, utf8_encode($e->getMessage()));
        }

        return code_400($response, 'invalid_grant', 'Credentials are invalid.');
    }

    private static function refreshTokenGrantType($response, $refreshToken)
    {
        return code_501($response);
        /*
         * $app->post('/api/tokens/refresh', function ($request, $response) {
         * $jwt = $request->getParsedBodyParam('refresh-token');
         *
         * if ($jwt != '') {
         * try {
         * $secretKey = 'myverysecretkey';
         * $token = JWT::decode($jwt, $secretKey, array(
         * 'HS512'
         * ));
         * print_r($token);
         * exit();
         * } catch (Exception $e) {
         * echo '401';
         * exit();
         * }
         * }
         * });
         */
    }

    private static function prepareToken(User $user)
    {
        // Unique identifier for the token
        // Base64 is not encryption. It's an encoding.
        $tokenId = base64_encode(mcrypt_create_iv(32));

        // Time when the token was generated and when will expire
        $issuedAt = time();
        $notBefore = $issuedAt;
        $accessExpire = $notBefore + self::$config->jwt->accessExpire;
        $refreshExpire = $notBefore + self::$config->jwt->refreshExpire;
        $issuer = self::$config->jwt->serverName;

        // Create the token as an array
        $token = [
            // Reserved Claims
            'jti' => $tokenId,
            'iat' => $issuedAt,
            'nbf' => $notBefore,
            'exp' => $accessExpire,
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
            'access' => JWT::encode($token, self::$config->jwt->secretKey, self::$config->jwt->algorithm),
            'accessExpire' => $accessExpire,
            'refresh' => $tokenId,
            'refreshExpire' => $refreshExpire
        );
    }
}

Tokens::register($app, $config);
