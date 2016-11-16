<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as V;

class PassLower extends AbstractRule
{
    public $quantity;
    
    public function __construct($quantity)
    {
        $this->quantity = (int) $quantity;
    }
    
    public function validate($input)
    {
        return V::regex("/(?=(.*[a-z]){{$this->quantity}})/")->validate($input);
    }
}
