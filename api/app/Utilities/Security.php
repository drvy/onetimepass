<?php

namespace App\Utilities;

class Security
{
    /**
     * Generates a VALID RFC 4211 COMPLIANT Universally Unique IDentifier (UUID), version 4
     * https://www.rfc-editor.org/rfc/rfc4122#section-4.4
     *
     * @return string
     */
    public static function getUUIDv4(): string
    {
        $entropy = random_bytes(16);
        $entropy[6] = chr(ord($entropy[6]) & 0x0f | 0x40); // set version to 0100
        $entropy[8] = chr(ord($entropy[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($entropy), 4)));
    }


    /**
     * Generates a unique hash from certain input
     *
     * @param string $input
     * @return string
     */
    public static function generateHash(string $input): string
    {
        $hashSeed = sprintf('%s@%s@%s', $input, uniqid(), time());
        return hash('sha256', $hashSeed);
    }
}
