<?php

declare(strict_types=1);

global $container;

use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Project\Bookworm\Middleware\SessionMiddleware;
use Symfony\Component\Dotenv\Dotenv;
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = new Dotenv();

$dotenv->load(__DIR__ . '/../.env');

require_once __DIR__ . '/../config/dependencies.php';

AppFactory::setContainer($container);


$app = AppFactory::create();
$app->add(new SessionMiddleware());

$app->add(TwigMiddleware::createFromContainer($app));
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

require_once __DIR__ . '/../config/routing.php';

$app->run();