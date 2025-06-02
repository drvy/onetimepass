<?php

namespace App\Abstracts\Models;

use Psr\Container\ContainerInterface as Container;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Slim\App;
use App\Classes\AppContainer;

abstract class Model extends EloquentModel
{
    /**
     * Utility class to return Slim\App instance if needed.
     *
     * @return App
     */
    public static function &getApp(): App
    {
        return (AppContainer::getInstance()->load());
    }


    /**
     * Utility class to return Container of Slim\App instance if needed
     *
     * @return Container
     */
    public static function getContainer(): Container
    {
        return (self::getApp()->getContainer());
    }


    /**
     * Begins a database transaction
     *
     * @return void
     */
    public static function beginTransaction(): void
    {
        self::getConnectionResolver()->connection()->beginTransaction();
    }


    /**
     * Commits a database transaction
     *
     * @return void
     */
    public static function commit(): void
    {
        self::getConnectionResolver()->connection()->commit();
    }


    /**
     * Rollbacks a database transaction
     *
     * @return void
     */
    public static function rollBack(): void
    {
        self::getConnectionResolver()->connection()->rollBack();
    }
}
