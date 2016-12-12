<?php
namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as V;

/**
 * a-z, A-Z letters + Czech characters from Latin-2 charset
 */
class AlphaLatin2 extends AbstractRule
{

    public function validate($input)
    {
        return V::alpha('ěščřžýáíéďťňúůĚŠČŘŽÝÁÍÉĎŤŇÚŮ')->validate($input);
    }
}
