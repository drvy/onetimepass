<?php

namespace App\Utilities;

class Env
{
    /**
     * Gets an environment variable from $_ENV or getenv.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     *
     * @SuppressWarnings("Superglobals")
     */
    public static function get(string $name, $default = null)
    {
        if (isset($_ENV[$name])) {
            return $_ENV[$name];
        }

        if (getenv($name) !== false) {
            return getenv($name);
        }

        return $default;
    }
}
