<?php

namespace App\Models;

use App\Validation\UserValidator;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users'; // link model to db table explicitly
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'first_name', 'last_name', 'email', 'password'];

    public $incrementing = false; // disable auto-incrementing of primary key (id is not integer type)
    public $errors = [];

    public function isValid()
    {
        $userValidator = new UserValidator();
        $isValid = $userValidator->validate($this->attributes);

        if (!$isValid) {
            $this->errors = $userValidator->errors();
        }

        return $isValid;
    }
}
