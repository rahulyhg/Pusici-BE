<?php
namespace App\Models;

class Token extends ModelValidation
{

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'tokens';
    // Explicitly overriding default value 'id'
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'password',
        'refresh_token',
        'expire'
    ];

    // Define relationship
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
