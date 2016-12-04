<?php
namespace App\Models;

use App\Validation\UserValidator;

class User extends ModelValidation
{
    // disable auto-incrementing of primary key (id is not an integer type)
    public $incrementing = false;
    // link model to db table explicitly
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email'
    ];

    public function __construct($attributes = array())
    {
        parent::__construct(new UserValidator(), $attributes);
    }

    // Define relationship
    public function token()
    {
        // The first argument is the name of the related model
        // The second argument is the foreign key (optional)
        // The third argument is the local_key (optional)
        return $this->hasOne('App\Models\Token', 'user_id');
    }
}
