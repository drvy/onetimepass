<?php

namespace App\Controllers\Abstracts;

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    protected $container;


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
