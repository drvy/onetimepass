<?php

namespace App\Utilities;

class Validation
{
    public static array $lengths = array(
        'int'             => 11,
        'string'          => 255,
        'email'           => 320,
    );

    /**
     * Filters and sanitizes an email address
     *
     * @param string $email
     * @param int $size
     * @return string
     */
    public static function filterEmail(string $email, int $size = 0): string
    {
        $size  = ($size > 0 ? $size : self::$lengths['email']);
        $email = filter_var(trim(mb_substr($email, 0, $size)), FILTER_SANITIZE_EMAIL);

        return (empty($email) || !is_string($email) ? '' : $email);
    }

    /**
     * Filters a string
     *
     * @param string $string
     * @param int $size
     * @return string
     */
    public static function filterString(string $string, int $size = 0): string
    {
        $size = ($size > 0 ? $size : self::$lengths['name']);
        return htmlspecialchars(trim(mb_substr($string, 0, $size)), ENT_QUOTES, 'UTF-8');
    }


    /**
     * Escape a string
     *
     * @param string $string
     * @return string
     */
    public static function escString(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }


    /**
     * Filters and sanitizes an integer
     *
     * @param mixed $int
     * @param int $size
     * @return int
     */
    public static function filterInt($int, int $size = 0): int
    {
        $size = ($size > 0 ? $size : self::$lengths['int']);
        $int = trim(mb_substr($int, 0, $size));
        return intval($int);
    }


    /**
     * Validates an email address
     *
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }


    /**
     * Validates an string with a UUID V4 format
     *
     * @param string $uuid
     * @return bool
     */
    public static function validateUUIDV4(string $uuid): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }
}
