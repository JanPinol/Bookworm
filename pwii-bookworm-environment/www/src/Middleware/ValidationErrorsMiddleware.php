<?php
declare(strict_types=1);

namespace Project\Bookworm\Middleware;
use Slim\Flash\Messages as FlashMessages;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ValidationErrorsMiddleware {
    protected $flash;

    public function __construct(FlashMessages $flash) {
        $this->flash = $flash;
    }

    public function __invoke(Request $request, Response $response, $next) {
        $response = $next($request, $response);

        $messages = $this->flash->getMessages();

        $response = $this->passFlashMessagesToView($response, $messages);

        return $response;
    }

    protected function passFlashMessagesToView(Response $response, $messages) {
        $data = [];
        foreach ($messages as $field => $message) {
            $data[$field] = $message[0];
        }

        $response->getBody()->write(json_encode($data));

        return $response;
    }
}
