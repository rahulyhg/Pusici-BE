<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Extends Eloquent Model with atributes validation
 * - automatically creates Validator instance based on Model name
 */
class ModelValidation extends Model
{

    protected $validator = null;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes); // Calls Default Constructor

        // Create new instance of Validator
        if (is_null($this->validator)) {
            $validatorName = str_replace('Models', 'Validation', get_class($this)) . 'Validator';
            $this->validator = new $validatorName();
        }
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
