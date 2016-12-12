<?php
namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as V;

/**
 * MD5
 */
class HashMd5 extends AbstractRule
{

    public function validate($input)
    {
        return V::Xdigit()->length(32, 32)->validate($input);
    }
}
