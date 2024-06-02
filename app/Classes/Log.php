<?php

namespace App\Classes;

use Throwable;
use Psr\Container\ContainerInterface as Container;
use App\Models\Logs;

class Log
{
    private Container $container;
    public int $debugLevel = 0;
    public string $logPath = '';

    /**
     * Constructor
     *
     * @param Container $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $settings = $container->get('settings')->get('log', []);

        if (!empty($settings['debug']['level'])) {
            $this->debugLevel = (int) $settings['debug']['level'];
        }

        if (!empty($settings['path'])) {
            $this->logPath = $settings['path'];
        }

        unset($settings);
    }


    /**
     * Search and replace tags in message with real value
     *
     * @param string $message
     * @return string
     */
    private function filterTags(string $message): string
    {
        $tags = array(
            '@time' => date('Y-m-d H:i:s')
        );

        return strtr($message, $tags);
    }


    /**
     * Log messages to error_log or custom path
     *
     * @param string $message
     * @return void
     */
    private function errorLog(string $message): void
    {

        if (empty($this->logPath)) {
            error_log($message);
            return;
        }

        $message = (mb_substr($message, -1) === "\n" ? $message : $message . "\n");
        error_log($message, 3, $this->logPath);
    }


    /**
     * Log a message to the debug output that will be shown only if debug is enabled.
     *
     * @param string $message
     * @param mixed $params
     * @return void
     */
    public function debug(int $level, string $message, ...$params): void
    {
        if ($this->debugLevel && $level > $this->debugLevel) {
            return;
        }

        $prefix  = sprintf('%s DEBUG -> ', str_replace('\\', '_', __NAMESPACE__));
        $message = sprintf($prefix . $message, ...$params);

        $this->errorLog($message);
        unset($prefix, $message, $params);
    }


    /**
     * Log an exception as an error and optionally interrupt CLI execution
     *
     * @param Throwable $error
     * @return void
     */
    public function exception(Throwable $error): void
    {
        $template = 'Exception: %s %s: %s';

        $code = sprintf('(%d)', $error->getCode());
        if (method_exists($error, 'getShortCode')) {
            $code = sprintf('%s (%d)', $error->getShortCode(), $error->getCode());
        }

        // TODO: Make trace show only on specific debug level.
        $this->error($template, $code, $error->getMessage(), $error->getTraceAsString());
    }


    /**
     * Log sprintf error to PHP log and if in debug mode, print message to STDOUT.
     *
     * @param string $message
     * @param mixed $params
     * @return void
     */
    public function error(string $message, ...$params): void
    {
        $message = $this->filterTags($message);

        $prefix  = sprintf('%s -> ', str_replace('\\', '_', __NAMESPACE__));
        $message = sprintf($prefix . $message, ...$params);

        $this->errorLog($message);
        unset($prefix, $message, $params);
    }


    /**
     * Store an audit log in the database
     *
     * @param array<string,string> $message
     * @param string $user
     * @return void
     */
    public function auditLog(array $message, string $user = ''): void
    {
        try {
            $log = new Logs();
            $log->message = json_encode($message, JSON_THROW_ON_ERROR);
            $log->user    = (!empty($user) ? $user : null);
            $log->ip      = $this->container->get('ip');
            $log->save();
        } catch (Throwable $error) {
            $this->exception($error);
        }
    }


    /**
     * Log an audit message
     *
     * @param string $type
     * @param int|string $user
     * @param string $details
     * @return void
     */
    public function audit(string $type, int|string $user = '', string $details = ''): void
    {
        switch ($type) {
            case 'login':
                $message = sprintf('User %s logged in', $user);
                break;

            case 'failed_login':
                $message = sprintf('Failed login attempt for user %s', $user);
                break;

            case 'change':
                $message = sprintf('User %s changed %s', $user, $details);
                break;

            case 'reset':
                $message = sprintf('User %s has requested password reset', $user);
                break;

            default:
                $message = $type;
                break;
        }

        $this->auditLog(['type' => $type, 'message' => $message, 'details' => $details], $user);
    }
}
