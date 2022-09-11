<?php

namespace App\Controllers;

use Exception;
use Throwable;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Controllers\Abstracts\RestController as Controller;
use App\Utilities\Uuid as UUID;
use App\Utilities\Validation;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Render the home page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function createMessage(Request $request, Response $response, array $args = array())
    {
        try {
            $args = $request->getParsedBody();

            if (empty($args['secret']) || empty($args['token'])) {
                throw new Exception('Missing secret and/or token', 400);
            }

            if (!Validation::validateToken($args['token'])) {
                throw new Exception('Token does not have a valid format', 400);
            }

            if (!Validation::validateSecret($args['secret'])) {
                throw new Exception('Secret does not have a valid format', 400);
            }

            $args['hasFiles'] = (isset($args['hasFiles']) && (int) $args['hasFiles'] > 0 ? 1 : 0);

            $message = new Message();
            $message->uuid     = UUID::generate();
            $message->message  = $args['secret'];
            $message->token    = $args['token'];
            $message->hasFiles = $args['hasFiles'];
            $message->save();

            $payload = array(
                'id' => $message->uuid
            );

            return $this->respond($response, $payload, 200);
        } catch (Throwable $error) {
            return $this->returnException($error);
        }
    }


    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function getMessage(Request $request, Response $response, array $args = array())
    {
    }


    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function deleteMessage(Request $request, Response $response, array $args = array())
    {
    }
}
