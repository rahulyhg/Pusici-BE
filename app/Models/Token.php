<?php
namespace App\Models;

use App\Validation\TokenValidator;

class Token extends ModelValidation
{

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'tokens';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'password',
        'refresh_token',
        'expire'
    ];

    public function __construct($attributes = array())
    {
        parent::__construct(new TokenValidator(), $attributes);
    }

    // Define relationship
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
