<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class HomeController extends Controller
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
        return $this->render($response, 'index.twig', [
            'appName' => $this->container->get('settings')['app']['name']
        ]);
    }
}
