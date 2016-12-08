<?php
namespace App\Api;

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
    $data = errorData('not_implemented', '');
    return $response->withJson($data, 501);
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
