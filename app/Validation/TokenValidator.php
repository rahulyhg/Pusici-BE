<?php

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as V;

class TokenValidator extends ValidatorBase
{
    public function initRules()
    {
        V::with('App\\Validation\\Rules\\');
        $this->rules['password'] = V::hashMd5()->setName('Password');
    }

    public function initMessages()
    {
        $this->messages = [];
    }
}
