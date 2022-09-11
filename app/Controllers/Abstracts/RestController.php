<?php

namespace App\Controllers\Abstracts;

use DI\Container;
use Throwable;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;

abstract class RestController
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a json response
     *
     * @param Response $response
     * @param array $data
     * @param int $status
     * @return Response
     */
    public function respond(Response $response, array $data = array(), int $status = 200): Response
    {
        $data = array('success' => true, 'response' => $data);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }


    /**
     * Returns a basic json error with a status code and a message.
     *
     * @param int $status
     * @param string $message
     * @return Response
     */
    protected function returnError(int $status = 500, string $message = ''): Response
    {
        $payload = array(
            'success'   => false,
            'errorCode' => $status,
            'errorMsg'  => $message
        );

        $response = (new ResponseFactory)->createResponse($status);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     *
     * @param Throwable $error
     * @return Response
     */
    protected function returnException(Throwable $error)
    {
        $status = $error->getCode();
        if ($status < 400 || $status > 503) {
            $status = 500;
        }

        return $this->returnError($status, $error->getMessage());
    }


    /**
     * Returns a 403 Forbidden Response
     *
     * @return Response
     */
    protected function returnForbidden(): Response
    {
        return $this->returnError(403, 'Forbidden');
    }
}
