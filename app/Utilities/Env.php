<?php

namespace App\Utilities;

class Env
{
    public static function get($name, $default = null)
    {
        if (isset($_ENV[$name])) {
            return $_ENV[$name];
        }

        if (!is_null(getenv($name))) {
            return getenv($name);
        }

        return $default;
    }
}
