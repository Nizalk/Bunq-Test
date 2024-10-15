<?php
// src/routes.php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Elkha\ChatBackend\Models\User;
use Elkha\ChatBackend\Models\Group;
use Elkha\ChatBackend\Models\Message;
use Firebase\JWT\JWT;
use Respect\Validation\Validator as v;

return function (App $app) {
    // Route to create a user (public)
    $app->post('/users', function ($request, $response, $args) {
        $data = $request->getParsedBody();

        $username = $data['username'] ?? null;

        if (!$username || !v::stringType()->notEmpty()->validate($username)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid username']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Check if username exists
        if (User::where('username', $username)->exists()) {
            $response->getBody()->write(json_encode(['error' => 'Username already exists']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Create user
        $user = User::create([
            'username' => $username,
            'token' => bin2hex(random_bytes(16)), // For example purposes
        ]);

        // Generate JWT
        $payload = [
            'iss' => 'chat-app',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 1 day expiration
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        $response->getBody()->write(json_encode(['token' => $jwt]));
        return $response->withHeader('Content-Type', 'application/json');
    })->setName('createUser');

    // Group Routes (protected)
    $app->group('/groups', function (RouteCollectorProxy $group) {
        // Create a group
        $group->post('', function ($request, $response, $args) {
            $data = $request->getParsedBody();
            $name = $data['name'] ?? null;

            if (!$name || !v::stringType()->notEmpty()->validate($name)) {
                $response->getBody()->write(json_encode(['error' => 'Invalid group name']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (Group::where('name', $name)->exists()) {
                $response->getBody()->write(json_encode(['error' => 'Group name already exists']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $group = Group::create(['name' => $name]);

            $response->getBody()->write(json_encode(['group' => $group]));
            return $response->withHeader('Content-Type', 'application/json');
        })->setName('createGroup');

        // List all groups (public)
        $group->get('', function ($request, $response, $args) {
            $groups = Group::all();
            $response->getBody()->write(json_encode(['groups' => $groups]));
            return $response->withHeader('Content-Type', 'application/json');
        })->setName('listGroups');

        // Join a group
        $group->post('/{id}/join', function ($request, $response, $args) {
            $groupId = $args['id'];
            $user = $request->getAttribute('user');

            $group = Group::find($groupId);
            if (!$group) {
                $response->getBody()->write(json_encode(['error' => 'Group not found']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Since groups are public and no join table is required, acknowledge the join
            $response->getBody()->write(json_encode(['message' => "Joined group '{$group->name}' successfully."]));
            return $response->withHeader('Content-Type', 'application/json');
        })->setName('joinGroup');

        // Send a message to a group
        $group->post('/{id}/messages', function ($request, $response, $args) {
            $groupId = $args['id'];
            $user = $request->getAttribute('user');

            $group = Group::find($groupId);
            if (!$group) {
                $response->getBody()->write(json_encode(['error' => 'Group not found']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $data = $request->getParsedBody();
            $messageText = $data['message'] ?? null;

            if (!$messageText || !v::stringType()->notEmpty()->validate($messageText)) {
                $response->getBody()->write(json_encode(['error' => 'Invalid message']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $message = Message::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'message' => $messageText,
            ]);

            $response->getBody()->write(json_encode(['message' => $message]));
            return $response->withHeader('Content-Type', 'application/json');
        })->setName('sendMessage');

        // List all messages in a group (public)
        $group->get('/{id}/messages', function ($request, $response, $args) {
            $groupId = $args['id'];

            $group = Group::find($groupId);
            if (!$group) {
                $response->getBody()->write(json_encode(['error' => 'Group not found']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $messages = $group->messages()->with('user')->orderBy('created_at', 'asc')->get();

            $response->getBody()->write(json_encode(['messages' => $messages]));
            return $response->withHeader('Content-Type', 'application/json');
        })->setName('listMessages');
    });
};
