<?php

use App\Helpers\Generator;
use App\Models\Token;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Gets all Users
 */
$app->get('/api/users', function ($request, $response) {

    $filter = $request->getQueryParam('filter', $default = null);
    $orderBy = $request->getQueryParam('orderby', $default = null);
    $builder = Capsule::table('users');

    if (isset($filter))
    {
        $array = explode(':', $filter);
        $column = $array[0];
        $operator = isset($array[1]) ? $array[1] : '=';
        $value = isset($array[2]) ? $array[2] : null;

        $builder = $builder->where($column, $operator, $value);
    }

    if (isset($orderBy))
    {
        $array = explode(':', $orderBy);
        $column = $array[0];
        $direction = isset($array[1]) ? $array[1] : 'asc';

        $builder = $builder->orderBy($column, $direction);
    }

    try
    {
        $users = $builder->get();
    }
    catch (Illuminate\Database\QueryException $e)
    {
        $data = array('error' => $e->getMessage());
        return $response->withJson($data, 400, JSON_UNESCAPED_UNICODE);
    }
    catch (PDOException $e)
    {
        $data = array('error' => utf8_encode($e->getMessage()));
        return $response->withjson($data, 500);
    }

    return $response->withJson($users, 200, JSON_UNESCAPED_UNICODE);
});

/**
 * Gets User with id
 */
$app->get('/api/users/{id}', function ($request, $response) {

    $id = $request->getAttribute('id');

    try
    {
        $user = User::find($id);
    }
    catch (PDOException $e)
    {
        $data = array('error' => utf8_encode($e->getMessage()));
        return $response->withjson($data, 500);
    }

    if (!isset($user))
    {
        $data = array('user' => "User with id '$id' not found.");
        return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
    }

    return $response->withJson($user, 200, JSON_UNESCAPED_UNICODE);
});

/**
 * Creates new User
 */
$app->post('/api/users', function ($request, $response) {

    $user = new User;
    $user->id = Generator::guid($hyphens = false);
    $user->first_name = $request->getParsedBodyParam('first_name');
    $user->last_name = $request->getParsedBodyParam('last_name');
    $user->email = $request->getParsedBodyParam('email');

    $token = new Token;
    $token->user_id = $user->id;
    $token->password = $request->getParsedBodyParam('password');

    $isValid = $user->validate();
    $isValid &= $token->validate();

    if ($isValid)
    {
        try
        {
            $user->save();
            $token->save();
        }
        catch (PDOException $e)
        {
            $data = array('error' => utf8_encode($e->getMessage()));
            return $response->withjson($data, 500);
        }
    }
    else
    {
        return $response->withJson(array_merge($user->errors(), $token->errors()), 400, JSON_UNESCAPED_UNICODE);
    }

    $data = array('id' => $user->id);
    return $response->withJson($data, 201, JSON_UNESCAPED_UNICODE);
});

/**
 * Updates User with id
 */
$app->put('/api/users/{id}', function ($request, $response) {

    $id = $request->getAttribute('id');

    try
    {
        $user = User::where('id', '=', "$id")->first();

        if (isset($user))
        {
            $user->first_name = $request->getParsedBodyParam('first_name', $default = $user->first_name);
            $user->last_name = $request->getParsedBodyParam('last_name', $default = $user->last_name);
            $user->email = $request->getParsedBodyParam('email', $default = $user->email);

            if ($user->validate())
            {
                $user->save();
            }
            else
            {
                return $response->withJson($user->errors(), 400, JSON_UNESCAPED_UNICODE);
            }
        }
        else
        {
            $data = array('user' => "User with id '$id' not found.");
            return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
        }
    }
    catch (PDOException $e)
    {
        $data = array('error' => utf8_encode($e->getMessage()));
        return $response->withjson($data, 500);
    }

    return $response->withJson(null, 204);
});

/**
 * Deletes User with id
 */
$app->delete('/api/users/{id}', function ($request, $response) {

    $id = $request->getAttribute('id');

    try
    {
        $user = User::find($id);

        if (!isset($user))
        {
            $data = array('user' => "User with id '$id' not found.");
            return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
        }

        $user->delete();
    }
    catch (PDOException $e)
    {
        $data = array('error' => utf8_encode($e->getMessage()));
        return $response->withjson($data, 500);
    }

    return $response->withJson(null, 204);
});
