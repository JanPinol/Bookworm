<?php

declare(strict_types=1);

use DI\Container;
use Project\Bookworm\Controller\BookDetailsController;
use Project\Bookworm\Controller\CatalogueController;
use Project\Bookworm\Controller\ForumController;
use Project\Bookworm\Model\BookRepository;
use Project\Bookworm\Model\Repository\MysqlBookRepository;
use Project\Bookworm\Model\UserRepository;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Project\Bookworm\Controller\HomeController;
use Project\Bookworm\Controller\SimpleFormController;
use Project\Bookworm\Controller\ProfileController;
use Project\Bookworm\Model\Repository\MySQLUserRepository;
use Project\Bookworm\Model\Repository\MysqlForumRepository;
use Project\Bookworm\Model\ForumRepository;
use Project\Bookworm\Model\Repository\PDOSingleton;
use Slim\Flash\Messages;
use Project\Bookworm\Model\Repository\MysqlReviewRepository;
use Project\Bookworm\Model\Repository\MysqlRatingRepository;
use Project\Bookworm\Model\ReviewRepository;
use Project\Bookworm\Model\RatingRepository;


$container = new Container();

$container->set('base_url', function () {
    return 'http://localhost:8080/';
});

$container->set('flash', function () {
    return new Messages();
});

$container->set('view', function (ContainerInterface $container) {
    $twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    $twig->getEnvironment()->addGlobal('base_url', $container->get('base_url'));
    return $twig;
});


$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        $_ENV['DB_CONNECTION'],
        $_ENV['DB_PORT'],
        $_ENV['DB_DATABASE']
    );
});

$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set(ReviewRepository::class, function (ContainerInterface $container) {
    $db = $container->get('db');
    return new MysqlReviewRepository($db);
});


$container->set(RatingRepository::class, function (ContainerInterface $container) {
    $db = $container->get('db');
    return new MysqlRatingRepository($db);
});

$container->set(SimpleFormController::class, function (Container $c) {
    $twig = $c->get('view');
    $userRepository = $c->get(UserRepository::class);
    return new SimpleFormController($twig, $userRepository);
});

$container->set(HomeController::class, function (Container $container) {
    $twig = $container->get('view');
    $userRepository = $container->get(UserRepository::class);
    return new HomeController($twig, $userRepository);
});


$container->set(ProfileController::class, function (Container $c) {
    $twig = $c->get('view');
    $userRepository = $c->get(UserRepository::class);
    return new ProfileController($twig, $userRepository);
});

$container->set(BookRepository::class, function (ContainerInterface $container) {
    return new MySQLBookRepository($container->get('db'));
});

$container->set(CatalogueController::class, function (Container $container) {
    $twig = $container->get('view');
    $bookRepository = $container->get(BookRepository::class);
    return new CatalogueController($twig, $bookRepository);
});

$container->set(BookDetailsController::class, function (ContainerInterface $container) {
    $twig = $container->get('view');
    $bookRepository = $container->get(BookRepository::class);
    $reviewRepository = $container->get(ReviewRepository::class);
    $ratingRepository = $container->get(RatingRepository::class);
    return new BookDetailsController($twig, $bookRepository, $reviewRepository, $ratingRepository);
});


$container->set(ForumRepository::class, function (ContainerInterface $container) {
    return new MysqlForumRepository($container->get('db'));
});

$container->set(ForumController::class, function (ContainerInterface $container) {
    $forumRepository = $container->get(ForumRepository::class);
    $twig = $container->get('view');
    return new ForumController($forumRepository,$twig );
});
