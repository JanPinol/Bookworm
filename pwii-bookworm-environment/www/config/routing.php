<?php

declare(strict_types=1);

global $app;

use Project\Bookworm\Controller\BookDetailsController;
use Project\Bookworm\Controller\CatalogueController;
use Project\Bookworm\Controller\HomeController;
use Project\Bookworm\Middleware\SessionMiddleware;
use Project\Bookworm\Controller\FlashController;
use Project\Bookworm\Controller\SimpleFormController;
use Project\Bookworm\Controller\ProfileController;
use Project\Bookworm\Controller\ForumController;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->add(SessionMiddleware::class);

$app->get('/', HomeController::class . ':apply')->setName('home');

$app->get('/sign-out', HomeController::class . ':signOut')->setName('sign-out');

$app->get('/sign-up', SimpleFormController::class . ':showForm');
$app->post('/sign-up', SimpleFormController::class . ':handleFormSubmission')->setName('handle-form');

$app->get('/sign-in', SimpleFormController::class . ':showSignInForm')->setName('show-sign-in');
$app->post('/sign-in', SimpleFormController::class . ':signIn')->setName('handle-sign-in');

$app->get('/profile', ProfileController::class . ':showProfile');
$app->post('/profile', ProfileController::class . ':updateProfile')->setName('update-profile');
$app->post('/profile/upload', SimpleFormController::class . ':uploadFileAction')->setName('upload_profile');

$app->get('/catalogue', CatalogueController::class . ':showCatalogue')->setName('catalogue');
$app->post('/catalogue', CatalogueController::class . ':handleFormBook')->setName('handle-form-book');
$app->get('/catalogue/{id}', BookDetailsController::class . ':showBookDetails')->setName('book-details');

$app->put('/catalogue/{id}/rate', BookDetailsController::class . ':updateRating');
$app->delete('/catalogue/{id}/rate', BookDetailsController::class . ':deleteRating');
$app->put('/catalogue/{id}/review', BookDetailsController::class . ':updateReview');
$app->delete('/catalogue/{id}/review', BookDetailsController::class . ':deleteReview');

$app->get('/api/forums', ForumController::class . ':getForumsJson');
$app->get('/api/forums/{id}', ForumController::class . ':getForumById');
$app->post('/api/forums', ForumController::class . ':createForum');
$app->delete('/api/forums/{id}', ForumController::class . ':deleteForum');
$app->get('/forums', function (Request $request, Response $response) {
    $controller = $this->get(ForumController::class);
    return $controller->showForums($request, $response);
});