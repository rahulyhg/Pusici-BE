<?php
namespace App\Api;

$app->get('/api/info', function ($request, $response) use ($config) {
    return code_200($response, $config->info);
});
