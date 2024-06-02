<?php

namespace App\Controllers;

use DI\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    /**
     * The container instance.
     *
     * @var \Interop\Container\ContainerInterface
     */
    protected $container;


    /**
     * Set up controllers to have access to the container.
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * Render shortcut
     *
     * @param Response $response
     * @param string $view
     * @param array $args
     * @return Response
     */
    public function render(Response $response, string $view, array $args = array()): Response
    {
        return $this->container->get('view')->render($response, $view, $args);
    }
}
