<?php
namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class PassSpecialException extends ValidationException
{

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must contain at least {{quantity}} special characters'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must contain less than {{quantity}} special characters'
        ]
    ];
}
