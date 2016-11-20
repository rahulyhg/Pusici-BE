<?php

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as V;

class UserValidator extends ValidatorBase
{
    public function initRules()
    {
        V::with('App\\Validation\\Rules\\');

        $this->rules['first_name'] = V::alphaLatin2()->noWhitespace()->length(1, 20)->setName('First name');
        $this->rules['last_name'] = V::alphaLatin2()->noWhitespace()->length(1, 20)->setName('Last name');
        $this->rules['email'] = V::email();
    }

    public function initMessages()
    {
        $this->messages = [
            'alphaLatin2'   => '{{name}} must only contain alphabetic characters.',
            'noWhitespace'  => '{{name}} must not contain white spaces.',
            'length'        => '{{name}} length must be between {{minValue}} and {{maxValue}}.',
            'email'         => 'Please make sure you typed a correct email address.',
        ];
    }
}
