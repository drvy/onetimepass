<?php

namespace App\Controllers\Abstracts;

use Throwable;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;

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
     * Returns a json response
     *
     * @param Response $response
     * @param array $data
     * @param int $status
     * @return Response
     */
    public function return(Response $response, array $data = [], int $status = 200): Response
    {
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
    protected function returnError(int $status = 500, string $errorCode = '', string $message = ''): Response
    {
        $payload = array(
            'error'      => true,
            'errorCode'  => $errorCode,
            'errorMsg'   => $message
        );

        $response = ((new ResponseFactory())->createResponse($status));
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     * Returns an error response and if the exception code is in the 400>=503 range,
     * uses that status code.
     *
     * @param Throwable $error
     * @return Response
     */
    protected function returnException(Throwable $error)
    {
        $status = (int) $error->getCode();
        $this->container->get('log')->exception($error);

        if ($status < 400 || $status > 503) {
            $status = 500;
        }

        $code = (method_exists($error, 'getShortCode') ? $error->getShortCode() : 'unknown_error');
        return $this->returnError($status, $code, $error->getMessage());
    }


    /**
     * Returns a 403 Forbidden Response
     *
     * @return Response
     */
    protected function returnForbidden(): Response
    {
        return $this->returnError(403, 'forbidden', 'Forbidden');
    }
}
