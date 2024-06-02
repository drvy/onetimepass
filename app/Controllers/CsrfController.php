<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\Abstracts\RestController as Contoller;

class CsrfController extends Contoller
{
    /**
     * Render the home page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function getTokens(Request $request, Response $response, array $args = array())
    {
        $csrf    = $this->container->get('csrf');
        $payload = array(
            $csrf->getTokenNameKey () => $csrf->getTokenName(),
            $csrf->getTokenValueKey() => $csrf->getTokenValue()
        );

        return $this->respond($response, $payload, 200);
    }
}
