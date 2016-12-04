<?php
namespace App\Helpers;

class Generator
{

    /**
     * Universally unique identifier
     *
     * @param bool $hyphens
     * @return string
     */
    static function uuid(bool $hyphens = true)
    {
        $uuid = ($hyphens == true) ? '%04x%04x-%04x-%04x-%04x-%04x%04x%04x' : '%04x%04x%04x%04x%04x%04x%04x%04x';

        return sprintf($uuid,
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    /**
     * Globally unique identifier
     *
     * @param bool $hyphens
     * @return string
     */
    static function guid(bool $hyphens = false)
    {
        return self::uuid($hyphens);
    }
}
