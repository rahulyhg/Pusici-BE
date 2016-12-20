<?php
namespace App\Api;

use App\Models\RefreshToken;
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

        /**
         * Return decoded Access Token
         * 200 - Return Access Token data
         * 400 - jwt_error (unexpected value, expired, invalid signature)
         */
        $app->post('/api/tokens/decode', function ($request, $response) {
            return self::decodeAccessToken($request, $response);
        });
    }

    private static function getAccessToken($request, $response)
    {
        // Get request body parameter
        $grantType = $request->getParsedBodyParam('grant_type'); // password, refresh_token, ...

        switch ($grantType) {
            case 'password':
                $email = $request->getParsedBodyParam('email');
                $password = $request->getParsedBodyParam('password');
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
                if (password_verify($password, $user->userData->password)) {
                    $tokenData = self::getTokenData($user);
                    // Save the Refresh Token
                    self::saveRefreshToken($user, $tokenData);

                    return code_200($response, self::formatTokenData($tokenData));
                }
            }
        } catch (\PDOException $e) {
            return code_500($response, $e->getMessage());
        }

        return code_400($response, 'invalid_grant', 'Credentials are invalid.');
    }

    private static function refreshTokenGrantType($response, $refreshToken)
    {
        try {
            $token = RefreshToken::find($refreshToken);

            if ($token != null) {

                if ($token->used != null) {
                    return code_400($response, 'invalid_grant', 'Refresh Token is already used.');
                }

                if (strtotime($token->expire) < time()) {
                    return code_400($response, 'invalid_grant', 'Refresh Token is expired.');
                }

                $user = $token->user;
                $tokenData = self::getTokenData($user);

                // Mark the current Refresh Token as used
                $token->used = date('Y-m-d H:i:s', time());
                $token->save();

                // Save the new Refresh Token
                self::saveRefreshToken($user, $tokenData);

                return code_200($response, self::formatTokenData($tokenData));
            }
        } catch (\PDOException $e) {
            return code_500($response, $e->getMessage());
        }

        return code_400($response, 'invalid_grant', 'Refresh Token is invalid.');
    }

    /**
     * Return Access and Refresh Token data
     */
    private static function getTokenData(User $user)
    {
        // Unique identifier for the Access Token
        $tokenId = base64_encode(mcrypt_create_iv(32));
        // Ensure the Refresh Token is unique
        do {
            $refreshToken = base64_encode(mcrypt_create_iv(32));
        } while (null !== RefreshToken::find($refreshToken));

        // Time when the token was generated and when will expire
        $issuedAt = time();
        $notBefore = $issuedAt;
        $accessExpire = $notBefore + self::$config->jwt->accessExpire;
        $refreshExpire = $notBefore + self::$config->jwt->refreshExpire;
        $issuer = self::$config->jwt->serverName;

        // Create the jwt token as an array
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

        $secretKey = base64_decode(self::$config->jwt->secretKey);

        return (object) array(
            // Encode the token array to a JWT string (The output string can be validated at http://jwt.io/)
            'access' => JWT::encode($token, $secretKey, self::$config->jwt->algorithm),
            'accessExpire' => $accessExpire,
            'refresh' => $refreshToken,
            'refreshExpire' => $refreshExpire
        );
    }

    /**
     * Prepare Token Data for response output
     */
    private static function formatTokenData($tokenData)
    {
        $result = array(
            'access_token' => $tokenData->access,
            'expires_in' => $tokenData->accessExpire,
            'token_type' => 'Bearer',
            'refresh_token' => $tokenData->refresh
        );

        return $result;
    }

    private static function saveRefreshToken($user, $tokenData)
    {
        $token = new RefreshToken();
        $token->id = $tokenData->refresh;
        $token->user_id = $user->id;
        $token->expire = date('Y-m-d H:i:s', $tokenData->refreshExpire);
        $token->save();
    }

    private static function decodeAccessToken($request, $response)
    {
        $token = $request->getParsedBodyParam('access_token');

        $secretKey = base64_decode(self::$config->jwt->secretKey);
        $algorithm = self::$config->jwt->algorithm;

        try {
            $data = JWT::decode($token, $secretKey, array(
                $algorithm
            ));
        } catch (\Exception $e) {
            return code_400($response, 'jwt_error', $e->getMessage());
        }

        return code_200($response, $data);
    }
}

Tokens::register($app, $config);
