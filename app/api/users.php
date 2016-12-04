<?php
use App\Helpers\Generator;
use App\Models\Token;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Gets all Users
 *
 * simplified query message parameters can be used
 * e.g. ?filter=first_name:=:John&orderby=last_name:asc
 *
 * 200 - returns array of users
 * 400 - Bad Request
 * 500 - Internal Server Error
 */
$app->get('/api/users', function ($request, $response)
{
    // Get query message parameters
    $filter = $request->getQueryParam('filter', $default = null);
    $orderBy = $request->getQueryParam('orderby', $default = null);

    // Using The Query Builder
    $builder = Capsule::table('users'); // all users

    if (isset($filter))
    {
        $array = explode(':', $filter);
        $column = $array[0];
        $operator = isset($array[1]) ? $array[1] : '=';
        $value = isset($array[2]) ? $array[2] : null;

        $builder = $builder->where($column, $operator, $value); // sql where clause
    }

    if (isset($orderBy))
    {
        $array = explode(':', $orderBy);
        $column = $array[0];
        $direction = isset($array[1]) ? $array[1] : 'asc'; // asc or desc

        $builder = $builder->orderBy($column, $direction); // sql order by keyword
    }

    try
    {
        $users = $builder->get(); // execute query
    } catch (Illuminate\Database\QueryException $e)
    {
        $data = array (
            'error' => $e->getMessage()
        );
        return $response->withJson($data, 400, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e)
    {
        $data = array (
            'error' => utf8_encode($e->getMessage())
        );
        return $response->withjson($data, 500);
    }

    return $response->withJson($users, 200, JSON_UNESCAPED_UNICODE);
});

/**
 * Gets User with id
 *
 * 200 - returns user
 * 404 - user id not found
 * 500 - Internal Server Error
 */
$app->get('/api/users/{id}', function ($request, $response)
{
    $id = $request->getAttribute('id');

    try
    {
        // Using The Eloquent ORM
        $user = User::find($id);
    } catch (PDOException $e)
    {
        $data = array (
            'error' => utf8_encode($e->getMessage())
        );
        return $response->withjson($data, 500);
    }

    if (!isset($user))
    {
        $data = array (
            'user' => "User with id '$id' not found."
        );
        return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
    }

    return $response->withJson($user, 200, JSON_UNESCAPED_UNICODE);
});

/**
 * Creates new User
 *
 * 201 - returns user id
 * 400 - Bad Request
 * 500 - Internal Server Error
 */
$app->post('/api/users', function ($request, $response)
{
    $user = new User();
    $user->id = Generator::guid();
    // get request body parameters
    $user->first_name = $request->getParsedBodyParam('first_name');
    $user->last_name = $request->getParsedBodyParam('last_name');
    $user->email = $request->getParsedBodyParam('email');

    $token = new Token();
    $token->user_id = $user->id;
    $token->password = $request->getParsedBodyParam('password');

    // validate User and Token model attributes
    $isValid = $user->validate();
    $isValid &= $token->validate();

    if ($isValid)
    {
        try
        {
            $user->save();
            $token->password = md5($token->password); // temporary solution (md5 is not strong enough for storing passwords)
            $token->save();
        } catch (PDOException $e)
        {
            $data = array (
                'error' => utf8_encode($e->getMessage())
            );
            return $response->withjson($data, 500);
        }
    } else
    {
        return $response->withJson(array_merge($user->errors(), $token->errors()), 400, JSON_UNESCAPED_UNICODE);
    }

    $data = array (
        'id' => $user->id
    );
    return $response->withJson($data, 201, JSON_UNESCAPED_UNICODE);
});

/**
 * Updates User with id
 *
 * 204
 * 400 - Bad Request
 * 404 - Not Found
 * 500 - Internal Server Error
 */
$app->put('/api/users/{id}', function ($request, $response)
{
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
            } else
            {
                return $response->withJson($user->errors(), 400, JSON_UNESCAPED_UNICODE);
            }
        } else
        {
            $data = array (
                'user' => "User with id '$id' not found."
            );
            return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e)
    {
        $data = array (
            'error' => utf8_encode($e->getMessage())
        );
        return $response->withjson($data, 500);
    }

    return $response->withJson(null, 204);
});

/**
 * Deletes User with id
 *
 * 204
 * 404
 * 500
 */
$app->delete('/api/users/{id}', function ($request, $response)
{
    $id = $request->getAttribute('id');

    try
    {
        $user = User::find($id);

        if (!isset($user))
        {
            $data = array (
                'user' => "User with id '$id' not found."
            );
            return $response->withJson($data, 404, JSON_UNESCAPED_UNICODE);
        }

        $user->delete();
    } catch (PDOException $e)
    {
        $data = array (
            'error' => utf8_encode($e->getMessage())
        );
        return $response->withjson($data, 500);
    }

    return $response->withJson(null, 204);
});
