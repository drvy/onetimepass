<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\Abstracts\Controller;

class ViewController extends Controller
{
    /**
     * Render the home page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function index(Request $request, Response $response, array $args = array())
    {
        return $this->render($response, 'view.twig', [
            'appName' => $this->container->get('settings')['app']['name']
        ]);
    }
}
