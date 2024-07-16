<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

final class HomeController
{
    private Twig $twig;

    public function __construct(
        Twig $twig
    ) {
        $this->twig = $twig;
    }

    public function apply(Request $request, Response $response): Response
    {

        $authenticated = isset($_SESSION['user_id']);

        return $this->twig->render($response, 'home.twig', ['authenticated' => $authenticated]);
    }

    public function signOut(Request $request, Response $response): Response
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();

        return $response->withHeader('Location', '/');
    }

}