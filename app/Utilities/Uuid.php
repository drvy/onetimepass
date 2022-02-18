<?php

namespace App\Utilities;

use Exception;

class Uuid
{
    public static int $length = 16;

    /**
     *
     * @return string|false
     * @throws Exception
     */
    public static function generate(): string
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes(ceil(self::$length / 2));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(ceil(self::$length / 2));
        } else {
            throw new Exception('No cryptographicall secure random function available');
        }

        return substr(bin2hex($bytes), 0, self::$length);
    }
}
