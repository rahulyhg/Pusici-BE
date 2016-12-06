<?php
namespace App\Validation;

use Respect\Validation\Validator as V;

class TokenValidator extends ValidatorBase
{

    public function initRules()
    {
        V::with('App\\Validation\\Rules\\');
        $this->rules['password'] = V::passLower(2)->passUpper(2)
            ->passDigit(2)
            ->passSpecial(0)
            ->noWhitespace()
            ->length(8, 20)
            ->setName('Password');
    }

    public function initMessages()
    {
        $this->messages = [];
    }
}
