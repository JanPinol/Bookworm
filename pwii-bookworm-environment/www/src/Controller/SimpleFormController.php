<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;

final class SimpleFormController
{
    private UserRepository $userRepository;
    private Twig $twig;

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showForm(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'simple-form.twig', [
            'formAction' => $routeParser->urlFor("handle-form"),
            'formMethod' => "POST"
        ]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
    
            $errors = [];
            if (!empty($errors)) {
                return $this->twig->render($response, 'simple-form.twig', [
                    'errors' => $errors,
                    'formData' => $data
                ]);
            }
    
            $user = $this->userRepository->findByEmail($data['email']);
            if ($user !== null) {
                $errors['email'] = 'This email is already registered.';
                return $this->twig->render($response, 'simple-form.twig', [
                    'errors' => $errors,
                    'formData' => $data
                ]);
            }
            $username = strtok($data['email'], '@');
            $user = new User(
                $data['email'],
                $data['password'],
                new \DateTime(),
                new \DateTime(),
                $username,
                'default.jpg',
                null
            );
    
            $this->userRepository->save($user);
            $this->userRepository->update($user);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
    
            $_SESSION['user_id'] = $user->id();
    
            return $response->withHeader('Location', '/')->withStatus(302);
        }
    
        return $this->twig->render($response, 'simple-form.twig', [
            'errors' => [],
            'formData' => []
        ]);
    }
    public function showSignInForm(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        return $this->twig->render($response, 'sign-in.twig', []);
    }
    public function signIn(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $errors = [];
    
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'The email address is not valid.';
        }

        /*
        elseif (!str_ends_with($data['email'], '@salle.url.edu')) {
            $errors['email'] = 'Only emails from the domain @salle.url.edu are accepted.';
        }
        */
    
        if (strlen($data['password']) < 7) {
            $errors['password'] = 'The password must contain at least 7 characters.';
        } elseif (!preg_match('/[a-z]/', $data['password']) || !preg_match('/[A-Z]/', $data['password']) || !preg_match('/\d/', $data['password'])) {
            $errors['password'] = 'The password must contain both upper and lower case letters and numbers.';
        }
    
        if (!empty($errors)) {
            return $this->twig->render($response, 'sign-in.twig', ['errors' => $errors, 'formData' => $data]);
        }
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $user = $this->userRepository->findByEmailAndPassword($data['email'], $data['password']);
        if ($user === null) {
            $errors['email'] = 'User with this email address does not exist.';
            return $this->twig->render($response, 'sign-in.twig', ['errors' => $errors, 'formData' => $data]);
        }
    
        $_SESSION['user_id'] = $user->id();
        return $response->withHeader('Location', '/')->withStatus(302);
    }


}