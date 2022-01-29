<?php

namespace App\Utilities;

class Env
{
    /**
     * Search for an environment variable with $_ENV and getenv.
     * Return default if not found.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $name, $default = null)
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
