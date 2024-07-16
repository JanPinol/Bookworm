<?php

namespace Project\Bookworm\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthSessionMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        return $handler->handle($request);
    }
}
