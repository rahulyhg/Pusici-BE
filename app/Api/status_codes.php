<?php
namespace App\Api;

/**
 * 200 OK
 */
function code_200($response, $data)
{
    return $response->withJson($data, 200, JSON_UNESCAPED_UNICODE);
}

/**
 * 201 Created
 */
function code_201($response, $data)
{
    return $response->withJson($data, 201, JSON_UNESCAPED_UNICODE);
}

/**
 * 204 No Content
 */
function code_204($response)
{
    return $response->withJson(null, 204);
}

/**
 * 400 Bad Request
 */
function code_400($response, $error, $errorDescription = '')
{
    $data = errorData($error, $errorDescription);
    return $response->withJson($data, 400, JSON_UNESCAPED_UNICODE);
}

/**
 * 401 Unauthorized
 */
function code_401($response, $error, $errorDescription = '')
{
    $data = errorData($error, $errorDescription);
    return $response->withJson($data, 401, JSON_UNESCAPED_UNICODE);
}

/**
 * 403 Forbidden
 */
function code_403($response)
{
    $data = errorData('access_denied', 'You don\'t have permission.');
    return $response->withJson($data, 403);
}

/**
 * 404 Not Found
 */
function code_404($response, $error, $errorDescription = '')
{
    $data = errorData($error, $errorDescription);
    return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
}

/**
 * 500 Internal Server Error
 */
function code_500($response, $errorDescription = '')
{
    // utf8_encode() - $errorDescription string can be in different encoding than utf8
    $data = errorData('internal_server_error', utf8_encode($errorDescription));
    return $response->withjson($data, 500, JSON_UNESCAPED_UNICODE);
}

/**
 * 501 Not Implemented
 */
function code_501($response)
{
    $data = errorData('not_implemented', '');
    return $response->withJson($data, 501);
}

/**
 * Return error
 */
function error($response, $error, $errorDescription = '')
{
    $data = errorData($error, $errorDescription);
    return $response->withHeader('Content-Type', 'application/json')->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

/**
 * Returns error data
 */
function errorData(string $error, $errorDescription)
{
    $data['error'] = $error;
    if ($errorDescription != '') {
        $data['error_description'] = $errorDescription;
    }
    return $data;
}
