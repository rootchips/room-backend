<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface {
    protected $role;

    public function __construct($role = null) {
        $this->role = $role;
    }

    public function process(Request $request, RequestHandler $handler): Response {
        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            if ($this->role && $decoded->role !== $this->role) {
                $response = new SlimResponse();
                $response->getBody()->write(json_encode(['error' => 'Forbidden']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }
            $request = $request->withAttribute('user_id', $decoded->id);
        } catch (Exception $e) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}