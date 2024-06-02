<?php

namespace App\Classes;

use Throwable;
use DI\Container;
use Slim\Factory\ServerRequestCreatorFactory;
use App\Errors\AppException;

class Server
{
    private Container $container;
    private $request;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $this->request = $serverRequestCreator->createServerRequestFromGlobals();

        $this->container->set('ip', $this->getIP());
        $this->container->set('browser', $this->getBrowser());
    }


    /**
     * Return the IP address of the client
     *
     * @return string
     *
     * @SuppressWarnings("Superglobals")
     */
    public function getIP(): string
    {
        try {
            $ip = $this->request->getHeader('X-Alumni-IP');
            $ip = (isset($ip[0]) ? $ip[0] : null);

            if (!isset($ip) && isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new AppException('invalid_ip', 'Invalid IP address', 500);
            }

            return $ip;
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Return the browser of the client.
     * Max length 512, no more than 1 space between words (for security reasons)
     *
     * @return string
     *
     * @SuppressWarnings("Superglobals")
     */
    public function getBrowser(): string
    {
        $browser = $this->request->getHeader('X-Alumni-Browser');
        $browser = (isset($browser[0]) ? $browser[0] : null);

        if (!isset($browser) && isset($_SERVER['HTTP_USER_AGENT'])) {
            $browser = $_SERVER['HTTP_USER_AGENT'];
        }

        return (!empty($browser) ? preg_replace('/\s+/', ' ', trim(substr($browser, 0, 512))) : '');
    }
}
