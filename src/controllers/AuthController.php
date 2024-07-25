<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Illuminate\Database\Capsule\Manager as DB;

class AuthController {
    public function login(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        if (is_null($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid request body']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $user = DB::table('users')->where('username', $data['username'])->first();

        if (!$user || !password_verify($data['password'], $user->password)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = JWT::encode(['id' => $user->id, 'role' => $user->role], $_ENV['JWT_SECRET'], 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function register(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        if (is_null($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid request body']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Basic validation
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Username, email, and password are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Check if the username or email already exists
        if (DB::table('users')->where('username', $data['username'])->exists() || DB::table('users')->where('email', $data['email'])->exists()) {
            $response->getBody()->write(json_encode(['error' => 'Username or email already exists']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $id = DB::table('users')->insertGetId([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => 'user', // Default role is user
        ]);

        $response->getBody()->write(json_encode(['id' => $id]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}