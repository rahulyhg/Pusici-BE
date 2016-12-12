<?php
namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class AlphaLatin2Exception extends ValidationException
{

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must contain only letters'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not contain letters'
        ]
    ];
}
