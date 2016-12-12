<?php
namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as V;

/**
 * Verifies quantity of digits in input
 */
class PassDigit extends AbstractRule
{

    public $quantity;

    public function __construct($quantity)
    {
        $this->quantity = (int) $quantity;
    }

    public function validate($input)
    {
        return V::regex("/(?=(.*\d){{$this->quantity}})/")->validate($input);
    }
}
