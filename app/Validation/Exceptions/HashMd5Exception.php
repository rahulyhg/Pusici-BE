<?php
namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class HashMd5Exception extends ValidationException
{

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be MD5 hash'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be MD5 hash'
        ]
    ];
}
