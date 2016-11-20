<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as V;

/**
 * Verifies quantity of upper-case letters in input
 */
class PassUpper extends AbstractRule
{
    public $quantity;
    
    public function __construct($quantity)
    {
        $this->quantity = (int) $quantity;
    }
    
    public function validate($input)
    {
        return V::regex("/(?=(.*[A-Z]){{$this->quantity}})/")->validate($input);
    }
}
