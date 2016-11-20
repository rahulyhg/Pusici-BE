<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelValidation extends Model
{
    protected $validator;

    public function __construct($validator, $attributes = array())
    {
        parent::__construct($attributes); // Calls Default Constructor
        $this->validator = $validator;
    }

    // Validates attributes
    public function validate()
    {
        return $this->validator->validate($this->attributes);
    }

    // Returns validation errors
    public function errors()
    {
        return $this->validator->errors();
    }
}
