<?php

namespace App\Classes;

use Psr\Container\ContainerInterface as Container;
use Slim\App;
use App\Classes\Log;
use App\Utilities\Settings;

class AppContainer
{
    private static $instance;
    private App $app;

    /**
     * Singleton
     *
     * @return AppContainer
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Return the Slim\App instance as reference
     *
     * @return App
     */
    public function &load(): App
    {
        return $this->app;
    }


    /**
     * Set Slim\App instance for future use.
     *
     * @param App $app
     * @return void
     */
    public function set(App &$app): void
    {
        $this->app =& $app;
    }


    /**
     * Return the container of the App
     *
     * @return Container
     */
    public static function container(): Container
    {
        return self::getInstance()->load()->getContainer();
    }


    /**
     * Fast access to the logger class in the App container
     *
     * @return Log
     */
    public static function logger(): Log
    {
        return self::container()->get('log');
    }


    /**
     * Fast access to the settings array in the App container
     *
     * @return Settings
     */
    public static function settings(): Settings
    {
        return self::container()->get('settings');
    }
}
