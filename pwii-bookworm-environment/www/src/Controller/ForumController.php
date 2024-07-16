<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\ForumRepository;
use Project\Bookworm\Model\Forum;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\RedirectResponse;
use Slim\Views\Twig;
use Slim\Psr7\JsonResponse;

class ForumController
{
    private ForumRepository $forumRepository;
    private Twig $twig;

    public function __construct(ForumRepository $forumRepository, Twig $twig)
    {
        $this->forumRepository = $forumRepository;
        $this->twig = $twig;
    }


    public function getForum(int $id): JsonResponse
    {
        $forum = $this->forumRepository->findById($id);
        if ($forum === null) {
            return new JsonResponse(['error' => 'Forum not found'], 404);
        }
        return new JsonResponse($forum);
    }

    public function createForum(Request $request): Response
    {
        $parsedBody = $request->getParsedBody();
        $title = $parsedBody['title'] ?? null;
        $description = $parsedBody['description'] ?? null;

        $now = new \DateTime();
        $formattedNow = $now->format('Y-m-d H:i:s');
    $forum = new Forum(0, $title, $description, $formattedNow, $formattedNow);
    $this->forumRepository->save($forum);

    
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', '/forums');
    }

    public function deleteForum(int $id): JsonResponse
    {
        $forum = $this->forumRepository->findById($id);
        if ($forum === null) {
            return new JsonResponse(['error' => 'Forum not found'], 404);
        }
        $this->forumRepository->delete($forum);
        return new JsonResponse(null, 204);
    }

    public function showForums(Request $request, Response $response): Response
    {
        $authenticated = isset($_SESSION['user_id']);
        if (!$authenticated) {
            return $response->withHeader('Location', '/sign-in');
        }
    
        $forums = $this->forumRepository->findAll();
        return $this->twig->render($response, 'forum.twig', ['forums' => $forums]);
    }

    
public function getForumsJson(Request $request, Response $response): Response
{
    $authenticated = isset($_SESSION['user_id']);
    if (!$authenticated) {
        return $response->withHeader('Location', '/sign-in');
    }

    $forums = $this->forumRepository->findAll();

    $forumsArray = array_map(function ($forum) {
        return $forum->toArray();
    }, $forums);
    $json = json_encode($forumsArray);

    $response->getBody()->write($json);

    return $response->withHeader('Content-Type', 'application/json');
}
}
 