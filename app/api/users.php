<?php

use App\Helpers\Generator;
use App\Models\User;

$app->get('/api/users', function ($request, $response) { // TODO implement query attributes (where, order_by, ...)

    $users = User::all();

    return $response->withJson($users, 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/api/users/{id}', function ($request, $response) {

    $id = $request->getAttribute('id');
    $user = User::find($id);

    if (!isset($user))
    {
        $data = array('user' => "User with id '$id' not found.");
        return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
    }

    return $response->withJson($user, 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/api/users', function ($request, $response) {

    $user = new User;

    $user->id = Generator::guid(false);
    $user->first_name = $request->getParsedBodyParam('first_name');
    $user->last_name = $request->getParsedBodyParam('last_name');
    $user->email = $request->getParsedBodyParam('email');
    $user->password = $request->getParsedBodyParam('password');

    if ($user->isValid())
    {
        $user->save();
    }
    else
    {
        return $response->withJson($user->errors, 400, JSON_UNESCAPED_UNICODE);
    }

    $data = array('id' => $user->id);
    return $response->withJson($data, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/api/users/{id}', function ($request, $response) {

    $id = $request->getAttribute('id');
    $user = User::where('id', '=', "$id")->first();

    if (isset($user))
    {
        $user->first_name = $request->getParsedBodyParam('first_name');
        $user->last_name = $request->getParsedBodyParam('last_name');
        $user->email = $request->getParsedBodyParam('email');
        $user->password = $request->getParsedBodyParam('password');
        
        if ($user->isValid())
        {
            $user->save();
        }
        else
        {
            return $response->withJson($user->errors, 400, JSON_UNESCAPED_UNICODE);
        }
    }
    else
    {
        $data = array('user' => "User with id '$id' not found.");
        return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
    }

    return $response->withJson(null, 204);
});

$app->delete('/api/users/{id}', function ($request, $response) {

    $id = $request->getAttribute('id');
    $user = User::find($id);

    if (!isset($user))
    {
        $data = array('user' => "User with id '$id' not found.");
        return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
    }

    $user->delete();

    return $response->withJson(null, 204);
});
