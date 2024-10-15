<?php
// public/index.php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';


use Slim\Factory\AppFactory;
use DI\Container;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create Container
$container = new Container();

// Set container to create App with it
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register dependencies
(require __DIR__ . '/../src/dependencies.php')($container);

// Add Middleware
(require __DIR__ . '/../src/middleware.php')($app);

// Register routes
(require __DIR__ . '/../src/routes.php')($app);

// Run app
$app->run();
