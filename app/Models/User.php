<?php

namespace App\Models;

use App\Validation\UserValidator;

class User extends ModelValidation
{
    public $incrementing = false; // disable auto-incrementing of primary key (id is not integer type)

    protected $table = 'users'; // link model to db table explicitly
    protected $primaryKey = 'id';
    protected $fillable = ['first_name', 'last_name', 'email'];

    public function __construct($attributes = array())
    {
        parent::__construct(new UserValidator(), $attributes);
    }

    // Define relationship
    public function token() {
        return $this->hasOne('App\Models\Token'); // this matches the Eloquent model
    }
}
