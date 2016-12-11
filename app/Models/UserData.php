<?php
namespace App\Models;

class UserData extends ModelValidation
{
    // Disable auto-incrementing of primary key (id is not an integer type)
    public $incrementing = false;
    // Link model to db table explicitly
    protected $table = 'user_data';
    // Explicitly override default value 'id'
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'password'
    ];

    // Define relationship
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
