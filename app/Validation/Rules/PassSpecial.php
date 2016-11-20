<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as V;

/**
 * Verifies quantity of special characters in input
 */
class PassSpecial extends AbstractRule
{
    public $quantity;
    public $characters;
    
    public function __construct($quantity, $characters = null)
    {
        $this->quantity = (int) $quantity;
        $this->characters = $characters;
    }
    
    public function validate($input)
    {
        if (!isset($this->characters)) $this->characters = '\W';

        return V::regex("/(?=(.*{$this->characters}){{$this->quantity}})/")->validate($input);
    }
}
