<?php
// src/Middleware/JwtMiddleware.php

namespace Elkha\ChatBackend\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Elkha\ChatBackend\Models\User;

class JwtMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $route = $request->getAttribute('route');

        // Define public routes that do not require authentication
        $publicRoutes = [
            'createUser',
            'listGroups',
            'listMessages',
        ];

        if ($route) {
            $routeName = $route->getName();
            if (in_array($routeName, $publicRoutes)) {
                return $handler->handle($request);
            }
        }

        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Authorization header required']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            $user = User::find($decoded->sub);
            if (!$user) {
                throw new \Exception('User not found');
            }
            // Add user to request attributes
            $request = $request->withAttribute('user', $user);
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}
