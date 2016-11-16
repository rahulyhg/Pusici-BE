<?php

namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class PassLowerException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must contain at least {{quantity}} lower-case letters',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must contain less than {{quantity}} lower-case letters',
        ],
    ];
}
