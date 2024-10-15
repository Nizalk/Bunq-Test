<?php
// src/middleware.php

use Slim\App;
use Elkha\ChatBackend\Middleware\JwtMiddleware;

return function (App $app) {
    // Add Body Parsing Middleware
    $app->addBodyParsingMiddleware();

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    // Add JWT Middleware
    $app->add(new JwtMiddleware());
};
