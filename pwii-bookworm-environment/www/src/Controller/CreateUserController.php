<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use DateTime;
use Exception;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class CreateUserController
{

    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }
   
    public function apply(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $user = new User(
                $data['email'] ?? '',
                $data['password'] ?? '',
                new DateTime(),
                new DateTime(),
                $data['username'] ?? '',
                'default.jpg' // Aquí se establece la imagen de perfil en 'default.jpg'
            );
    
            $this->userRepository->save($user);
        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }
    
        return $response->withStatus(201);
    }
}