<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Render the default response
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        unset($request);
        return $this->return($response, ['response' => 'Hello world'], 200);
    }
}
