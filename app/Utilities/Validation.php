<?php

namespace App\Utilities;

use Exception;
use Throwable;

class Validation
{
    /**
     *
     * @param string $secret
     * @return bool
     */
    public static function validateSecret(string $secret): bool
    {
        try {
            $object = json_decode($secret, true, 2, JSON_THROW_ON_ERROR);

            if (!is_array($object) || count($object) !== 3) {
                throw new Exception('Secret does not have a valid format', 400);
            }

            if (empty($object['ct']) || empty($object['iv']) || empty($object['s'])) {
                throw new Exception('Secret is missing ct, iv or s', 400);
            }

            if (!ctype_xdigit($object['iv']) || !ctype_xdigit($object['s'])) {
                throw new Exception('S or iv do not have a valid format', 400);
            }

            return true;
        } catch (Throwable $error) {
           return false;
        }
    }


    /**
     *
     * @param string $token
     * @return bool
     */
    public static function validateToken(string $token): bool
    {
        try {
            if (empty($token) || !ctype_xdigit($token)) {
                throw new Exception('Token is empty or does not have a valid format', 400);
            }

            return true;
        } catch (Throwable $error) {
            return false;
        }
    }
}
