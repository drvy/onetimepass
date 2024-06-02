<?php

namespace App\Utilities;

use Exception;
use Dotenv\Dotenv;

class Settings
{
    private array $settings = [];


    public function __construct()
    {
        $this->loadEnv();
    }


    /**
     * Retrieve a property from the settings array or throw an exception
     *
     * @param string|null $property
     * @return mixed
     *
     * @SuppressWarnings("CountInLoopExpression")
     */
    private function getOrThrow(string|null $property = null)
    {
        if (isset($this->settings[$property])) {
            return $this->settings[$property];
        }

        if (is_null($property)) {
            return $this->settings;
        }

        $keys = explode('.', $property);
        $settings = $this->settings;


        // While count is used correctly here. We want associative iteration until the last key
        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($settings[$key]) || !is_array($settings[$key])) {
                throw new Exception('Not found', 404);
            }

            $settings = &$settings[$key];
        }

        $settings = &$settings[array_shift($keys)];
        return $settings;
    }


    /**
     * Load environment variables from .env
     *
     * @return void
     */
    private function loadEnv(): void
    {
        try {
            $envs = Dotenv::createArrayBacked(ABSPATH)->safeLoad();

            if (!is_array($envs)) {
                return;
            }

            foreach ($envs as $key => $value) {
                if (stripos($key, 'APP_') === 0) {
                    $key = substr($key, 4);
                }

                $key = str_replace('_', '.', strtolower($key));
                $this->set($key, $value);
            }
        } catch (Exception) {
            return;
        }
    }


    /**
     * Retrieve a property from the settings array
     *
     * @param string|null $property
     * @return mixed
     */
    public function get(string|null $property = null, $default = null)
    {
        try {
            $value = $this->getOrThrow($property);
            return (is_null($value) ? $default : $value);
        } catch (Exception) {
            return $default;
        }
    }


    /**
     * Set a property in the settings array
     *
     * @param string|null $property
     * @param mixed $value
     * @return void
     *
     * @SuppressWarnings("CountInLoopExpression")
     */
    public function set(string|null $property, $value): void
    {
        $keys = explode('.', $property);
        $settings = &$this->settings;

        // While count is used correctly here. We want associative iteration until the last key
        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($settings[$key]) || !is_array($settings[$key])) {
                $settings[$key] = [];
            }

            $settings = &$settings[$key];
        }

        $settings[array_shift($keys)] = $value;
    }
}
