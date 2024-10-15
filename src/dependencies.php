<?php
// src/dependencies.php

use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

return function (ContainerInterface $container) {
    // Logger
    $container->set('logger', function () {
        $logger = new Logger('chat-app');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG));
        return $logger;
    });
};
