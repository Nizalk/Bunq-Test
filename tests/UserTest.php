<?php
// tests/UserTest.php

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use DI\Container;
use Elkha\ChatBackend\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        // Set up the application for testing
        $container = new Container();
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // Load environment variables
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // Register dependencies
        (require __DIR__ . '/../src/dependencies.php')($container);

        // Add Middleware
        (require __DIR__ . '/../src/middleware.php')($app);

        // Register routes
        (require __DIR__ . '/../src/routes.php')($app);

        $this->app = $app;
    }

    public function testCreateUser()
    {
        // Simulate a POST request to /users
        $request = $this->createRequest('POST', '/users', ['username' => 'testuser']);
        $response = $this->app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        var_dump($body); // Debugging line
        $this->assertArrayHasKey('token', $body);

        // Decode JWT to verify
        $decoded = JWT::decode($body['token'], new Key($_ENV['JWT_SECRET'], 'HS256'));
        $this->assertEquals('chat-app', $decoded->iss);
        $this->assertEquals('testuser', User::find($decoded->sub)->username);
    }

    protected function createRequest($method, $path, $data = null)
    {
        $requestFactory = new \Slim\Psr7\Factory\ServerRequestFactory();
        $uri = new \Slim\Psr7\Uri('', '', 80, $path);
        $headers = ['Content-Type' => 'application/json'];
        $stream = new \Slim\Psr7\Stream(fopen('php://temp', 'r+'));
        if ($data) {
            $stream->write(json_encode($data));
            $stream->rewind();
        }

        $request = $requestFactory->createServerRequest($method, $uri, []);
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        if ($data) {
            $request = $request->withBody($stream);
        }

        return $request;
    }
}
