<?php
namespace App\Api;

use App\Helpers\Generator;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Get all Users
 *
 * - simplified query message parameters can be used (?filter=first_name:=:John&orderby=last_name:asc)
 *
 * 200 - Return array of users
 * 400 - database_error
 * 500 - Internal Server Error
 */
$app->get('/api/users', function ($request, $response) {
    // Get query message parameters
    $filter = $request->getQueryParam('filter', $default = null);
    $orderBy = $request->getQueryParam('orderby', $default = null);

    // Using The Query Builder
    $builder = Capsule::table('users'); // all users

    if (isset($filter)) {
        $array = explode(':', $filter);
        $column = $array[0];
        $operator = isset($array[1]) ? $array[1] : '=';
        $value = isset($array[2]) ? $array[2] : null;

        $builder = $builder->where($column, $operator, $value); // sql where clause
    }

    if (isset($orderBy)) {
        $array = explode(':', $orderBy);
        $column = $array[0];
        $direction = isset($array[1]) ? $array[1] : 'asc'; // asc or desc

        $builder = $builder->orderBy($column, $direction); // sql order by keyword
    }

    try {
        $users = $builder->get(); // execute query
    } catch (\Illuminate\Database\QueryException $e) {
        return code_400($response, 'database_error', $e->getMessage());
    } catch (\PDOException $e) {
        return code_500($response, $e->getMessage());
    }

    return code_200($response, $users);
});

/**
 * Get User with id
 *
 * 200 - Return User
 * 404 - user_not_found
 * 500 - Internal Server Error
 */
$app->get('/api/users/{id}', function ($request, $response) {
    $id = $request->getAttribute('id');

    try {
        // Using The Eloquent ORM
        $user = User::find($id);
    } catch (\PDOException $e) {
        return code_500($response, $e->getMessage());
    }

    if (! isset($user)) {
        return code_404($response, 'user_not_found', "User with id '$id' not found.");
    }

    return code_200($response, $user);
});

/**
 * Create new User
 *
 * 201 - Return User id
 * 400 - wrong_input, email_exist
 * 500 - Internal Server Error
 */
$app->post('/api/users', function ($request, $response) {
    $user = new User();
    // Get request body parameters
    $user->first_name = $request->getParsedBodyParam('first_name');
    $user->last_name = $request->getParsedBodyParam('last_name');
    $user->email = $request->getParsedBodyParam('email');
    $userData = new UserData();
    $userData->password = $request->getParsedBodyParam('password');

    // Validate User and Password model attributes
    $isValid = $user->validate();
    $isValid &= $userData->validate();

    if ($isValid) {
        try {
            // Ensure the User id is unique
            do {
                $user->id = Generator::guid();
            } while (null !== User::find($user->id));
            $userData->user_id = $user->id;
            // Ensure the User email is unique
            if (null !== User::where('email', '=', $user->email)->first()) {
                return code_400($response, 'email_exist', "User with the email address '$user->email' already exists.");
            }

            $user->save();
            // Temporary solution (md5 is not strong enough for storing passwords)
            $userData->password = md5($userData->password);
            $userData->save();
        } catch (\PDOException $e) {
            return code_500($response, $e->getMessage());
        }
    } else {
        return code_400($response, 'wrong_input', array_merge($user->errors(), $userData->errors()));
    }

    $data = array(
        'id' => $user->id
    );
    return code_201($response, $data);
});

/**
 * Update User with id
 *
 * 204 - User updated
 * 400 - wrong_input, email_exist
 * 404 - user_not_found
 * 500 - Internal Server Error
 */
$app->put('/api/users/{id}', function ($request, $response) {
    $id = $request->getAttribute('id');

    try {
        $user = User::where('id', '=', $id)->first();

        if (isset($user)) {
            $originalEmail = $user->email;

            $user->first_name = $request->getParsedBodyParam('first_name', $default = $user->first_name);
            $user->last_name = $request->getParsedBodyParam('last_name', $default = $user->last_name);
            $user->email = $request->getParsedBodyParam('email', $default = $user->email);

            if ($user->validate()) {
                // Ensure the User email is unique
                if ($originalEmail != $user->email && null !== User::where('email', '=', $user->email)->first()) {
                    return code_400($response, 'email_exist', "User with the email address '$user->email' already exists.");
                }

                $user->save();
            } else {
                return code_400($response, 'wrong_input', $user->errors());
            }
        } else {
            return code_404($response, 'user_not_found', "User with id '$id' not found.");
        }
    } catch (\PDOException $e) {
        return code_500($response, $e->getMessage());
    }

    return code_204($response);
});

/**
 * Delete User with id
 *
 * 204 - User deleted
 * 404 - user_not_found
 * 500 - Internal Server Error
 */
$app->delete('/api/users/{id}', function ($request, $response) {
    $id = $request->getAttribute('id');

    try {
        $user = User::find($id);

        if (! isset($user)) {
            return code_404($response, 'user_not_found', "User with id '$id' not found.");
        }

        $user->delete();
    } catch (\PDOException $e) {
        return code_500($response, $e->getMessage());
    }

    return code_204($response);
});
