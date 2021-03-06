<?php
namespace App\Api;

use App\Helpers\Generator;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Each endpoint is protected by Permissions middleware
 */

/**
 * Get all Users
 *
 * - simplified query message parameters can be used (?filter=first_name:=:John&orderby=last_name:asc)
 * - required permission: 'users' or 'users-read'
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
})->add(new \App\Middleware\Permissions($container, ['users', 'users-read']));

/**
 * Get User with id
 *
 * - required attribute 'id'
 * - required permission: 'users' or 'users-read'
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
})->add(new \App\Middleware\Permissions($container, ['users', 'users-read']));

/**
 * Get logged-in User profile
 *
 * 200 - Return User profile
 * 400 - jwt_not_found
 * 500 - Internal Server Error
 */
$app->get('/api/users/myself/profile', function ($request, $response) {
    if ($this->has('jwt')) {
        try {
            $user = User::where('email', '=', $this->jwt->user->email)->first();
        } catch (\PDOException $e) {
            return code_500($response, $e->getMessage());
        }
        $data = [];
        if (isset($user)) {
            $data[first_name] = $user->first_name;
            $data[last_name] = $user->last_name;
            $data[email] = $user->email;
        }
        return code_200($response, $data);
    }
    return code_400($response, 'jwt_not_found');
});

/**
 * Create new User
 *
 * - required body parameters: 'first_name', 'last_name', 'email', 'password'
 * - required permission: 'users'
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
            // hash password
            $userData->password = password_hash($userData->password, PASSWORD_BCRYPT);
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
})->add(new \App\Middleware\Permissions($container, ['users']));

/**
 * Update User with id
 *
 * - required attribute 'id'
 * - optional body parameters: 'first_name', 'last_name', 'email'
 * - required permission: 'users'
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
})->add(new \App\Middleware\Permissions($container, ['users']));

/**
 * Change logged-in User password
 *
 * - required body parameters: 'old_password', 'new_password'
 *
 * 204 - password successfully changed
 * 400 - jwt_not_found, user_not_found, password_mismatched, wrong_input
 * 500 - Internal Server Error
 */
$app->put('/api/users/myself/change-password', function ($request, $response) {
    $oldPassword = $request->getParsedBodyParam('old_password');
    $newPassword = $request->getParsedBodyParam('new_password');

    if ($this->has('jwt')) {
        try {
            $user = User::where('email', '=', $this->jwt->user->email)->first();
        } catch (\PDOException $e) {
            return code_500($response, $e->getMessage());
        }

        if (isset($user)) {
            $userData = $user->userData;
            if (password_verify($oldPassword, $userData->password))
            {
                $userData->password = $newPassword;

                if ($userData->validate()) {
                    $userData->password = password_hash($userData->password, PASSWORD_BCRYPT);
                    try {
                        $userData->save();
                    } catch (\PDOException $e) {
                        return code_500($response, $e->getMessage());
                    }
                    return code_204($response);
                }
                return code_400($response, 'wrong_input', $userData->errors());
            }
            return code_400($response, 'password_mismatched', 'The old password is incorrect.');
        }
        return code_400($response, 'user_not_found');
    }
    return code_400($response, 'jwt_not_found');
});

/**
 * Delete User with id
 *
 * - required attribute 'id'
 * - required permission: 'users'
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
})->add(new \App\Middleware\Permissions($container, ['users']));
